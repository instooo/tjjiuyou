<?php
/**
 * 用户注册统计
 * Created by DaiLinLin.
 * Date: 2017/1/5
 * Time: 14:03
 */
$stime=microtime(true);

define('API', true);
define('LOG_FILE', 'log_register.log');
require_once 'common.php';
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
$type = 70;
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
    $dele_oldData_sql = "DELETE FROM `mygame_tj_member` WHERE `date`={$t_date}";
    $home_conn->query($dele_oldData_sql);

    $update_flag_sql = "UPDATE `mygame_day_flag` SET `count`=`count`+1 WHERE `date` = {$t_date}  AND `type` ={$type}";
    $home_conn->query($update_flag_sql);
}else {
    //当天还未统计，则初始化
    $insert_flag_sql = "INSERT INTO `mygame_day_flag` (`date`, `count`, `type`) VALUES ({$t_date},1,{$type})";
    $home_conn->query($insert_flag_sql);
}
/**数据库记录操作end**/

$flag_find_sql = "SELECT * FROM `mygame_tj_member` WHERE  `date`={$t_date} LIMIT 1";
$flog = $home_conn->select($flag_find_sql);
if ($flog) {
    $arr['code'] = 2;
    $arr['msg'] = 'one day only execute once!';
    exit(json_encode($arr));
}

//获取当前时间段的注册人数,游戏,一级，二级媒体
$member_sql ="SELECT COUNT(uid) as register_num,gid,channel as sub_channels FROM mygame_member WHERE register_time BETWEEN {$t_date_start} AND {$t_date_end} GROUP BY gid,channel";
$mlog = $home_conn->select($member_sql);
if($mlog){
    $channelArr = array_column($mlog,'sub_channels');
    $channelArr = array_unique($channelArr);
    $channels = implode(',',$channelArr);
    $channelsql = "select id,pid from mygame_channel WHERE id IN ({$channels})";
    $totalchanne = $home_conn->select($channelsql);
    $totalchanne = array_column($totalchanne, 'pid','id');

    //数据入库
    $values = '';
    foreach($mlog as $key=>$val){
        $time = time();
        $str = implode(',',$val);
        if(!isset($totalchanne[$val['sub_channels']])){
            $totalchanne[$val['sub_channels']] = 0;
        }
        //日期,注册数,游戏,媒体,一级媒体,添加时间
        $values .= "({$t_date},{$str},{$totalchanne[$val['sub_channels']]},{$time}),";
    }
    unset($mlog); unset($totalchanne); //清内存
    $values = trim($values, ',');

    if($values){
        $insert_sql ="INSERT INTO `mygame_tj_member` (`date`, `register_num`, `gid`, `sub_channels`, `total_channels`,`addtime`) VALUES {$values}";
        $res = $home_conn->query($insert_sql);
    }
    $log = new Log();

    $etime=microtime(true);
    $content_log = "nowtime:".date('Y-m-d H:i:s',time())." spend_time：".($etime-$stime)."秒 tongji_date:".$t_date;
    $log->saveLog($content_log,LOG_FILE);
    exit(json_encode(array('code'=>1,'msg'=>'success')));
}else{
    exit(json_encode(array('code'=>3,'msg'=>'no data')));
}
