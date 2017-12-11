<?php
/**
 * 复制用户表为记录用户地区
 * Created by Ldan.
 * Date: 2017/12/11
 * Time: 14:03
 */
$stime=microtime(true);

define('API', true);
define('LOG_FILE', 'log_copy_member_area.log');
require_once 'common.php';
require_once 'lib/IP/IP.php';
$config = include('config.php');
$home_conn = new Model($config['DB_HOST']);

$dates = intval(trim(htmlspecialchars($_REQUEST['date'])));
if($dates){
    $t_date = $dates = (int)date('Ymd', strtotime($dates));          //获取传入昨日日期
    $t_date_start = strtotime($dates.'000000');//昨天开始时间
    $t_date_end = strtotime($dates.'235959');  //昨天结束时间
}else{
    $nowtime = time();                  //当前时间
    $date = (int)date('Ymd', $nowtime); //当日日期

    $t_date_time = $nowtime - 24*3600;  //昨日当前时间
    $t_date = (int)date('Ymd', $t_date_time); //昨日日期

    $t_date_start = strtotime($t_date.'000000');//昨天开始时间
    $t_date_end = strtotime($t_date.'235959');  //昨天结束时间
}

/**数据库记录操作start**/
$type = 9980;
//当天该数据是否已经统计过，防止重复
$flag_find_sql = "SELECT * FROM `mygame_day_flag` WHERE `date` = {$t_date} AND `type` ={$type}";
$flag = $home_conn->select($flag_find_sql);
if (false === $flag) {
    $arr['code'] = -3;
    $arr['msg'] = 'flag find is error!';
    exit(json_encode($arr));
}
if (count($flag) > 0) {
    //重复统计，删除原有数据
    $dele_oldData_sql = "DELETE FROM `mygame_member_area` WHERE `date`={$t_date}";
    $home_conn->query($dele_oldData_sql);

    $update_flag_sql = "UPDATE `mygame_day_flag` SET `count`=`count`+1 WHERE `date` = {$t_date}  AND `type` ={$type}";
    $home_conn->query($update_flag_sql);
}else {
    //当天还未统计，则初始化
    $insert_flag_sql = "INSERT INTO `mygame_day_flag` (`date`, `count`, `type`) VALUES ({$t_date},1,{$type})";
    $home_conn->query($insert_flag_sql);
}
/**数据库记录操作end**/

$flag_find_sql = "SELECT * FROM `mygame_member_area` WHERE  `date`={$t_date} LIMIT 1";
$flog = $home_conn->select($flag_find_sql);
if ($flog) {
    $arr['code'] = 2;
    $arr['msg'] = 'one day only execute once!';
    exit(json_encode($arr));
}

//复制当前表
$member_sql ="SELECT uid,gid,register_ip,channel,total_channel,register_time,os FROM mygame_member WHERE register_time BETWEEN {$t_date_start} AND {$t_date_end}";
$mlog = $home_conn->select($member_sql);
if($mlog){

    //数据入库
    $values = '';
    foreach($mlog as $key=>$val){
        $area = IP::find($val['register_ip']);
        unset($val['register_ip']);
        $os = $val['os'];
        unset($val['os']);
        //$str = implode(',',$val);
        //uid,gid,channel,register_time,os,(省份,城市,添加日期)
        $values .= "('{$val['uid']}',{$val['gid']},{$val['channel']},{$val['total_channel']},{$val['register_time']},'{$os}','{$area[1]}','{$area[2]}',{$t_date}),";
        if(($key>0&&$key%1000==0)||$key==count($mlog)-1){
            $values = trim($values, ',');

            if($values){
                $insert_sql ="INSERT INTO `mygame_member_area` (`uid`, `gid`, `channel`, `total_channel`, `register_time`, `os`,`province`,`city`,`date`) VALUES {$values}";
                $res = $home_conn->query($insert_sql);
            }
            $log = new Log();

            $etime=microtime(true);
            $content_log = "nowtime:".date('Y-m-d H:i:s',time())." spend_time：".($etime-$stime)."秒 tongji_date:".$t_date." 复制数据{$res}条,数据分割执行点:{$key}";
            $log->saveLog($content_log,LOG_FILE);
            unset($values);
        }
    }
    unset($mlog); //清内存
    exit(json_encode(array('code'=>1,'msg'=>'success')));
}else{
    exit(json_encode(array('code'=>3,'msg'=>'no data')));
}
