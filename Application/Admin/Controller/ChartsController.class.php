<?php
/**
 * 实时数据查询控制器
 * Created by qinfan qf19910623@gmail.com.
 * Date: 2017/1/4
 */
namespace Admin\Controller;
use Org\Util\Rbac;
use \Think\Controller;
use Think\Crypt\Driver\Think;

class ChartsController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 充值走势分析
     * */
    public function recharge_trend() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //处理接收的数据
        $gid = $_REQUEST['gid'];
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
		//接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*时间处理*/
        $map = array();
        $nowtime = time();
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime-7*24*3600);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        $map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
        //当选择游戏时
        if ($gid) {
            $spend_model = M('tj_pay_bygid');
            $map['gid'] = $_REQUEST['gid'];
            $this->assign('gid', $gid);
        }else {
            $spend_model = M('tj_pay_bysub');
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }

        if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}

		if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //查询数据
        $list = $spend_model
            ->field('sum(total_money) as sum_money, sum(spend_num) as num, sum(now_money) as now_money ,sum(dan_num) as dan_num,date')
            ->where($map)
            ->group('date')
            ->order('date asc')
            ->select();
        
        $result = array();
        foreach ($list as $v) {
            $result[$v['date']] = $v;
        }
        //遍历时间天数
        $day_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time) {
            $t = strtotime('+'.$i.' day', $start_time);
            $day_list[] = (int)date('Ymd',$t);
            $i++;
        }
        $klist = array();
        $k = 0;
        for ($i=0;$i<count($day_list);$i++) {
            if (isset($result[$day_list[$i]])) {
                $money_v[] = (float)$result[$day_list[$i]]['sum_money'];
                $now_money_v[] = (float)$result[$day_list[$i]]['now_money'];
                $num_v[] = (int)$result[$day_list[$i]]['num'];
                $dan_num_v[] = (int)$result[$day_list[$i]]['dan_num'];
            }elseif($day_list[$i]) {
                $money_v[] = 0;
                $now_money_v[] =0;
                $num_v[] = 0;
                $dan_num_v[] = 0;
            }
            $klist[] = date('m月d日', strtotime($day_list[$i]));
            $k++;
        }

        //数据展现
        $this->assign('num_v', json_encode($num_v));
        $this->assign('money_v', json_encode($money_v));
        $this->assign('dan_num_v', json_encode($dan_num_v));
        $this->assign('now_money_v', json_encode($now_money_v));
        
        $this->assign('klist', json_encode($klist));
        $this->assign('money_sum', array_sum($money_v));
        $this->assign('num_sum', array_sum($num_v));
        $this->assign('now_money_sum', array_sum($now_money_v));
        $this->assign('dan_num_sum', array_sum($dan_num_v));
        
        $this->assign('sub_channel', $rechannel); 
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    
    
     /**
     * 充值走势对比
     * */
    public function recharge_compare(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
		//接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*开始处理时间*/
        $map = array();
        $nowtime = time();
        $nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
        
        //日期2
        $end2 = $_REQUEST['end2']?$_REQUEST['end2']:date('Y-m-d', $nowtime-24*3600);
        $end_time2 = strtotime($end2.' 23:59:59');
        if ($end_time2 >= $nowtime_start) $end_time2 = $nowtime_start - 1;
        $start2 = $_REQUEST['start2']?$_REQUEST['start2']:date('Y-m-d', $end_time2-7*24*3600);
        $start_time2 = strtotime($start2.' 00:00:00');
        if ($end_time2 < $start_time2) exit('时间错误');
        $this->assign('start2', $start2);
        $this->assign('end2', $end2);
        
        
        //日期1
         if ($_REQUEST['end1']) {
            $end1 = $_REQUEST['end1'];
            $end_time1 = strtotime($end1.' 23:59:59');
        }else {
            $end1 = date('Y-m-d', $start_time2-1);
            $end_time1 = strtotime($end1.' 23:59:59');
        }

        if ($_REQUEST['start1']) {
            $start1 = $_REQUEST['start1'];
            $start_time1 = strtotime($start1.' 00:00:00');
        }else {
            $start1 = date('Y-m-d', $end_time1-($end_time2-$start_time2));
            $start_time1 = strtotime($start1.' 00:00:00');
        }

        $this->assign('start1', $start1);
        $this->assign('end1', $end1);
        /*结束处理时间*/
        
        if ($gid) {
            $spend_model = M('tj_pay_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_pay_bysub');
        }
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
        
        $map1 = $map;
        $map1['date'] = array('between', array(date('Ymd', $start_time1), date('Ymd', $end_time1)));

        $list1 = $spend_model
            ->field('sum(total_money) as sum_money, sum(spend_num) as num,sum(now_money) as now_money,sum(dan_num) as dan_num ,date')
            ->where($map1)
            ->group('date')
            ->order('date asc')
            ->select();

        $result1 = array();
        foreach ($list1 as $v) {
            $result1[$v['date']] = $v;
        }
        $map2 = $map;
        $map2['date'] = array('between', array(date('Ymd', $start_time2), date('Ymd', $end_time2)));
        $list2 = $spend_model
            ->field('sum(total_money) as sum_money, sum(spend_num) as num,sum(now_money) as now_money,sum(dan_num) as dan_num ,date')
            ->where($map2)
            ->group('date')
            ->order('date asc')
            ->select();
        
        $result2 = array();
        foreach ($list2 as $v) {
            $result2[$v['date']] = $v;
        }

        $day1_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time1) {
            $t = strtotime('+'.$i.' day', $start_time1);
            $day1_list[] = (int)date('Ymd',$t);
            $i++;
        }

        $day2_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time2) {
            $t = strtotime('+'.$i.' day', $start_time2);
            $day2_list[] = (int)date('Ymd',$t);
            $i++;
        }

        //取时间范围大的一个时间段做X轴
        $len = ( count($day1_list) > count($day2_list) )?count($day1_list):count($day2_list);

        $klist = array();
        $k = 0;
        for ($i=0;$i<$len;$i++) {
            if (isset($result1[$day1_list[$i]])) {
                $money_v1['data'][] = (float)$result1[$day1_list[$i]]['sum_money'];
                $num_v1['data'][] = (int)$result1[$day1_list[$i]]['num'];
                $now_money_v1['data'][] = (float)$result1[$day1_list[$i]]['now_money'];
                $dan_num_v1['data'][] = (int)$result1[$day1_list[$i]]['dan_num'];
            }elseif($day1_list[$i] && $day2_list[$i]) {
                $money_v1['data'][] = 0;
                $num_v1['data'][] = 0;
                $now_money_v1['data'][] = 0;
                $dan_num_v1['data'][] = 0;
            }

            if (isset($result2[$day2_list[$i]])) {
                $money_v2['data'][] = (float)$result2[$day2_list[$i]]['sum_money'];
                $num_v2['data'][] = (int)$result2[$day2_list[$i]]['num'];
                $now_money_v2['data'][] = (float)$result2[$day2_list[$i]]['now_money'];
                $dan_num_v2['data'][] = (int)$result2[$day2_list[$i]]['dan_num'];
            }elseif($day1_list[$i] && $day2_list[$i]) {
                $money_v2['data'][] = 0;
                $num_v2['data'][] = 0;
                $now_money_v2['data'][] = 0;
                $dan_num_v2['data'][] = 0;
            }
            $date1 = date('m月d日', strtotime($day1_list[$i]));
            $date2 = date('m月d日', strtotime($day2_list[$i]));
            $klist[] = $date1.'/'.$date2;
            $k++;
        }

        $money_v1['name'] = '日期1';
        $money_v2['name'] = '日期2';
        $money_v[] = $money_v1;
        $money_v[] = $money_v2;
        $now_money_v1['name'] = '日期1';
        $now_money_v2['name'] = '日期2';
        $now_money_v[] = $now_money_v1;
        $now_money_v[] = $now_money_v2;
        $num_v1['name'] = '日期1';
        $num_v2['name'] = '日期2';
        $num_v[] = $num_v1;
        $num_v[] = $num_v2;
        $dan_num_v1['name'] = '日期1';
        $dan_num_v2['name'] = '日期2';
        $dan_num_v[] = $dan_num_v1;
        $dan_num_v[] = $dan_num_v2;
        $this->assign('klist', json_encode($klist));
        $this->assign('money_v', json_encode($money_v));
        $this->assign('num_v', json_encode($num_v));
        $this->assign('now_money_v', json_encode($now_money_v));
        $this->assign('dan_num_v', json_encode($dan_num_v));
        $this->assign('subtitle_money', '日期1总额：￥'.array_sum($money_v1['data']).' 日期2总额：￥'.array_sum($money_v2['data']));
        $this->assign('subtitle_num', '日期1人数：'.array_sum($num_v1['data']).'人 日期2人数：'.array_sum($num_v2['data']).'人');
        $this->assign('subtitle_now_money', '日期1当前总额：￥'.array_sum($now_money_v1['data']).' 日期2当前总额：￥'.array_sum($now_money_v2['data']));
        $this->assign('subtitle_dan_num', '日期1当前人数：'.array_sum($dan_num_v1['data']).'人 日期2当前人数：'.array_sum($dan_num_v2['data']).'人');

        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }


    /**
     * 注册走势分析
     * */
    public function register_trend() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime-7*24*3600);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        $map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
       
        if ($gid) {
            $spend_model = M('tj_member');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_member');
        }

        if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //查询数据
        $list = $spend_model
            ->field('sum(register_num) as install_num,date')
            ->where($map)
            ->group('date')
            ->order('date asc')
            ->select();

        $result = array();
        foreach ($list as $v) {
            $result[$v['date']] = $v;
        }

        $day_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time) {
            $t = strtotime('+'.$i.' day', $start_time);
            $day_list[] = (int)date('Ymd',$t);
            $i++;
        }
        $klist = array();
        $k = 0;
        for ($i=0;$i<count($day_list);$i++) {
            if (isset($result[$day_list[$i]])) {
                $register_v[] = (float)$result[$day_list[$i]]['install_num'];

            }elseif($day_list[$i]) {
                $register_v[] = 0;

            }
            $klist[] = date('m月d日', strtotime($day_list[$i]));
            $k++;
        }
        $this->assign('register_v', json_encode($register_v));

        $this->assign('klist', json_encode($klist));
        $this->assign('register_sum', array_sum($register_v));
        
        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    
    
     /**
     * 注册走势对比
     * */
    public function register_compare(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
        //接收栏目
		$sub_channels = $_REQUEST['sub_channel'];

        /*处理时间*/
        $map = array();
        $nowtime = time();
        $nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
        $this->assign('nowtime', date('Y-m-d H:i:s', $nowtime));
        
        //日期2
        $end2 = $_REQUEST['end2']?$_REQUEST['end2']:date('Y-m-d', $nowtime-24*3600);
        $end_time2 = strtotime($end2.' 23:59:59');
        if ($end_time2 >= $nowtime_start) $end_time2 = $nowtime_start - 1;
        $start2 = $_REQUEST['start2']?$_REQUEST['start2']:date('Y-m-d', $end_time2-7*24*3600);
        $start_time2 = strtotime($start2.' 00:00:00');
        if ($end_time2 < $start_time2) exit('时间错误');
        $this->assign('start2', $start2);
        $this->assign('end2', $end2);
        
        
        //日期1
         if ($_REQUEST['end1']) {
            $end1 = $_REQUEST['end1'];
            $end_time1 = strtotime($end1.' 23:59:59');
        }else {
            $end1 = date('Y-m-d', $start_time2-1);
            $end_time1 = strtotime($end1.' 23:59:59');
        }

        if ($_REQUEST['start1']) {
            $start1 = $_REQUEST['start1'];
            $start_time1 = strtotime($start1.' 00:00:00');
        }else {
            $start1 = date('Y-m-d', $end_time1-($end_time2-$start_time2));
            $start_time1 = strtotime($start1.' 00:00:00');
        }

        $this->assign('start1', $start1);
        $this->assign('end1', $end1);
        
        $map = array();
        
        if ($gid) {
            $spend_model = M('tj_member');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_member');
        }

		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
        
        $map1 = $map;
        $map1['date'] = array('between', array(date('Ymd', $start_time1), date('Ymd', $end_time1)));
        $list1 = $spend_model
            ->field('sum(register_num) as install_num,date')
            ->where($map1)
            ->group('date')
            ->order('date asc')
            ->select();
        $result1 = array();
        foreach ($list1 as $v) {
            $result1[$v['date']] = $v;
        }
        $map2 = $map;
        $map2['date'] = array('between', array(date('Ymd', $start_time2), date('Ymd', $end_time2)));
        $list2 = $spend_model
            ->field('sum(register_num) as install_num,date')
            ->where($map2)
            ->group('date')
            ->order('date asc')
            ->select();
        
        
        $result2 = array();
        foreach ($list2 as $v) {
            $result2[$v['date']] = $v;
        }

        $day1_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time1) {
            $t = strtotime('+'.$i.' day', $start_time1);
            $day1_list[] = (int)date('Ymd',$t);
            $i++;
        }

        $day2_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time2) {
            $t = strtotime('+'.$i.' day', $start_time2);
            $day2_list[] = (int)date('Ymd',$t);
            $i++;
        }

        //取时间范围大的一个时间段做X轴
        $len = ( count($day1_list) > count($day2_list) )?count($day1_list):count($day2_list);

        $klist = array();
        $k = 0;
        for ($i=0;$i<$len;$i++) {
            if (isset($result1[$day1_list[$i]])) {
                $register_v1['data'][] = (float)$result1[$day1_list[$i]]['install_num'];

            }elseif($day1_list[$i] && $day2_list[$i]) {
                $register_v1['data'][] = 0;

            }

            if (isset($result2[$day2_list[$i]])) {
                $register_v2['data'][] = (float)$result2[$day2_list[$i]]['install_num'];

            }elseif($day1_list[$i] && $day2_list[$i]) {
                $register_v2['data'][] = 0;

            }
            $date1 = date('m月d日', strtotime($day1_list[$i]));
            $date2 = date('m月d日', strtotime($day2_list[$i]));
            $klist[] = $date1.'/'.$date2;
            $k++;
        }

        $register_v1['name'] = '日期1';
        $register_v2['name'] = '日期2';
        $register_v[] = $register_v1;
        $register_v[] = $register_v2;
       
        $this->assign('klist', json_encode($klist));
        $this->assign('register_v', json_encode($register_v));

        $this->assign('subtitle_register', '日期1总人数：'.array_sum($register_v1['data']).' 日期2总人数：'.array_sum($register_v2['data']));

        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    
    
    /**
     * 广告点击量查询
     * */
    public function click(){
        $channel = $this->getChannel();
        //接收数据
        $os = $_REQUEST['os'];
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];
        $start_time = strtotime($start.' 00:00:00');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        $this->assign('start', $start);

        //按操作系统查询
        if ($os) {
            $map['os'] = $os;
        }
		//当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr = M('channel')->field('name')->where(array('id'=>$sec_channels))->find();
            $channelArr[]= $channelArr['name'];
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
            $this->assign('sub_channel_val', $sub_channel_val);
            $this->assign('sec_channel_val', $sec_channels);           
             //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
            $this->assign('sec_channel', $seclist);
           
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array(); 
            $channelArr[] = $sub_channels;
            $seclist = M('channel')->where(array('pid'=>$sub_channels))->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'name'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
            $channelArra = array_column($channel['sub_channel'],'name');//顶级
            $channelArrb = array_column($channel['sec_channel'],'name');//子级
            $channelArr = array_merge($channelArra,$channelArrb);          
        }
        $map['cps_name'] = array('in', $channelArr);
        
        //统计历史部分
        $datelist = array();
        $t = $start_time;
        $datelist[] = (int)date('Ymd', $t);
        $map['date'] = array('in', $datelist);
        $list = array();
        //按查询时间或渠道条件查询
        if ($datelist && $map['cps_name']) {
            $list = M('tj_click')->field('sum(traffic) as num,h_date,date')
                ->where($map)
                ->group('h_date')
                ->order('h_date asc')
                ->select();
        }
        $result_list = array();
        foreach ($list as $val) {
            $result_total += $val['num']; 
            $result_list[$val['date']][(int)substr($val['h_date'], -2)] = (int)$val['num'];
        }

        //按条件显示
        $klist = range(0,23);
        $datalist = array();
        foreach ($result_list as $k=>$v) {
            $key = date('Y年m月d日', strtotime($k.'000000'));
            $temp = array();
            $temp['name'] = $key; 
            foreach ($klist as $kv) {  
                $temp['data'][] = $v[$kv]?$v[$kv]:0;
            }
            $datalist[] = $temp;
        }
        if(!$result_total){
            $result_total=0;
        }
        //显示前端页面
        $this->assign('result_total', json_encode($result_total));
        $this->assign('klist', json_encode($klist));
        $this->assign('datalist', json_encode($datalist));

        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);
        $this->display();
    }
    
    
    /**
     * 注册留存分析
     * */
    public function reg_liucun(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);        
		
        //接收数据
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        
        if ($gid) {
            $spend_model = M('tj_install_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_install_bygid');
        }
       
		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
        
        $list = M('tj_liucun')
            ->field('sum(liucun_num) as num,sum(reg_num) as all_num,date,day')
            ->where($map)
            ->group('date,day')
            ->order('date')
            ->select();
        
        
        $result = array();
        foreach ($list as $v) {
            $result[$v['date'].'|'.$v['day']] = $v;
        }

        $day_arr = array_unique(array_column($list, 'day'));
        sort($day_arr);

        $day_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time) {
            $t = strtotime('+'.$i.' day', $start_time);
            $day_list[] = (int)date('Ymd',$t);
            $i++;
        }
        $flag_data = array();
        for ($i=0;$i<count($day_list);$i++) {
            $date = date('Ymd', strtotime('+'.$i.' day',$start_time));
            foreach ($day_arr as $val) {
                $key = $date.'|'.$val;
                $temp = array();
                $temp['key'] = $date;
                if (isset($result[$key])) {
                    $percent = (int)$result[$key]['num']*100/(int)$result[$key]['all_num'];
                    $temp['percent'] = round($percent, 2);
                }else {
                    $temp['percent'] = 0;
                }
                $flag_data[$val][] = $temp;
            }
        }

        $klist = array();
        $data = array();
        foreach ($flag_data as $key=>$val) {
            $temp = array();
            $temp['name'] = $key.'日留存';
            $temp['data'] = array();
            foreach ($val as $k=>$v) {
                if (!in_array($v['key'], $klist)) $klist[] = date('m月d日', strtotime($v['key']));
                $temp['data'][] = $v['percent'];
            }
            $data[] = $temp;
        }

        $this->assign('klist', json_encode($klist));
        $this->assign('data', json_encode($data));

        $this->assign('gamelist', $gamelist);

        if ($_REQUEST['subsign1']) $this->assign('subsign1', $_REQUEST['subsign1']);
        $this->assign('subsign', $_REQUEST['subsign']);
        $this->assign('flag', $meminfo_cps['subsign']);

        $this->assign('sub_channel', $rechannel);
        $this->display();
    
    }

    /**
     * 安装走势分析
     * */
    public function down_trend() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime-7*24*3600);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        $map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));

        if ($gid) {
            $spend_model = M('tj_install_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_install_bygid');
        }

        if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //查询数据
        $list = $spend_model
            ->field('sum(install_num) as install_num,date')
            ->where($map)
            ->group('date')
            ->order('date asc')
            ->select();
        $result = array();
        foreach ($list as $v) {
            $result[$v['date']] = $v;
        }
        //天数查询
        $day_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time) {
            $t = strtotime('+'.$i.' day', $start_time);
            $day_list[] = (int)date('Ymd',$t);
            $i++;
        }
        $klist = array();
        $k = 0;
        for ($i=0;$i<count($day_list);$i++) {
            if (isset($result[$day_list[$i]])) {
                $register_v[] = (float)$result[$day_list[$i]]['install_num'];

            }elseif($day_list[$i]) {
                $register_v[] = 0;

            }
            $klist[] = date('m月d日', strtotime($day_list[$i]));
            $k++;
        }
        $this->assign('register_v', json_encode($register_v));

        $this->assign('klist', json_encode($klist));
        $this->assign('register_sum', array_sum($register_v));
        
		$this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    
    
     /**
     * 安装走势对比
     * */
    public function down_compare(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
        $this->assign('nowtime', date('Y-m-d H:i:s', $nowtime));
        
        //日期2
        $end2 = $_REQUEST['end2']?$_REQUEST['end2']:date('Y-m-d', $nowtime-24*3600);
        $end_time2 = strtotime($end2.' 23:59:59');
        if ($end_time2 >= $nowtime_start) $end_time2 = $nowtime_start - 1;
        $start2 = $_REQUEST['start2']?$_REQUEST['start2']:date('Y-m-d', $end_time2-7*24*3600);
        $start_time2 = strtotime($start2.' 00:00:00');
        if ($end_time2 < $start_time2) exit('时间错误');
        $this->assign('start2', $start2);
        $this->assign('end2', $end2);
      
        
        //日期1
         if ($_REQUEST['end1']) {
            $end1 = $_REQUEST['end1'];
            $end_time1 = strtotime($end1.' 23:59:59');
        }else {
            $end1 = date('Y-m-d', $start_time2-1);
            $end_time1 = strtotime($end1.' 23:59:59');
        }

        if ($_REQUEST['start1']) {
            $start1 = $_REQUEST['start1'];
            $start_time1 = strtotime($start1.' 00:00:00');
        }else {
            $start1 = date('Y-m-d', $end_time1-($end_time2-$start_time2));
            $start_time1 = strtotime($start1.' 00:00:00');
        }

        $this->assign('start1', $start1);
        $this->assign('end1', $end1);
        
		
        $map = array();
        
        if ($gid) {
            $spend_model = M('tj_install_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_install_bygid');
        }

        if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //日期1的数据查询
        $map1 = $map;
        $map1['date'] = array('between', array(date('Ymd', $start_time1), date('Ymd', $end_time1)));
        $list1 = $spend_model
            ->field('sum(install_num) as install_num,date')
            ->where($map1)
            ->group('date')
            ->order('date asc')
            ->select();
        $result1 = array();
        foreach ($list1 as $v) {
            $result1[$v['date']] = $v;
        }

        //日期2的数据查询
        $map2 = $map;
        $map2['date'] = array('between', array(date('Ymd', $start_time2), date('Ymd', $end_time2)));
        $list2 = $spend_model
            ->field('sum(install_num) as install_num,date')
            ->where($map2)
            ->group('date')
            ->order('date asc')
            ->select();
        $result2 = array();
        foreach ($list2 as $v) {
            $result2[$v['date']] = $v;
        }

        //日期1的天数查询
        $day1_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time1) {
            $t = strtotime('+'.$i.' day', $start_time1);
            $day1_list[] = (int)date('Ymd',$t);
            $i++;
        }
        //日期2的天数查询
        $day2_list = array();
        $i = 0;
        $t = 0;
        while ($t+24*3600 < $end_time2) {
            $t = strtotime('+'.$i.' day', $start_time2);
            $day2_list[] = (int)date('Ymd',$t);
            $i++;
        }

        //取时间范围大的一个时间段做X轴
        $len = ( count($day1_list) > count($day2_list) )?count($day1_list):count($day2_list);

        $klist = array();
        $k = 0;
        for ($i=0;$i<$len;$i++) {
            if (isset($result1[$day1_list[$i]])) {
                $register_v1['data'][] = (float)$result1[$day1_list[$i]]['install_num'];
            }elseif($day1_list[$i] && $day2_list[$i]) {
                $register_v1['data'][] = 0;
            }

            if (isset($result2[$day2_list[$i]])) {
                $register_v2['data'][] = (float)$result2[$day2_list[$i]]['install_num'];
            }elseif($day1_list[$i] && $day2_list[$i]) {
                $register_v2['data'][] = 0;
            }

            $date1 = date('m月d日', strtotime($day1_list[$i]));
            $date2 = date('m月d日', strtotime($day2_list[$i]));
            $klist[] = $date1.'/'.$date2;
            $k++;
        }

        $register_v1['name'] = '日期1';
        $register_v2['name'] = '日期2';
        $register_v[] = $register_v1;
        $register_v[] = $register_v2;

        $this->assign('klist', json_encode($klist));
        $this->assign('register_v', json_encode($register_v));

        $this->assign('subtitle_register', '日期1总人数：'.array_sum($register_v1['data']).' 日期2总人数：'.array_sum($register_v2['data']));

        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
	/**
	*注册人数地域分布图
	*/
    public function areaSpread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收条件
		$submit = $_REQUEST['submit'];
		$gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
		//接收时间
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
		$map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
		if ($_REQUEST['gid']) {
            $map['gid'] = $_REQUEST['gid'];
            $this->assign('gid', $_REQUEST['gid']);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
		//实例化
		$data = array();
		if($submit){
		$area_model = M('tj_member_byarea');
		$area_tj = $area_model
			->field('sum(register_num) as sum_num,area')
			->where($map)
			->group('area')
			->select();
		$count = 0;
		foreach($area_tj as $key => $val){
			$count += $val['sum_num'];
			$num1[$key] = $val['sum_num'];	
		}
		array_multisort($num1, SORT_DESC,$area_tj);
		$max = max(array_column($area_tj,'sum_num'));
		$i = 0;
		foreach($area_tj as $key=>$val){
			if($max == $val['sum_num'] && $i==0){
				$data[$key]['sliced'] = true;
				$data[$key]['selected'] = true;
				$i++;
			}
			$data[$key]['name'] = $val['area'].' '.$val['sum_num'].'人';
			$data[$key]['y'] = ($val['sum_num']/$count)*100;
		}
		$title = "注册人数地域分布分析图   (总人数：".$count."人)";
			$this->assign('title',$title);
		}
		$this->assign('data',json_encode($data));
		$this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
		$this->display();	
	}
	
	/**
    *注册人数时段饼状图
    */
    public function regHourSpread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收条件
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
        $submit = $_REQUEST['submit'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
		//接收时间
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        $map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
        if ($_REQUEST['gid']) {
            $map['gid'] = $_REQUEST['gid'];
            $this->assign('gid', $_REQUEST['gid']);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }
        
        if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
        //实例化
		$data = array();
		if($submit){
        $hour_model = M('tj_member_byhour');
        $hour_list = $hour_model
            ->field('substring(h_date,9) as hour,sum(register_num) as sum_num')
            ->where($map)
            ->group('hour')
            ->select();
        $count = 0;
        foreach($hour_list as $key=>$val){
            $count += $val['sum_num'];      
        }
        $max = max(array_column($hour_list,'sum_num'));
        $i = 0;
        foreach($hour_list as $key=>$val){
            if($max == $val['sum_num'] && $i==0){
                $data[$key]['sliced'] = true;
                $data[$key]['selected'] = true;
                $i++;
            }
            $data[$key]['name'] = (ltrim($val['hour'],0)==''?0:ltrim($val['hour'],0)).'点 '.$val['sum_num'].'人  ';
            $data[$key]['y'] = ($val['sum_num']/$count)*100;
        }
        $title = "注册人数时段分布分析图    (总人数：".$count."人)";
			$this->assign('title',$title);
		}
        $this->assign('data',json_encode($data));
        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    /**
	*充值金额地域占比图
	*/
	public function payAreaSpread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收参数
		$gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		$type = $_REQUEST['SearchType']; 
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
		
		$map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
		if ($gid) {
			//实例化
			$model = M('tj_pay_bygid_area');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else{
			//实例化
			$model = M('tj_pay_bysub_area');
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
			$this->assign('gid', $_REQUEST['gid']);			
		}
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){  //二级搜索
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
		
		if($type == 'money'){
			$area_tj = $model
			->field('sum(now_money) as total_money,area')
			->where($map)
			->group('area')
			->select();
			$money_sum = 0;
			foreach($area_tj as $key => $val){
				$money_sum += $val['total_money'];
				$num1[$key] = $val['total_money'];	
			}
			array_multisort($num1, SORT_DESC,$area_tj);
			$max = max(array_column($area_tj,'total_money'));
			$i = 0;
			foreach($area_tj as $key=>$val){
				if($max == $val['total_money'] && $i==0){
					$data[$key]['sliced'] = true;
					$data[$key]['selected'] = true;
					$i++;
				}
				$data[$key]['name'] = $val['area'].' '.$val['total_money'].'元';
				$data[$key]['y'] = ($val['total_money']/$money_sum)*100;
			}
			$title = "充值金额地域占比图"."(总金额：".$money_sum."元)";
		}elseif($type == 'num'){
			$area_tj = $model
			->field('sum(spend_num) as spend_num,area')
			->where($map)
			->group('area')
			->select();
			$num_sum = 0;
			foreach($area_tj as $key => $val){
				$num_sum += $val['spend_num'];
				$num1[$key] = $val['spend_num'];	
			}
			array_multisort($num1, SORT_DESC,$area_tj);
			$max = max(array_column($area_tj,'spend_num'));
			$i = 0;
			foreach($area_tj as $key=>$val){
				if($max == $val['spend_num'] && $i==0){
					$data[$key]['sliced'] = true;
					$data[$key]['selected'] = true;
					$i++;
				}
				$data[$key]['name'] = $val['area'].' '.$val['spend_num'].'人';
				$data[$key]['y'] = ($val['spend_num']/$num_sum)*100;
			}
			$title = "充值金额地域占比图"."(总充值人数：".$num_sum."人)";
		}
		$this->assign('data',json_encode($data));
        $this->assign('title',$title);
		$this->assign('type',$type);
        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();	
	}
	 /**
	*充值时段地域占比图
	*/
	public function payHourSpread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收条件
		$gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		$type = $_REQUEST['SearchType'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
		
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);
		$map['date'] = array('between', array(date('Ymd', $start_time), date('Ymd', $end_time)));
		
		if ($gid) {
			$model = M('tj_pay_bygid_hour');
			$pay_model = M('tj_pay_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else{
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
			$pay_model = M('tj_pay_bysub');
			$model = M('tj_pay_bysub_hour');
			$this->assign('gid', $gid);			
		}
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channels'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channels'] = array('in',$channel);
        }
		
		if($sec_search){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['sub_channels'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['sub_channels'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channels'] = array('in',$channelArr);
            }else{
                $map['total_channels'] = null;
            }
            $this->assign('type_name',$typename);
        }
		
		//实例化
		if($type == 'money'){
			$hour_list = $model
				->field('now_money,spend_num')
				->where($map)
				->select();
			//求总金额
			$money_sum = $pay_model->where($map)->sum('now_money');
			$data_arr = array();
			$data_sum = array();
			foreach($hour_list as $key=>$val){
				$data_arr = json_decode($val['now_money']);
				foreach($data_arr as $k => $v){
					$data_sum[(ltrim($k,0)==''?0:ltrim($k,0))]['money'] += $v; 
				}
			}
			ksort($data_sum);
			$max = max(array_column($data_sum,'money'));
			$i = 0;
			$j = 0;
			$data = array();
			foreach($data_sum as $key => $val){
				if($max == $val['money'] && $i==0){
					$data[$j]['sliced'] = true;
					$data[$j]['selected'] = true;
					$i++;
				}
				$data[$j]['name'] = $key.'点 '.$val['money'].'元 ';
				$data[$j]['y'] = ($val['money']/$money_sum)*100;
				$j++;
			}
			$title = "充值金额时段占比图  （总： ".$money_sum." 元）";
		}elseif($type == 'num'){
			$hour_list = $model
				->field('spend_num')
				->where($map)
				->select();
			$data_arr = array();
			$data_sum = array();
			foreach($hour_list as $key=>$val){
				$data_arr = json_decode($val['spend_num']);
				foreach($data_arr as $k => $v){
					$data_sum[(ltrim($k,0)==''?0:ltrim($k,0))]['num'] += $v; 
				}
			}
			ksort($data_sum);
			//查出总的充值人数
			$num_column = array_column($data_sum,'num');
			$num_sum = array_sum($num_column);
			$max = max(array_column($data_sum,'num'));
			$i = 0;
			$j = 0;
			$data = array();
			foreach($data_sum as $key => $val){
				if($max == $val['num'] && $i==0){
					$data[$j]['sliced'] = true;
					$data[$j]['selected'] = true;
					$i++;
				}
				$data[$j]['name'] = $key.'点 '.$val['num'].'人 ';
				$data[$j]['y'] = ($val['num']/$num_sum)*100;
				$j++;
			}
			$title = "充值人数时段占比图  （总： ".$num_sum." 人）";		
		}
		$this->assign('type',$type);
		$this->assign('data',json_encode($data));
        $this->assign('title',$title);
        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();	
	}
	/**
	*注册用户充值趋势分析图
	*/
    public function reg_pay_trend(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		//接收类型参数
		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收条件
		$gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];		
		//游戏条件
		if ($gid) {
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }
		if($sub_channels){//当选择一级栏目时候
			$map['total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channel'] = array('in',$channel);
        }
		
		if($sec_search){ //二级栏目搜索
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['channel'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['channel'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channel'] = array('in',$channelArr);
            }else{
                $map['total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }
		$date = $_GET['date'];
		$weeky = $_GET['weeky'];
		$monthy = $_GET['monthy'];
		//实例化
		$pay_model = M('pay_log');
		
		if($date){
			$regstart = $_REQUEST['regstart']." 00:00:00";
			$regend = $_REQUEST['regend']." 23:59:59";
			$paystart = $_REQUEST['paystart']." 00:00:00";
			$payend = $_REQUEST['payend']." 23:59:59";
			//处理时间参数
			$RegStart = strtotime($regstart);
			$RegEnd = strtotime($regend);
			$PayStart = strtotime($paystart);
			$PayEnd = strtotime($payend);

			$this->assign('rstart',$_REQUEST['regstart']);
			$this->assign('rend',$_REQUEST['regend']);
			$this->assign('pstart',$_REQUEST['paystart']);
			$this->assign('pend',$_REQUEST['payend']);
			$this->assign('date',$date);
			//时间条件
			$map['register_time'] = array('between',array($RegStart,$RegEnd));
			$map['pay_time'] = array('between',array($PayStart,$PayEnd));
			$pay_list = $pay_model->field('COUNT(DISTINCT(uid)) as num,sum(pay_money) as money_sum,FROM_UNIXTIME(pay_time,"%Y%m%d") as date')->where($map)->group('date')->select();
			
			
			//时间段
			$datelist = array();
			$timelist = $PayStart;
			for($PayStart;$timelist<$PayEnd;$timelist = $timelist + 24*60*60){
				$datelist[] = date('Ymd',$timelist);
			}
			
			$dtlist = array();
			$datalist = array();
			$money_sum = 0;
			if($pay_list){
				foreach($pay_list as $key => $val){
					$money_sum += $val['money_sum'];
					$datalist[0][$val['date']] = $val['money_sum'];		
					$datalist[1][$val['date']] = $val['num'];		
				}
				foreach($datelist as $key => $val){
					if($datalist[0][$val]){
						$dtlist[0]['data'][$key] = (double)$datalist[0][$val];
					}else{
						$dtlist[0]['data'][$key] = 0;
					}
					if($datalist[1][$val]){
						$dtlist[1]['data'][$key] = (double)$datalist[1][$val];
					}else{
						$dtlist[1]['data'][$key] = 0;
					}					
				}
				$dtlist[0]['name'] = '充值金额';
				$dtlist[0]['type'] = 'spline';
				$dtlist[0]['tooltip']['valueSuffix'] = ' 元';
				$dtlist[1]['name'] = '充值人数';	
				$dtlist[1]['type'] = 'spline';
				$dtlist[1]['tooltip']['valueSuffix'] = ' 人';
				$dtlist[1]['yAxis'] = 1;
				$this->assign('money_sum',$money_sum);
				$this->assign('datelist', json_encode($datelist));
				$this->assign('dtlist', json_encode($dtlist));
			}else{
				$this->assign('money_sum',$money_sum);
				$this->assign('datelist', json_encode($datelist));
				$this->assign('dtlist', json_encode($dtlist));
			}
		}elseif($weeky){
			$regstart = $_REQUEST['regstart']." 00:00:00";
			$regend = $_REQUEST['regend']." 23:59:59";
			$paystart = $_REQUEST['paystart']." 00:00:00";
			$payend = $_REQUEST['payend']." 23:59:59";
			//处理时间参数
			$RegStart = strtotime($regstart);
			$RegEnd = strtotime($regend);
			$PayStart = strtotime($paystart);
			$PayEnd = strtotime($payend);
			$this->assign('month',$month);
			$this->assign('rstart',$_REQUEST['regstart']);
			$this->assign('rend',$_REQUEST['regend']);
			$this->assign('pstart',$_REQUEST['paystart']);
			$this->assign('pend',$_REQUEST['payend']);
			$this->assign('week',$weeky);
			//时间条件
			$map['register_time'] = array('between',array($RegStart,$RegEnd));
			$map['pay_time'] = array('between',array($RegStart,$PayEnd));
			$pay_list = $pay_model->field('uid,pay_money,pay_time')->where($map)->order('pay_time asc')->select();
			$datelist = array();
			//时间范围
			$time = ceil(($PayEnd-$RegStart)/(7*24*3600));
			for($i=0;$i<$time;$i++){
				$datelist[$i] = '第'.($i+1).'周';
			}
			$datalist = array();
			$start_time = $RegStart;
			$flag = 1;
			$money_sum = 0;
			foreach($pay_list as $key=>$val){
				$money_sum += $val['pay_money'];
				if($start_time<$val['pay_time'] && $val['pay_time']<($start_time+7*24*3600)){
					$datalist[0][$flag]['money_sum'] += $val['pay_money'];
					$datalist[1][$flag][] = $val['uid']; 
				}else{
					$flag++;
					$start_time = $start_time+7*24*3600;
					$datalist[0][$flag]['money_sum'] += $val['pay_money'];
					$datalist[1][$flag][] = $val['uid']; 
				}
			}
			foreach($datalist[1] as $key=>$val){
				unset($datalist[1][$key]);
				$datalist[1][$key]['num'] = count(array_unique($val));
			}	
			foreach($datelist as $key=>$val){
				$num = $key+1;
				if($datalist[0][$num]){
					$dtlist[0]['data'][$key] = $datalist[0][$num]['money_sum'];
				}else{
					$dtlist[0]['data'][$key] = 0;
				}
				if($datalist[1][$num]){
					$dtlist[1]['data'][$key] = $datalist[1][$num]['num'];
				}else{
					$dtlist[1]['data'][$key] = 0;
				}				
			}
			$dtlist[0]['name'] = '充值金额';
			$dtlist[0]['type'] = 'spline';
			$dtlist[0]['tooltip']['valueSuffix'] = ' 元';
			$dtlist[1]['name'] = '充值人数';	
			$dtlist[1]['type'] = 'spline';
			$dtlist[1]['tooltip']['valueSuffix'] = ' 人';
			$dtlist[1]['yAxis'] = 1;
			
			$this->assign('money_sum',$money_sum);
			$this->assign('datelist', json_encode($datelist));
			$this->assign('dtlist', json_encode($dtlist));
		}elseif($monthy){
			$month = $_REQUEST['monthselect'];
			$this->assign('month',$month);
			//月开始
			$monthstart = strtotime($month);//指定月份月初时间戳 
			//月结束
			$monthend = mktime(23, 59, 59, date('m', strtotime($month))+1, 00);//指定月份月末时间戳
			//当前时间
			$nowtime = time();
			$map['register_time'] = array('between',array($monthstart,$monthend));
			$map['pay_time'] = array('between', array($monthstart,$nowtime));
			$pay_list = $pay_model->field('COUNT(DISTINCT(uid)) as num,sum(pay_money) as money_sum,FROM_UNIXTIME(pay_time,"%Y-%m") as month')->where($map)->group('month')->select();
			//时间范围
			$datelist = array();
			$today = date('Y-m',time());
			$todayarr = explode('-',$today);
			$montharr = explode('-',$month);
			$time = (($todayarr[0] - $montharr[0]) * 12 + ($todayarr[1] - $montharr[1]))+1;
			$yearstart = $montharr[0];
			$monthstart = ltrim($montharr[1],'0');
			for($i=0;$i<$time;$i++){
				if($monthstart>12){
					$monthstart = 1;
					$yearstart++;
					$datelist[$i] = $yearstart.'年'.$monthstart.'月';
				}else{
					$datelist[$i] = $yearstart.'年'.$monthstart.'月';	
					$monthstart++;
				}	
			}
			$money_sum = 0;
			$dtlist = array();
			$datalist = array();
			foreach($pay_list as $key=>$val){
				$strarr = explode('-',$val['month']);
				$str = $strarr[0].'年'.ltrim($strarr[1],'0').'月';
				$money_sum += $val['money_sum'];
				$datalist[0][$str] = $val['money_sum'];
				$datalist[1][$str] = $val['num'];
			}
			foreach($datelist as $key=>$val){
				if($datalist[0][$val]){
					$dtlist[0]['data'][$key] = (double)$datalist[0][$val];
				}else{
					$dtlist[0]['data'][$key] = 0;
				}
				if($datalist[1][$val]){
					$dtlist[1]['data'][$key] = (double)$datalist[1][$val];
				}else{
					$dtlist[1]['data'][$key] = 0;
				}				
			}
			$dtlist[0]['name'] = '充值金额';
			$dtlist[0]['type'] = 'spline';
			$dtlist[0]['tooltip']['valueSuffix'] = ' 元';
			$dtlist[1]['name'] = '充值人数';	
			$dtlist[1]['type'] = 'spline';
			$dtlist[1]['tooltip']['valueSuffix'] = ' 人';
			$dtlist[1]['yAxis'] = 1;
			
			$this->assign('money_sum',$money_sum);
			$this->assign('datelist', json_encode($datelist));
			$this->assign('dtlist', json_encode($dtlist));
		}else{
			$this->assign('money_sum',$money_sum);
			$this->assign('datelist', json_encode($datelist));
			$this->assign('dtlist', json_encode($dtlist));
		}
		$this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
		$this->display();
	}
	/**
	*注册用户充值时段占比图
	*/
	public function reg_pay_hourspread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        $typename = $_REQUEST['channeltype'];
		//接收类型参数
		$shistart = $_REQUEST['shistart'];
		$shiend = $_REQUEST['shiend'];
		$shiStart = strtotime($shistart);
		$shiEnd = strtotime($shiend);
		$hourstart = date('H:i',$shiStart);
		$hourend = date('H:i',$shiEnd);
		$hour_start = date('Hi',$shiStart);
		$hour_end = date('Hi',$shiEnd);
		$gid = $_REQUEST['gid'];
		$type = $_REQUEST['SearchType'];
		//游戏条件
		if ($gid) {
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }
		
		$regstart = $_REQUEST['regstart']." 00:00:00";
		$regend = $_REQUEST['regend']." 23:59:59";
		$paystart = $_REQUEST['paystart']." 00:00:00";
		$payend = $_REQUEST['payend']." 23:59:59";
		//处理时间参数
		$RegStart = strtotime($regstart);
		$RegEnd = strtotime($regend);
		$PayStart = strtotime($paystart);
		$PayEnd = strtotime($payend);

		$this->assign('rstart',$_REQUEST['regstart']);
		$this->assign('rend',$_REQUEST['regend']);
		$this->assign('pstart',$_REQUEST['paystart']);
		$this->assign('pend',$_REQUEST['payend']);
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channel'] = array('in',$channel);
        }
		
		if($sec_search){ //二级栏目搜索
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['channel'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['channel'] = null;
			}
		}
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channel'] = array('in',$channelArr);
            }else{
                $map['total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }
		
		$map['register_time'] = array('between',array($RegStart,$RegEnd));
		$map['pay_time'] = array('between',array($PayStart,$PayEnd));
		//实例化充值记录表
		$pay_model = M('pay_log');
		if($shiEnd == $shiStart)//查询24小时的金额或者分布
		{
		if($type == 'money'){
			$pay_list = $pay_model
				->field('FROM_UNIXTIME(pay_time,"%H") as hour,SUM(pay_money) as money_sum')
				->where($map)
				->group('hour')
				->select();
			foreach($pay_list as $key=>$value){
				$hour = ltrim($value['hour'],'0')?ltrim($value['hour'],'0'):0;
				$data_num[$hour] = $value['money_sum']; 	
			}
			//查出总的充值人数
			$money_column = array_column($pay_list,'money_sum');
			$money_sum = array_sum($money_column);
			$max = max(array_column($pay_list,'money_sum'));
			$i = 0;
			$j = 0;
			$data = array();
			foreach($pay_list as $key => $val){
				if($max == $val['money_sum'] && $i==0){
					$data[$j]['sliced'] = true;
					$data[$j]['selected'] = true;
					$i++;
				}
				$data[$j]['name'] = $key.'点 '.$val['money_sum'].'元 ';
				$data[$j]['y'] = ($val['money_sum']/$money_sum)*100;
				$j++;
			}
			$title = "注册用户充值金额时段占比图  （总： ".$money_sum." 元）";
		}elseif($type == 'num'){
				$model = M();
				$sql = $model
					->table('mygame_pay_log a')
					->field('FROM_UNIXTIME(a.pay_time,"%H") as hour,a.uid')
				->where($map)
					->order('pay_time asc')
					->group('uid')
					->select(false);
				$pay_list = $model
					->table('('.$sql.') z')
					->field('COUNT(uid) as num,hour')
				->group('hour')
				->select();
			foreach($pay_list as $key=>$value){
				$hour = ltrim($value['hour'],'0')?ltrim($value['hour'],'0'):0;
				$data_num[$hour] = $value['num']; 	
			}
			//查出总的充值人数
			$num_column = array_column($pay_list,'num');
			$num_sum = array_sum($num_column);
			$max = max(array_column($pay_list,'num'));
			$i = 0;
			$j = 0;
			$data = array();
			foreach($pay_list as $key => $val){
				if($max == $val['num'] && $i==0){
					$data[$j]['sliced'] = true;
					$data[$j]['selected'] = true;
					$i++;
				}
				$data[$j]['name'] = $key.'点 '.$val['num'].'人 ';
				$data[$j]['y'] = ($val['num']/$num_sum)*100;
				$j++;
			}
			$title = "注册用户充值人数时段占比图  （总： ".$num_sum." 人）";
			}
			$this->assign('start',$hourstart);
			$this->assign('end',$hourend);
		}else
		{
			if($shiStart > $shiEnd){
				$where['hour'] = array('between',array($hour_end,$hour_start));
				$this->assign('start',$hourend);
				$this->assign('end',$hourstart);
			}else{
				$where['hour'] = array('between',array($hour_start,$hour_end));
				$this->assign('start',$hourstart);
				$this->assign('end',$hourend);
			}
			if($type == 'money'){
				$model = M();
				$sql = $model
					->table('mygame_pay_log a')
					->field('FROM_UNIXTIME(a.pay_time,"%H%i") as hour,a.pay_money')
					->where($map)
					->select(false);
				$pay_list = $model
					->table('('.$sql.') z')
					->field('sum(pay_money) as sum_money')
					->where($where)
					->select();
				$money_sum = $pay_list[0]['sum_money'];
				//查出总的充值人数
				$data = array();
				foreach($pay_list as $key => $val){
					$data[0]['sliced'] = true;
					$data[0]['selected'] = true;
					$data[0]['name'] = $val['sum_money'].'元 ';
					$data[0]['y'] = 100;
				}
				$title = "注册用户充值金额时段占比图  （总： ".$money_sum." 元）";
			}elseif($type == 'num'){
				$model = M();
				$sql = $model
					->table('mygame_pay_log a')
					->field('a.uid,FROM_UNIXTIME(a.pay_time,"%H%i") as hour')
					->where($map)
					->order('a.pay_time asc')
					->group('uid')
					->select(false);
				$pay_list = $model
					->table('('.$sql.') z')
					->field('COUNT(uid) as num')
					->where($where)
					->select();
				$num_sum = $pay_list[0]['num'];
				//查出总的充值人数
				$data = array();
				foreach($pay_list as $key => $val){
					$data[0]['sliced'] = true;
					$data[0]['selected'] = true;
					$data[0]['name'] = $val['num'].'元 ';
					$data[0]['y'] = 100;
				}
				$title = "注册用户充值人数时段占比图  （总： ".$num_sum." 人）";	
			}
		}
		$this->assign('type',$type);
		$this->assign('data',json_encode($data));
        $this->assign('title',$title);
		$this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
		$this->display();
	}
	/**
	*注册用户充值地域占比图
	*/
	public function reg_pay_areaspread(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();

		//接收类型参数
		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		//接收栏目
		$sub_channels = $_REQUEST['sub_channel'];
        $typename = $_REQUEST['channeltype'];
		$gid = $_REQUEST['gid'];
		$type = $_REQUEST['SearchType'];
		//游戏条件
		if ($gid) {
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }
		$regstart = $_REQUEST['regstart']." 00:00:00";
		$regend = $_REQUEST['regend']." 23:59:59";
		$paystart = $_REQUEST['paystart']." 00:00:00";
		$payend = $_REQUEST['payend']." 23:59:59";
		//处理时间参数
		$RegStart = strtotime($regstart);
		$RegEnd = strtotime($regend);
		$PayStart = strtotime($paystart);
		$PayEnd = strtotime($payend);

		$this->assign('rstart',$_REQUEST['regstart']);
		$this->assign('rend',$_REQUEST['regend']);
		$this->assign('pstart',$_REQUEST['paystart']);
		$this->assign('pend',$_REQUEST['payend']);
		
		if($sub_channels){//当选择一级栏目时候
			$map['total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['total_channel'] = array('in',$channel);
        }
		
		if($sec_search){ //二级栏目搜索
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['channel'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['channel'] = null;
			}
		}

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channel'] = array('in',$channelArr);
            }else{
                $map['total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }
		
		$map['register_time'] = array('between',array($RegStart,$RegEnd));
		$map['pay_time'] = array('between',array($PayStart,$PayEnd));
		//实例化充值记录表
		$pay_model = M('pay_log_area');
		if($type == 'money'){
			$pay_list = $pay_model
				->field('sum(pay_money) as total_money,province')
				->where($map)
				->group('province')
				->select();
			$money_sum = 0;
			foreach($pay_list as $key => $val){
				$money_sum += $val['total_money'];
				$num1[$key] = $val['total_money'];	
			}
			array_multisort($num1, SORT_DESC,$pay_list);
			$max = max(array_column($pay_list,'total_money'));
			$i = 0;
			foreach($pay_list as $key=>$val){
				if($max == $val['total_money'] && $i==0){
					$data[$key]['sliced'] = true;
					$data[$key]['selected'] = true;
					$i++;
				}
				$data[$key]['name'] = $val['province'].' '.$val['total_money'].'元';
				$data[$key]['y'] = ($val['total_money']/$money_sum)*100;
			}
			$title = "充值金额地域占比图"."(总金额：".$money_sum."元)";

		}elseif($type == 'num'){
			$pay_list = $pay_model
				->field('COUNT(DISTINCT(uid)) as spend_num,province')
				->where($map)
				->group('province')
				->select();
			$num_sum = 0;
			foreach($pay_list as $key => $val){
				$num_sum += $val['spend_num'];
				$num1[$key] = $val['spend_num'];	
			}
			array_multisort($num1, SORT_DESC,$pay_list);
			$max = max(array_column($pay_list,'spend_num'));
			$i = 0;
			foreach($pay_list as $key=>$val){
				if($max == $val['spend_num'] && $i==0){
					$data[$key]['sliced'] = true;
					$data[$key]['selected'] = true;
					$i++;
				}
				$data[$key]['name'] = $val['province'].' '.$val['spend_num'].'人';
				$data[$key]['y'] = ($val['spend_num']/$num_sum)*100;
			}
			$title = "充值金额地域占比图"."(总充值人数：".$num_sum."人)";
		}
		$this->assign('type',$type);
		$this->assign('data',json_encode($data));
        $this->assign('title',$title);
		$this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
		$this->display();
	}

	/**
	 * LTV指标分析
	 * */
	public function ltv() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
        $channel =array_column($rechannel,'id');
        $sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
        $name = $this->isEscape($sec_search,true);
        //接收数据
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
        //接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*开始处理时间*/
        $map = array();
        $nowtime = time();
        $nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');

        //日期2
        $end2 = $_REQUEST['end2']?$_REQUEST['end2']:date('Y-m-d', $nowtime-24*3600);
        $end_time2 = strtotime($end2.' 23:59:59');
        if ($end_time2 >= $nowtime_start) $end_time2 = $nowtime_start - 1;
        $start2 = $_REQUEST['start2']?$_REQUEST['start2']:date('Y-m-d', $end_time2-24*3600);
        $start_time2 = strtotime($start2.' 00:00:00');
        if ($end_time2 < $start_time2) exit('时间错误');
        $this->assign('start2', $start2);
        $this->assign('end2', $end2);


        //日期1
        if ($_REQUEST['end1']) {
            $end1 = $_REQUEST['end1'];
            $end_time1 = strtotime($end1.' 23:59:59');
        }else {
            $end1 = date('Y-m-d', $start_time2-1);
            $end_time1 = strtotime($end1.' 23:59:59');
        }

        if ($_REQUEST['start1']) {
            $start1 = $_REQUEST['start1'];
            $start_time1 = strtotime($start1.' 00:00:00');
        }else {
            $start1 = date('Y-m-d', $end_time1-($end_time2-$start_time2));
            $start_time1 = strtotime($start1.' 00:00:00');
        }

        $this->assign('start1', $start1);
        $this->assign('end1', $end1);
        /*结束处理时间*/

        if ($gid) {
            $spend_model = M('tj_pay_bygid');
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
            $spend_model = M('tj_pay_bysub');
        }

        if($sub_channels){//当选择一级栏目时候
            $map['total_channel'] = $sub_channels;
            $this->assign('sub_channel_val',$sub_channels);
        }else{
            $map['total_channel'] = array('in',$channel);
        }

        if($sec_search){
            if($sub_channels){
                $condition['pid'] = $sub_channels;
            }
            $condition['name'] = array('like',"%".$name."%");
            $sec_list = M('channel')->field('id')->where($condition)->select();
            $this->assign('sec_search',$sec_search);
            if($sec_list){
                $map['channel'] = array('in',array_column($sec_list,'id'));
            }else{
                $map['channel'] = null;
            }
        }

        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channel'] = array('in',$channelArr);
            }else{
                $map['total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }
        $pay_log = M('pay_log');
        $member = M('member');

        //指标天数
        //$xlist = array(1,3,7,15,30,60);
        $xlist = range(1,60);

        /*第一个时间段start*/
        $daylist1 = array();
        $t = $start_time1;
        do{
            $daylist1[] = array(
                'start' =>  $t,
                'end' =>  $t + 24*3600 - 1,
            );
            $t = $t + 24*3600;
        }while($t < $end_time1);
        $ltv_value1 = array();
        unset($map['register_time']);
        unset($map['pay_time']);
        $map['register_time'] = array('between', array($start_time1, $end_time1));
        $reg_count1 = $member->where($map)->count();
        foreach ($xlist as $v) {
            $sum1 = 0;
            foreach ($daylist1 as $vv) {
                $map['register_time'] = array('between', array($vv['start'],$vv['end']));
                $map['pay_time'] = array('between', array($vv['start'],$vv['start']+$v*24*3600));
                $temp_sum = $pay_log->where($map)->sum('pay_money');
                $sum1 += $temp_sum;
            }
            $ltv_value1[] = round($sum1/$reg_count1, 2);
        }

        $daylist2 = array();
        $t = $start_time2;
        do{
            $daylist2[] = array(
                'start' =>  $t,
                'end' =>  $t + 24*3600 - 1,
            );
            $t = $t + 24*3600;
        }while($t < $end_time2);
        $ltv_value2 = array();
        unset($map['register_time']);
        unset($map['pay_time']);
        $map['register_time'] = array('between', array($start_time2, $end_time2));
        $reg_count2 = $member->where($map)->count();
        foreach ($xlist as $v) {
            $sum2 = 0;
            foreach ($daylist2 as $vv) {
                $map['register_time'] = array('between', array($vv['start'],$vv['end']));
                $map['pay_time'] = array('between', array($vv['start'],$vv['start']+$v*24*3600));
                $temp_sum = $pay_log->where($map)->sum('pay_money');
                $sum2 += $temp_sum;
            }
            $ltv_value2[] = round($sum2/$reg_count2, 2);
        }

        $data1['name'] = '日期1';
        $data1['data'] = $ltv_value1;
        $data2['name'] = '日期2';
        $data2['data'] = $ltv_value2;
        $data_value[] = $data1;
        $data_value[] = $data2;

        $this->assign('data_value', json_encode($data_value));
        $this->assign('xlist', json_encode($xlist));
        $this->assign('reg_count1', $reg_count1);
        $this->assign('reg_count2', $reg_count2);
        $this->assign('show_date1', "(".date('Y-m-d', $start_time1)."至".date('Y-m-d', $end_time1).")");
        $this->assign('show_date2', "(".date('Y-m-d', $start_time2)."至".date('Y-m-d', $end_time2).")");

        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }

    /**
     * SDK版本分析
     * */
    public function sdk() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
        $channel =array_column($rechannel,'id');
        $sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
        $name = $this->isEscape($sec_search,true);

        //接收数据
        $gid = $_REQUEST['gid'];
        $typename = $_REQUEST['channeltype'];
        //接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00');
        $end_time = strtotime($end.' 23:59:59');
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        $this->assign('start', $start);
        $this->assign('end', $end);

        if ($gid) {
            $map['gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['gid'] = array('in', array_column($gamelist, 'gid'));
        }

        if($sub_channels){//当选择一级栏目时候
            $map['total_channel'] = $sub_channels;
            $this->assign('sub_channel_val',$sub_channels);
        }else{
            $map['total_channel'] = array('in',$channel);
        }

        if($sec_search){
            if($sub_channels){
                $condition['pid'] = $sub_channels;
            }
            $condition['name'] = array('like',"%".$name."%");
            $sec_list = M('channel')->field('id')->where($condition)->select();
            $this->assign('sec_search',$sec_search);
            if($sec_list){
                $map['channel'] = array('in',array_column($sec_list,'id'));
            }else{
                $map['channel'] = null;
            }
        }
        if($typename && !$sec_search && !$sec_channel && !$sub_channels){
            $channelArr = array();
            foreach ($rechannel as $key => $val) {
                if($val['tid'] == $typename) {
                    $channelArr[] = $val['id'];
                }
            }
            if($channelArr){
                $map['total_channel'] = array('in',$channelArr);
            }else{
                $map['total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }

        $sdk_log = M('sdk_log');
        $sdk_counts = $sdk_log->where($map)->count();
        //7477SDK版本分布
        $sdk_7477 = $sdk_log->field('count("id") as num,sdk_vercode,sdk_vername')->where($map)->group('sdk_vercode')->select();
        $sdk_7477_data = array();
        foreach ($sdk_7477 as $v) {
            $temp = array();
            $temp[] = $v['sdk_vername'].'ver'.$v['sdk_vercode'];
            $temp[]  = round($v['num']*100/$sdk_counts,2);
            $sdk_7477_data[] = $temp;
        }
        //CPSSDK版本分布
        $sdk_cps = $sdk_log->field('count("id") as num,cpssdk_vercode,cpssdk_vername')->where($map)->group('cpssdk_vercode')->select();
        $sdk_cps_data = array();
        foreach ($sdk_cps as $v) {
            $temp = array();
            $temp[] = $v['cpssdk_vername'].' ver'.$v['cpssdk_vercode'];
            $temp[]  = round($v['num']*100/$sdk_counts,2);
            $sdk_cps_data[] = $temp;
        }

        $this->assign('sdk_7477_data', json_encode($sdk_7477_data));
        $this->assign('sdk_cps_data', json_encode($sdk_cps_data));

        $this->assign('sub_channel', $rechannel);
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
}