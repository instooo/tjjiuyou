<?php
/**
 * 删除三天前的广告点击统计
 * Created by DaiLinLin.
 * Date: 2017/1/5
 * Time: 14:03
 */
$stime=microtime(true);

define('API', true);
define('LOG_FILE', 'log_delete_click.log');
require_once 'common.php';
$config = include('config.php');
$home_conn = new Model($config['DB_HOST']);

$nowtime = time();                  //当前时间
$before_three_date = $nowtime - 3*24*3600;  //3天前
$three_date =(int)date('Ymd', $before_three_date); //3天前
$three_date_start = strtotime($three_date.'000000');//3天前 开始

$flag_find_sql = "SELECT * FROM `mygame_click_log` WHERE  `addtime`<{$three_date_start} LIMIT 1";

$flog = $home_conn->select($flag_find_sql);

if ($flog) {
    $delete_sql = "DELETE FROM `mygame_click_log` WHERE `addtime`<{$three_date_start}";

    $delete = $home_conn->query($delete_sql);

    $log = new Log();
    $etime=microtime(true);
    $content_log = "nowtime:".date('Y-m-d H:i:s',time())." spend_time：".($etime-$stime)."秒 删除".$three_date."之前数据:".$delete."条";
    $log->saveLog($content_log,LOG_FILE);
    exit(json_encode(array('code'=>1,'msg'=>'success')));
}else{
    $arr['code'] = 2;
    $arr['msg'] = 'no data  three days before!';
    exit(json_encode($arr));
}

