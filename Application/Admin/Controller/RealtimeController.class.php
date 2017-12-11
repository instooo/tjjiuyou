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

class RealtimeController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 实时充值
     * */
    public function recharge(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();
        //获取栏目
		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$c_keyword = htmlspecialchars(trim($_REQUEST['c_keyword']));
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //定义查询条件数组$map
        $map = array();
        //接收排序条件
        $order = $_REQUEST['order']?$_REQUEST['order']:'pay_time';
        $order = 'p.'.$order;
        $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
        //接收游戏
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];

		//接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*对时间的处理*/
        $nowtime = time();
        $start = $_REQUEST['start'];
        $end = $_REQUEST['end'];
        $start_time = strtotime($start.' 00:00:00'); //充值开始时间
        $end_time = strtotime($end.' 23:59:59');     //充值结束时间
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
        $map['p.pay_time'] = array('between', array($start_time, $end_time));
        //当选择游戏时查询的条件
        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['p.gid'] = array('in', array_column($gamelist, 'gid'));
        }
        $uid = trim($_REQUEST['uid']);
        if ($uid) {
            $map['p.uid'] = $uid;
            $this->assign('uid', $uid);
        }
        $device_id = trim($_REQUEST['device_id']);
        if ($device_id) {
            $map['p.device_id'] = $device_id;
            $this->assign('device_id', $device_id);
        }
		
		
		if($c_keyword){
			$sec_channel = M('channel')->field('id')->where('name="'.$c_keyword.'"')->find();
			$this->assign('c_keyword',$c_keyword);
		}
		//当选择二级栏目时候
		if($sec_channel){
			$map['p.channel'] = $sec_channel['id'];
		}elseif($sub_channels){//当选择一级栏目时候
			$map['p.total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['p.total_channel'] = array('in',$channel);
        }
		
		
		if($sec_search && !$sec_channel){
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
                $map['p.total_channel'] = array('in',$channelArr);
            }else{
                $map['p.total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }
		
		
        //查询合计金额
        $sum_money = M('pay_log p')
            ->field('p.uid')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->where($map)
            ->sum('pay_money');
		//查询充值人数
        $spend_member = M('pay_log p')
            ->distinct('p.uid')
            ->field('p.uid')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->where($map)
            ->select();
        $spend_count = count($spend_member);
		
        //获取查询数
        $count = M('pay_log p')
            ->field('p.*,g.game as gamename')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->where($map)
            ->count();
        $page = new \Think\Page($count, $pagesize);

        //查询列表
        $list = M('pay_log p')
            ->field('p.*,g.game as gamename,c.name as channel_name')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->order($order.' desc')
            ->select();
        if ($list) {
            //将二维数组转化为一维数组
            $arr=array_column($list,'uid');
            //组成查询条件
            $where['uid']=array('in',$arr);
            //去重查询最后登录时间
            $info= M('login_log')
                ->field('uid,login_time')
                ->where($where) 
                ->order('id asc')
                ->select();
 
            //拼接成去重后数组  
            $login=array_column($info,'login_time','uid');
            //组成最终数组
            foreach ($list as $key => $value) {
               $list[$key]['login_time'] =  isset($login[$value['uid']])?$login[$value['uid']]:'';
            }

            //处理数组中的IP和时间
            Vendor('IP.IP');
            foreach ($list as $k=>$v) {
                $ipArr = \IP::find($v['ip']);
                $list[$k]['ip'] = $v['ip']."(".$ipArr[1].$ipArr[2].")";
            }          
        }
        
        //数据显示 
		$this->assign('sub_channel', $rechannel); 		
        $this->assign('pagesize', $pagesize);
        $this->assign('sum_money', $sum_money);
        $this->assign('spend_count', $spend_count);
        $this->assign('list', $list);
        $this->assign('pagebar', $page->show());
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
    
    
    //充值查询
    public function pay_list(){
        //查询游戏名及id
        $gamelist = $this->getUserGames();
        //获取栏目
		$rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$c_keyword = htmlspecialchars(trim($_REQUEST['c_keyword']));
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);        
        //接收排序条件
        $order = $_REQUEST['order']?$_REQUEST['order']:'pay_time';
        $order = 'p.'.$order;
        $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
        //接收游戏
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
		//接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        //时间处理-start
        $map = array();
        $nowtime = time();
        $start = $_REQUEST['start'];    //充值开始时间
        $end = $_REQUEST['end'];        //充值结束时间
        
        $reg_start = $_REQUEST['reg_start'];//注册开始时间
        $reg_end = $_REQUEST['reg_end'];    //注册结束时间
        
        $start_time = strtotime($start.' 00:00:00'); 
        $end_time = strtotime($end.' 23:59:59');     
        
        $reg_start_time = strtotime($reg_start.' 00:00:00'); 
        $reg_end_time = strtotime($reg_end.' 23:59:59');     
        
        
        if (!$start) {
            $start = date('Y-m-d', $nowtime);
            $start_time = strtotime($start.' 00:00:00');
        }
        if (!$end) {
            $end = date('Y-m-d', $nowtime);
            $end_time = strtotime($end.' 23:59:59');
        }
        
        if (!$reg_start) {
            $reg_start = date('Y-m-d', $nowtime);
            $reg_start_time = strtotime($reg_start.' 00:00:00');
        }
        if (!$reg_end) {
            $reg_end = date('Y-m-d', $nowtime);
            $reg_end_time = strtotime($reg_end.' 23:59:59');
        }
        
        $this->assign('start', $start);
        $this->assign('end', $end);
        
        $this->assign('reg_start', $reg_start);
        $this->assign('reg_end', $reg_end);
        
        $map['p.pay_time'] = array('between', array($start_time, $end_time));
        $map['p.register_time'] = array('between', array($reg_start_time, $reg_end_time));  
        //时间处理-end
        
        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['p.gid'] = array('in', array_column($gamelist, 'gid'));
        }
        $uid = trim($_REQUEST['uid']);
        if ($uid) {
            $map['p.uid'] = $uid;
            $this->assign('uid', $uid);
        }
        $device_id = trim($_REQUEST['device_id']);
        if ($device_id) {
            $map['p.device_id'] = $device_id;
            $this->assign('device_id', $device_id);
        }
		
        if($c_keyword){
			$sec_channel = M('channel')->field('id')->where('name="'.$c_keyword.'"')->find();
			$this->assign('c_keyword',$c_keyword);
		}
		//当选择二级栏目时候
		if($sec_channel){
			$map['p.channel'] = $sec_channel['id'];
		}elseif($sub_channels){//当选择一级栏目时候
			$map['p.total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['p.total_channel'] = array('in',$channel);
        }
		
		
		if($sec_search && !$sec_channel){
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
                $map['p.total_channel'] = array('in',$channelArr);
            }else{
                $map['p.total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //获得总数
        $count = M('pay_log p')
                 ->field('p.*,g.game as gamename')
                 ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
                 ->where($map)
                 ->count();
        //获得总金额
        $sum_money = M('pay_log p')
                     ->field('p.uid')
                     ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
                     ->where($map)
                     ->sum('pay_money');
        //计算充值人数
        $spend_member = M('pay_log p')
                        ->distinct('p.uid')
                        ->field('p.uid')
                        ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
                        ->where($map)
                        ->select();
        $spend_count = count($spend_member);	 
					 
        //分页计算
        $page = new \Think\Page($count, $pagesize);
        //查询列表
        $list = M('pay_log p')
                ->field('p.*,g.game as gamename,c.name as channel_name')
                ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
                ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
                ->where($map)
                ->limit($page->firstRow.','.$page->listRows)
                ->order($order.' desc')
                ->select(); 
        if ($list) {
            //将二维数组转化为一维数组
            $arr=array_column($list,'uid');
            //组成查询条件
            $where['uid']=array('in',$arr);
            //去重查询最后登录时间
            $info= M('login_log')
                ->field('uid,login_time')
                ->where($where) 
                ->order('id asc')
                ->select();
 
            //拼接成去重后数组  
            $login=array_column($info,'login_time','uid');
            //组成最终数组
            foreach ($list as $key => $value) {
               $list[$key]['login_time'] =  isset($login[$value['uid']])?$login[$value['uid']]:'';
            }

            //处理数组中的IP和时间
            Vendor('IP.IP');
            foreach ($list as $k=>$v) {
                $ipArr = \IP::find($v['ip']);
                $list[$k]['ip'] = $v['ip']."(".$ipArr[1].$ipArr[2].")";
            }          
        }
        
        //数据展示  
		$this->assign('sub_channel', $rechannel);
        $this->assign('pagesize', $pagesize);        
        $this->assign('sum_money', $sum_money);
        $this->assign('spend_count', $spend_count);
        $this->assign('list', $list);
        $this->assign('pagebar', $page->show());        
        $this->assign('gamelist', $gamelist);
        $this->display();
    }

    /**
     * 实时注册
     * */
    public function register() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$c_keyword = htmlspecialchars(trim($_REQUEST['c_keyword']));
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
        //列表分页
        $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
        //接收游戏
        $gid = $_REQUEST['gid'];
        //接收设备编号
        $s_device_id = $_REQUEST['s_device_id'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
        //接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*时间处理*/
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
        $map['p.register_time'] = array('between', array($start_time, $end_time));
        //当选择游戏查询
        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['p.gid'] = array('in', array_column($gamelist, 'gid'));
        }
        $uid = trim($_REQUEST['uid']);
        if ($uid) {
            $map['p.uid'] = $uid;
            $this->assign('uid', $uid);
        }
        $device_id = trim($_REQUEST['device_id']);
        if ($device_id) {
            $map['p.device_id'] = $device_id;
            $this->assign('device_id', $device_id);
        }
		
		if($c_keyword){
			$sec_channel = M('channel')->field('id')->where('name="'.$c_keyword.'"')->find();
			$this->assign('c_keyword',$c_keyword);
		}
		//当选择二级栏目时候
		if($sec_channel){
			$map['p.channel'] = $sec_channel['id'];
		}elseif($sub_channels){//当选择一级栏目时候
			$map['p.total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['p.total_channel'] = array('in',$channel);
        }
		
		
		if($sec_search && !$sec_channel){
			if($sub_channels){
				$condition['pid'] = $sub_channels;
			}
			$condition['name'] = array('like',"%".$name."%");
			$sec_list = M('channel')->field('id')->where($condition)->select();
			$this->assign('sec_search',$sec_search);
			if($sec_list){
				$map['p.channel'] = array('in',array_column($sec_list,'id'));
			}else{
				$map['p.channel'] = null;
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
                $map['p.total_channel'] = array('in',$channelArr);
            }else{
                $map['p.total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }

        $map['t.username'] = array('exp', 'is null');
		
		$order = $_REQUEST['order'];
		if($order == 'level'){
			$orderMap = 'gr.level desc';
		}else{
			$orderMap = 'p.id desc';
		}


        //查询注册人数并分页
        $count = M('member p')            
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_game_role gr ON gr.uid=p.uid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->join('LEFT JOIN mygame_member_trial t ON t.uid = p.uid')
            ->where($map)
            ->count();
        $page = new \Think\Page($count, $pagesize);
        //查询列表
        $list = M('member p')
            ->field('p.*,g.game as gamename,gr.level,c.name as channel_name')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_game_role gr ON gr.uid=p.uid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->join('LEFT JOIN mygame_member_trial t ON t.uid = p.uid')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
			->order($orderMap)
            ->select();
        if ($list) {
            //将二维数组转化为一维数组
            $arr=array_column($list,'uid');
            //组成查询条件
            $where['uid']=array('in',$arr);
            //去重查询最后登录时间
            $info= M('login_log')
                ->field('uid,login_time')
                ->where($where) 
                ->order('id asc')
                ->select();
            //拼接成去重后数组  
            $login=array_column($info,'login_time','uid');

            /*
            //查询激活的账户
            $trial_list = M('member_trial')->where($where)->select();
            $trial_name_list = array_column($trial_list, 'username', 'uid');
            */

            //组成最终数组
            foreach ($list as $key => $value) {
               $list[$key]['login_time'] =  isset($login[$value['uid']])?$login[$value['uid']]:'';
               //$list[$key]['username_trial'] =  $trial_name_list[$value['uid']];
            }

            //处理数组中的IP和时间
            Vendor('IP.IP');
            foreach ($list as $k=>$v) {
                $ipArr = \IP::find($v['register_ip']);
                $list[$k]['register_ip'] = $v['register_ip']."(".$ipArr[1].$ipArr[2].")";
            }          
        }

        /*
        //查询试玩已激活人数
        $trial_count = M('member_trial t')
            ->join('INNER JOIN mygame_member p ON p.uid=t.uid')
            ->where($map)
            ->count();
        $this->assign('trial_count', $trial_count);
        */


        //数据显示
        $this->assign('sub_channel', $rechannel);       
        $this->assign('pagesize', $pagesize);
        $this->assign('register_count', $count);
        $this->assign('list', $list);
        $this->assign('pagebar', $page->show());
        $this->assign('gamelist', $gamelist);
        $this->display();
    }

    /*
    下载安装
     */
    public function down_list() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
		$channel =array_column($rechannel,'id');
		$c_keyword = htmlspecialchars(trim($_REQUEST['c_keyword']));
		$sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
		$name = $this->isEscape($sec_search,true);
		
        $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
        //接收游戏
        $gid = $_REQUEST['gid'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
		//接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*处理时间*/
        $map = array();
        $nowtime = time();
        
        $reg_start = $_REQUEST['reg_start']; //注册开始时间
        $reg_end = $_REQUEST['reg_end'];     //注册结束时间
         
        $reg_start_time = strtotime($reg_start.' 00:00:00'); 
        $reg_end_time = strtotime($reg_end.' 23:59:59');
        
        if (!$reg_start) {
            $reg_start = date('Y-m-d', $nowtime);
            $reg_start_time = strtotime($reg_start.' 00:00:00');
        }
        if (!$reg_end) {
            $reg_end = date('Y-m-d', $nowtime);
            $reg_end_time = strtotime($reg_end.' 23:59:59');
        }
        
        $this->assign('reg_start', $reg_start);
        $this->assign('reg_end', $reg_end);
        $map['p.install_time'] = array('between', array($reg_start_time, $reg_end_time));
		
        //当选择游戏查询
        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['p.gid'] = array('in', array_column($gamelist, 'gid'));
        }
        $device_id = trim($_REQUEST['device_id']);
        if ($device_id) {
            $map['p.device_id'] = $device_id;
            $this->assign('device_id', $device_id);
        }
                
		if($c_keyword){
			$sec_channel = M('channel')->field('id')->where('name="'.$c_keyword.'"')->find();
			$this->assign('c_keyword',$c_keyword);
		}
		//当选择二级栏目时候
		if($sec_channel){
			$map['p.channel'] = $sec_channel['id'];
		}elseif($sub_channels){//当选择一级栏目时候
			$map['p.total_channel'] = $sub_channels;
			$this->assign('sub_channel_val',$sub_channels);
        }else{
           $map['p.total_channel'] = array('in',$channel);
        }
		
		
		if($sec_search && !$sec_channel){
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
                $map['p.total_channel'] = array('in',$channelArr);
            }else{
                $map['p.total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }

        //查询列表总数量
        $count = M('install p')
            ->field('p.*,g.game as gamename')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->where($map)
            ->count();
        $page = new \Think\Page($count, $pagesize);
        //查询列表
        $list = M('install p')
            ->field('p.*,g.game as gamename,c.name as channel_name')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->order('install_time desc')
            ->select();

        if ($list) { 
            //处理数组中的IP和时间
            Vendor('IP.IP');
            foreach ($list as $k=>$v) {
                $ipArr = \IP::find($v['ip']);
                $list[$k]['ip'] = $v['ip']."(".$ipArr[1].$ipArr[2].")";
            }          
        }
        
        //数据展示
		$this->assign('sub_channel', $rechannel);
        $this->assign('pagesize', $pagesize);
        $this->assign('register_count', $count);
        $this->assign('list', $list);
        $this->assign('pagebar', $page->show());
        $this->assign('gamelist', $gamelist);
        $this->display();
    }


    /**
     * 试玩查询
     * */
    public function trial() {
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $rechannel = $this->getTotalChannel();
        $typeid = array_unique(array_column($rechannel,'tid'));
        $typeid_map['tid'] = array('in',$typeid);
        $type_model = M('channel_type');
        $type_list = $type_model->where($typeid_map)->field('tid,name')->select();
        $this->assign('typename',$type_list);
        $channel =array_column($rechannel,'id');
        $c_keyword = htmlspecialchars(trim($_REQUEST['c_keyword']));
        $sec_search = htmlspecialchars(trim($_REQUEST['secsearch']));
        $name = $this->isEscape($sec_search,true);
        //列表分页
        $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
        //接收游戏
        $gid = $_REQUEST['gid'];
        //接收设备编号
        $s_device_id = $_REQUEST['s_device_id'];
        //接收渠道类型
        $typename = $_REQUEST['channeltype'];
        //接收栏目
        $sub_channels = $_REQUEST['sub_channel'];
        /*时间处理*/
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
        $map['p.register_time'] = array('between', array($start_time, $end_time));
        //当选择游戏查询
        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
        }else {
            $map['p.gid'] = array('in', array_column($gamelist, 'gid'));
        }
        $uid = trim($_REQUEST['uid']);
        if ($uid) {
            $map['p.uid'] = $uid;
            $this->assign('uid', $uid);
        }
        $device_id = trim($_REQUEST['device_id']);
        if ($device_id) {
            $map['p.device_id'] = $device_id;
            $this->assign('device_id', $device_id);
        }

        if($c_keyword){
            $sec_channel = M('channel')->field('id')->where('name="'.$c_keyword.'"')->find();
            $this->assign('c_keyword',$c_keyword);
        }
        //当选择二级栏目时候
        if($sec_channel){
            $map['p.channel'] = $sec_channel['id'];
        }elseif($sub_channels){//当选择一级栏目时候
            $map['p.total_channel'] = $sub_channels;
            $this->assign('sub_channel_val',$sub_channels);
        }else{
            $map['p.total_channel'] = array('in',$channel);
        }


        if($sec_search && !$sec_channel){
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
                $map['p.total_channel'] = array('in',$channelArr);
            }else{
                $map['p.total_channel'] = null;
            }
            $this->assign('type_name',$typename);
        }

        $order = $_REQUEST['order'];
        if($order == 'level'){
            $orderMap = 'gr.level desc';
        }else{
            $orderMap = 'p.id desc';
        }

        //查询注册人数并分页
        $count = M('member_trial p')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_game_role gr ON gr.uid=p.uid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->where($map)
            ->count();
        $page = new \Think\Page($count, $pagesize);
        //查询列表
        $list = M('member_trial p')
            ->field('p.*,g.game as gamename,gr.level,c.name as channel_name')
            ->join('LEFT JOIN mygame_game g ON g.gid=p.gid')
            ->join('LEFT JOIN mygame_game_role gr ON gr.uid=p.uid')
            ->join('LEFT JOIN mygame_channel c ON p.channel = c.id')
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->order($orderMap)
            ->select();
        if ($list) {
            //将二维数组转化为一维数组
            $arr=array_column($list,'uid');
            //组成查询条件
            $where['uid']=array('in',$arr);
            //去重查询最后登录时间
            $info= M('login_log')
                ->field('uid,login_time')
                ->where($where)
                ->order('id asc')
                ->select();
            //拼接成去重后数组
            $login=array_column($info,'login_time','uid');
            //组成最终数组
            foreach ($list as $key => $value) {
                $list[$key]['login_time'] =  isset($login[$value['uid']])?$login[$value['uid']]:'';
            }

            //查询激活的账户
            $active_list = M('member')->where($where)->select();
            $active_name_list = array_column($active_list, 'username', 'uid');


            //处理数组中的IP和时间
            Vendor('IP.IP');
            foreach ($list as $k=>$v) {
                $ipArr = \IP::find($v['register_ip']);
                $list[$k]['register_ip'] = $v['register_ip']."(".$ipArr[1].$ipArr[2].")";
                $list[$k]['active_name'] = $active_name_list[$v['uid']];
            }
        }

        //查询试玩已激活人数
        $active_count = M('member_trial t')
            ->join('INNER JOIN mygame_member p ON p.uid=t.uid')
            ->where($map)
            ->count();
        $this->assign('active_count', $active_count);

        //数据显示
        $this->assign('sub_channel', $rechannel);
        $this->assign('pagesize', $pagesize);
        $this->assign('register_count', $count);
        $this->assign('list', $list);
        $this->assign('pagebar', $page->show());
        $this->assign('gamelist', $gamelist);
        $this->display();
    }


}