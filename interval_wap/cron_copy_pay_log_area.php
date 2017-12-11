<?php
/**
 * 复制支付日志为记录支付地区日志
 * Created by DaiLinLin.
 * Date: 2017/2/27
 * Time: 9:23
 */
$stime=microtime(true);

define('API', true);
define('LOG_FILE', 'log_copy_pay_log_area.log');
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
$type = 99110;
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
    $dele_oldData_sql = "DELETE FROM `mygame_pay_log_area` WHERE `date`={$t_date}";
    $home_conn->query($dele_oldData_sql);

    $update_flag_sql = "UPDATE `mygame_day_flag` SET `count`=`count`+1 WHERE `date` = {$t_date}  AND `type` ={$type}";
    $home_conn->query($update_flag_sql);
}else {
    //当天还未统计，则初始化
    $insert_flag_sql = "INSERT INTO `mygame_day_flag` (`date`, `count`, `type`) VALUES ({$t_date},1,{$type})";
    $home_conn->query($insert_flag_sql);
}
/**数据库记录操作end**/

$flag_find_sql = "SELECT * FROM `mygame_pay_log_area` WHERE  `date`={$t_date} LIMIT 1";
$flog = $home_conn->select($flag_find_sql);
if ($flog) {
    $arr['code'] = 2;
    $arr['msg'] = 'one day only execute once!';
    exit(json_encode($arr));
}

//复制当前表
$member_sql ="SELECT uid,gid,pay_money,channel,total_channel,pay_time,register_time,ip FROM mygame_pay_log WHERE pay_time BETWEEN {$t_date_start} AND {$t_date_end}";
$mlog = $home_conn->select($member_sql);
if($mlog){

    //数据入库
    $values = '';
    foreach($mlog as $key=>$val){
        $area = IP::find($val['ip']);
        unset($val['ip']);

        $str = implode(',',$val);
        //uid,gid,pay_money,channel,pay_time,(省份,城市,添加日期)
        $values .= "({$str},'{$area[1]}','{$area[2]}',{$t_date}),";
    }
    unset($mlog); //清内存
    $values = trim($values, ',');

    if($values){
        $insert_sql ="INSERT INTO `mygame_pay_log_area` (`uid`, `gid`,`pay_money`,`channel`,`total_channel`, `pay_time`,`register_time`,`province`,`city`,`date`) VALUES {$values}";
        $res = $home_conn->query($insert_sql);
    }
    $log = new Log();

    $etime=microtime(true);
    $content_log = "nowtime:".date('Y-m-d H:i:s',time())." spend_time：".($etime-$stime)."秒 tongji_date:".$t_date." 复制数据{$res}条";
    $log->saveLog($content_log,LOG_FILE);
    exit(json_encode(array('code'=>1,'msg'=>'success')));
}else{
    exit(json_encode(array('code'=>3,'msg'=>'no data')));
}
