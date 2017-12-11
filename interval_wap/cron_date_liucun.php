<?php
/**
 * Created by DaiLinLin.
 * User: 统计用户存留
 * Date: 2017/1/9
 * Time: 14:03
 */
$stime=microtime(true);

define('API', true);
define('LOG_FILE', 'log_liucun.log');
require_once 'common.php';
$config = include('config.php');
$home_conn = new Model($config['DB_HOST']);

$d = intval(trim(htmlspecialchars($_REQUEST['d'])));
$dates = intval(trim(htmlspecialchars($_REQUEST['date'])));
if (!$d) {
    $arr['code'] = 4;
    $arr['msg'] = 'params error';
    exit(json_encode($arr));
}

if($dates){
    $liucun_date = (int)date('Ymd', strtotime($dates)-($d-1)*24*60*60);
    $t_date = $dates = (int)date('Ymd', strtotime($dates));          //获取传入昨日日期
    $t_date_start = strtotime($dates.'000000')-($d-1)*24*60*60;//昨天开始时间
    $t_date_end = strtotime($dates.'235959')-($d-1)*24*60*60;  //昨天结束时间

    $liucun_day_start = strtotime($t_date.' 000000');
}else{
    $nowtime = time();      //当前时间
    $date = (int)date('Ymd', $nowtime); //当日日期

    $t_date_time = $nowtime - 24*3600;  //昨日当前时间
    $liucun_date = (int)date('Ymd', $t_date_time-($d-1)*24*60*60);
    $t_date = (int)date('Ymd', $t_date_time); //昨日日期

    $t_date_start = strtotime($t_date.'000000')-($d-1)*24*60*60;//昨天开始时间
    $t_date_end = strtotime($t_date.'235959')-($d-1)*24*60*60;  //昨天结束时间

    $liucun_day_start = strtotime($date.' 000000');
}

/**数据库记录操作start**/
$type = 40;
$type = $type+$d;
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
    $dele_oldData_sql = "DELETE FROM `mygame_tj_liucun` WHERE `date`={$t_date} AND `day`={$d}";
    $home_conn->query($dele_oldData_sql);

    $update_flag_sql = "UPDATE `mygame_day_flag` SET `count`=`count`+1 WHERE `date` = {$t_date}  AND `type` ={$type}";
    $home_conn->query($update_flag_sql);
}else {
    //当天还未统计，则初始化
    $insert_flag_sql = "INSERT INTO `mygame_day_flag` (`date`, `count`, `type`) VALUES ({$t_date},1,{$type})";
    $home_conn->query($insert_flag_sql);
}
/**数据库记录操作end**/

$flag_find_sql = "SELECT * FROM `mygame_tj_liucun` WHERE  `date`={$t_date} AND day={$d} LIMIT 1";
$flog = $home_conn->select($flag_find_sql);
if ($flog) {
    $arr['code'] = 2;
    $arr['msg'] = 'one day only execute once!';
    exit(json_encode($arr));
}


//查询n天前注册用户
$reg_sql = 'SELECT  m.uid,m.register_time,m.channel,m.total_channel,m.gid,MAX(l.login_time) as login_time
            FROM mygame_member m
            LEFT JOIN mygame_login_log l ON l.uid = m.uid
            WHERE ( m.register_time BETWEEN '.$t_date_start.' AND '.$t_date_end.' ) GROUP BY m.uid';
$all_list = $home_conn->select($reg_sql);
if (!$all_list) {
    $arr['code'] = -4;
    $arr['msg'] = '暂无数据';
    exit(json_encode($arr));
}

//数据分组
$group_list = array();
foreach ($all_list as $key=>$val) {
    $group = $val['gid'].'-'.$val['channel'];
    $group_arr = array_keys($group_list);
    if (!in_array($group, $group_arr)) {
        $group_list[$group]['all_num'] = 1;
    }else {
        $group_list[$group]['all_num']++;
    }
    $group_list[$group]['date'] = date('Ymd', $val['register_time']);
    $reg_end = strtotime(date('Y-m-d', $val['register_time']).' 23:59:59');
    if ($val['login_time'] > $liucun_day_start) {
        if (isset($group_list[$group]['num'])) {
            $group_list[$group]['num']++;
        }else {
            $group_list[$group]['num'] = 1;
        }
    }
}
unset($all_list);
//插入的数据
$nowtime = time();
$insert_values_arr = array();
foreach ($group_list as $k=>$v) {
    $num = isset($v['num'])?$v['num']:0;
    $val_arr = explode('-', $k);
    $insert_values_arr[] = "({$d},{$v['date']},{$v['all_num']},{$num},{$val_arr[0]},{$val_arr[1]},{$nowtime})";
}
$values = implode(',', $insert_values_arr);
unset($group_list);

if ($insert_values_arr) {
    $type = 20+$d;
    $flag_sql = "INSERT INTO `mygame_tj_liucun` (`day`,`date`,`reg_num`,`liucun_num`,`gid`,`channel`,`addtime`) VALUES {$values}";
    $home_conn->query($flag_sql);
}
$log = new Log();

$etime=microtime(true);
$content_log = "nowtime:".date('Y-m-d H:i:s',time())." spend_time：".($etime-$stime)."秒 tongji_date:".$t_date." 留存".$d."数据";
$log->saveLog($content_log,LOG_FILE);
exit(json_encode(array('code'=>1,'msg'=>'success')));
