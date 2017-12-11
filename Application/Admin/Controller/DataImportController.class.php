<?php
namespace Admin\Controller;
use Org\Util\Rbac;
use \Think\Controller;
use Think\Crypt\Driver\Think;


class DataImportController extends CommonController {
	public function _initialize() {
        parent::_initialize();
    }

    //导出下载总汇
	public function down_dao(){
		$channel = $this->getChannel();
		
		ini_set('memory_limit','256M');
		//注册时间
		$map = array();
		$nowtime = time();
		$starttime = strtotime($_REQUEST['start']);
		$endtime = strtotime($_REQUEST['end']);	
		$start = ($starttime > $nowtime) ? date('Y-m-d',time()).' 00:00:00' :date('Y-m-d',$starttime).' 00:00:00';
		$end = ($endtime > $nowtime) ? date('Y-m-d',time()).' 23:59:59' :date('Y-m-d',$endtime).' 23:59:59';
		$_REQUEST['start'] = substr($start,0,10);	
		$_REQUEST['end'] = substr($end,0,10);
		$start_time = strtotime($start);
		$end_time = strtotime($end);
		//文件名
		$filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'下载总汇';
		$game_id = I('get.gid','','intval');
		if ($_REQUEST['gid']) {
			$map['gid'] = $_REQUEST['gid'];
		}

		$map['install_time'] = array('between', array($start_time, $end_time));

		$sec_search = $_REQUEST['secsearch'];
		//接收栏目
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
		//当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr[]= $sec_channels;
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
             //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array(); 
            $channelArr[] = $sub_channels;
			$condition['pid'] = $sub_channels;
			if($sec_search){
				$condition['name'] = array('like','%'.$sec_search.'%');
			}
            $seclist = M('channel')->where($condition)->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'id'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
			if($sec_search){
				$dataArr= $this->get_sec_channels();
				if($dataArr){
					$channelArr = $dataArr;
				}else{
					$channelArr = array_column($channel['sub_channel'],'id');//顶级
				}
			}else{
				if($this->meminfo['username'] == 'admin'){
					$channelArr = null;
				}else{
					$channelArra = array_column($channel['sub_channel'],'id');//顶级
					$channelArrb = array_column($channel['sec_channel'],'id');//子级
					$channelArr = array_merge($channelArra,$channelArrb);
				}
			}                   
        }
		$map['channel'] = array('in', $channelArr);
		
		
		//如果开始时间等于今天，则查主库
		if( date('Y-m-d',time()) == $_REQUEST['start']){
			 $list = M('install', 'mygame_', 'DB_CONFIG_CHONG')
                    ->where($map)
                    ->select();		
		}elseif(date('Y-m-d',time()) == $_REQUEST['end']){ //如果开始时间在今天之前，结束时间在今天之后，则分情况查询
			$year1 = (int)date('Y', $start_time);
			$year2 = (int)date('Y', $end_time);
			unset($map['install_time']);
			$nowtime_start = strtotime(date('Y-m-d', time()).' 00:00:00');
			$map['install_time'] = array('between',array($nowtime_start,$end_time));
			//今天的数据查主表
			$list1 = M('install', 'mygame_', 'DB_CONFIG_CHONG')
                    ->where($map)
                    ->select();
			unset($map['install_time']);
			$map['install_time'] = array('between',array($start_time,$nowtime_start-1));

			//今天之前的数据查tongji_install表
				$table = M('install', 'mygame_', 'DB_CONFIG_CHONG');
				$list2 = $table->where($map)
					->select();
			$list = array_merge($list1,$list2);	
		}else{
			$year1 = (int)date('Y', $start_time);
			$year2 = (int)date('Y', $end_time);
			// 都在同一年
			$table = M('install', 'mygame_', 'DB_CONFIG_CHONG');
			$list = $table->where($map)
				->select();	
		}

		if($list){
			$gameresult = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,game')->where(array('gid'=>array('in', array_column($list, 'gid'))))->select();
			$gameresultnew = array();
			foreach ($gameresult as $val) {
				$gameresultnew[$val['gid']] = $val['game'];
			}

			$channelresult = M('channel', 'mygame_', 'DB_CONFIG_CHONG')->field('id,name')->where(array('id'=>array('in', array_column($list, 'channel'))))->select();
			$channelresultnew = array();
			foreach ($channelresult as $val) {
				$channelresultnew[$val['id']] = $val['name'];
			}

			Vendor('IP.IP');
			foreach ($list as $key=>$val) {
				$down_list[$key]['gid'] = $gameresultnew[$val['gid']];
				$down_list[$key]['device_id'] =$list[$key]['device_id'];
				$ipArr = \IP::find($val['ip']);
				$down_list[$key]['ip'] = $val['ip']."(".$ipArr[1].$ipArr[2].")";
				$down_list[$key]['channel'] =$channelresultnew[$val['channel']];
				$down_list[$key]['install_time']=date('Y-m-d H:i:s',$list[$key]['install_time']);
				$down_list[$key]['os'] =$list[$key]['os'];
			}
		}
		//dump($down_list);die;
		//导出来源url
		$title = array('游戏名','唯一标识','下载IP','渠道','下载时间','操作系统');
        $filename = $_SESSION['user'].'-'.$filename;
		$this->export($down_list,$filename,$title);
	}

	//导出注册总汇
	public function reg_dao(){
		$channel = $this->getChannel();		
		ini_set('memory_limit','256M');
		//注册时间
		$nowtime = time();
		$starttime = strtotime($_REQUEST['start']);
		$endtime = strtotime($_REQUEST['end']);	
		$start = ($starttime > $nowtime) ? date('Y-m-d',time()).' 00:00:00' :date('Y-m-d',$starttime).' 00:00:00';
		$end = ($endtime > $nowtime) ? date('Y-m-d',time()).' 23:59:59' :date('Y-m-d',$endtime).' 23:59:59';
		$_REQUEST['start'] = substr($start,0,10);	
		$_REQUEST['end'] = substr($end,0,10);
		$start_time = strtotime($start);
		$end_time = strtotime($end);
		//文件名
		$filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'注册总汇';
		$game_id = I('get.gid','','intval');
		if ($_REQUEST['gid']) {
			$map['gid'] = $_REQUEST['gid'];
		}

		$map['register_time'] = array('between', array($start_time, $end_time));
		
		
		$sec_search = $_REQUEST['secsearch'];
		//接收栏目
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
		//当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr[]= $sec_channels;
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
             //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array(); 
            $channelArr[] = $sub_channels;
			$condition['pid'] = $sub_channels;
			if($sec_search){
				$condition['name'] = array('like','%'.$sec_search.'%');
			}
            $seclist = M('channel')->where($condition)->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'id'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
			if($sec_search){
				$dataArr= $this->get_sec_channels();
				if($dataArr){
					$channelArr = $dataArr;
				}else{
					$channelArr = array_column($channel['sub_channel'],'id');//顶级
				}
			}else{
				if($this->meminfo['username'] == 'admin'){
					$channelArr = null;
				}else{
					$channelArra = array_column($channel['sub_channel'],'id');//顶级
					$channelArrb = array_column($channel['sec_channel'],'id');//子级
					$channelArr = array_merge($channelArra,$channelArrb);
				}
			}                   
        }
		if ($channelArr) $map['channel'] = array('in', $channelArr);

        $list = M('member')->where($map)->order('register_time desc')->select();
	
		if($list){
			$gameresult = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,game')->where(array('gid'=>array('in', array_column($list, 'gid'))))->select();
			$gameresultnew = array();
			foreach ($gameresult as $val) {
				$gameresultnew[$val['gid']] = $val['game'];
			}

			$timeresult = M('login_log', 'mygame_', 'DB_CONFIG_CHONG')->field('uid,login_time')->where(array('uid'=>array('in', array_column($list, 'uid'))))->select();
			$timeresultnew = array();
			foreach ($timeresult as $val) {
				$timeresultnew[$val['uid']] = $val['login_time'];
			}
			
			$channelresult = M('channel', 'mygame_', 'DB_CONFIG_CHONG')->field('id,name')->where(array('id'=>array('in', array_column($list, 'channel'))))->select();
			$channelresultnew = array();
			foreach ($channelresult as $val) {
				$channelresultnew[$val['id']] = $val['name'];
			}

            //游戏等级
            $listUidArr = array_column($list, 'uid');
            $roleList = M('game_role', 'mygame_', 'DB_CONFIG_CHONG')->field('*')->where(array('uid'=>array('in', $listUidArr)))->order('uid,gid,level')->select();
            $roleArr = array();
            foreach ($roleList as $k=>$v) {
                $roleArr[$v['uid'].$v['gid']] = $v;
            }

            //查询激活的账户
            Vendor('IP.IP');
			foreach ($list as $key=>$val) {
			  
				$reg_list[$key]['uid'] =$list[$key]['uid'];
				$reg_list[$key]['username'] =$list[$key]['username']; 
				$reg_list[$key]['gid'] = $gameresultnew[$val['gid']];
				$reg_list[$key]['device_id'] =$list[$key]['device_id'];
				$ipArr = \IP::find($val['register_ip']);
				$reg_list[$key]['register_ip'] = $val['register_ip']."(".$ipArr[1].$ipArr[2].")";
				$reg_list[$key]['channel'] =$channelresultnew[$val['channel']];
				$reg_list[$key]['register_time']=date('Y-m-d H:i:s',$list[$key]['register_time']);
				$reg_list[$key]['login_time']=date('Y-m-d H:i:s',isset($timeresultnew[$val['uid']])?$timeresultnew[$val['uid']]:'');
				$reg_list[$key]['os'] =$list[$key]['os'];
                $roleKey = $val['uid'].$val['gid'];
                $reg_list[$key]['level'] = $roleArr[$roleKey]['level']?$roleArr[$roleKey]['level']:0;
			}
		}
		//导出来源url
		$title = array('UID','用户名','游戏名','唯一标识','注册IP','渠道','注册时间','最后登录时间','操作系统','角色等级');
        $filename = $_SESSION['user'].'-'.$filename;
		$this->export($reg_list,$filename,$title);
	}

	//试玩导出
    public function trial_dao(){
        $channel = $this->getChannel();
        ini_set('memory_limit','256M');
        //注册时间
        $nowtime = time();
        $starttime = strtotime($_REQUEST['start']);
        $endtime = strtotime($_REQUEST['end']);
        $start = ($starttime > $nowtime) ? date('Y-m-d',time()).' 00:00:00' :date('Y-m-d',$starttime).' 00:00:00';
        $end = ($endtime > $nowtime) ? date('Y-m-d',time()).' 23:59:59' :date('Y-m-d',$endtime).' 23:59:59';
        $_REQUEST['start'] = substr($start,0,10);
        $_REQUEST['end'] = substr($end,0,10);
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        //文件名
        $filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'试玩总汇';
        $game_id = I('get.gid','','intval');
        if ($_REQUEST['gid']) {
            $map['gid'] = $_REQUEST['gid'];
        }

        $map['register_time'] = array('between', array($start_time, $end_time));


        $sec_search = $_REQUEST['secsearch'];
        //接收栏目
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
        //当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr[]= $sec_channels;
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
            //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array();
            $channelArr[] = $sub_channels;
            $condition['pid'] = $sub_channels;
            if($sec_search){
                $condition['name'] = array('like','%'.$sec_search.'%');
            }
            $seclist = M('channel')->where($condition)->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'id'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
            if($sec_search){
                $dataArr= $this->get_sec_channels();
                if($dataArr){
                    $channelArr = $dataArr;
                }else{
                    $channelArr = array_column($channel['sub_channel'],'id');//顶级
                }
            }else{
                if($this->meminfo['username'] == 'admin'){
                    $channelArr = null;
                }else{
                    $channelArra = array_column($channel['sub_channel'],'id');//顶级
                    $channelArrb = array_column($channel['sec_channel'],'id');//子级
                    $channelArr = array_merge($channelArra,$channelArrb);
                }
            }
        }
        if ($channelArr) $map['channel'] = array('in', $channelArr);

        $list = M('member_trial')->where($map)->order('register_time desc')->select();

        if($list){
            $listarray = array_column($list, 'uid');

            $gameresult = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,game')->where(array('gid'=>array('in', array_column($list, 'gid'))))->select();
            $gameresultnew = array();
            foreach ($gameresult as $val) {
                $gameresultnew[$val['gid']] = $val['game'];
            }

            $listUidArr = array_column($list, 'uid');
            $timeresult = M('login_log', 'mygame_', 'DB_CONFIG_CHONG')->field('uid,login_time')->where(array('uid'=>array('in', array_column($list, 'uid'))))->select();
            $timeresultnew = array();
            foreach ($timeresult as $val) {
                $timeresultnew[$val['uid']] = $val['login_time'];
            }

            $channelresult = M('channel', 'mygame_', 'DB_CONFIG_CHONG')->field('id,name')->where(array('id'=>array('in', array_column($list, 'channel'))))->select();
            $channelresultnew = array();
            foreach ($channelresult as $val) {
                $channelresultnew[$val['id']] = $val['name'];
            }

            $rolemap = array();
            $rolemap['uid'] = array('in', $listarray);
            $roleresult = M('game_role')->where($rolemap)->select();
            $rolearr = array();
            foreach ($roleresult as $val) {
                $rolearr[$val['uid'].$val['gid']] = $val;
            }

            //查询激活的账户
            $active_list = M('member')->where(array('uid'=>array('in', $listUidArr)))->select();
            $active_name_list = array_column($active_list, 'username', 'uid');

            Vendor('IP.IP');
            foreach ($list as $key=>$val) {
            	$reg_list[$key]['uid'] =$list[$key]['uid'];
                $reg_list[$key]['username'] =$list[$key]['username'];
                $reg_list[$key]['active_name'] =$active_name_list[$val['uid']];
                $reg_list[$key]['gid'] = $gameresultnew[$val['gid']];
                $reg_list[$key]['device_id'] =$list[$key]['device_id'];
                $ipArr = \IP::find($val['register_ip']);
                $reg_list[$key]['register_ip'] = $val['register_ip']."(".$ipArr[1].$ipArr[2].")";
                $reg_list[$key]['channel'] =$channelresultnew[$val['channel']];
                $reg_list[$key]['register_time']=date('Y-m-d H:i:s',$list[$key]['register_time']);
                $reg_list[$key]['login_time']=date('Y-m-d H:i:s',isset($timeresultnew[$val['uid']])?$timeresultnew[$val['uid']]:'');
                $reg_list[$key]['os'] =$list[$key]['os'];
                $level = $rolearr[$val['uid'].$val['gid']]['level'];
                $reg_list[$key]['level'] = $level?$level:0;
            }
        }

        //导出来源url
        $title = array('UID','用户名','激活用户名','游戏名','唯一标识','注册IP','渠道','注册时间','最后登录时间','操作系统','等级');
        $filename = $_SESSION['user'].'-'.$filename;
        $this->export($reg_list,$filename,$title);
    }


	//导出充值总汇
	
	public function real_dao(){
		$channel = $this->getChannel();
		ini_set('memory_limit','256M');
		
		$start = $_REQUEST['start']?urldecode($_REQUEST['start'].' 00:00:00'):date('Y-m-d').' 00:00:00';	
		$end = $_REQUEST['end']?urldecode($_REQUEST['end'].' 23:59:59'):date('Y-m-d H:i:s');
        $nowtime = time();
		$nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
		$start_time = strtotime($start);
		$end_time = strtotime($end);
		if($start_time > $nowtime && $end_time > $nowtime){
			$_REQUEST['end'] = urldecode(date('Y-m-d', $nowtime));
			$_REQUEST['start'] = urldecode(date('Y-m-d', $nowtime));
		}elseif($end_time > $nowtime){
			$_REQUEST['end'] = urldecode(date('Y-m-d', $nowtime));
		}
		//文件名
		$filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'充值总汇';
		$game_id = I('get.gid','','intval');
		if ($_REQUEST['gid']) {
			$map['gid'] = $_REQUEST['gid'];
		}
		$sec_search = $_REQUEST['secsearch'];
		//接收栏目
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
		//当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr[]= $sec_channels;
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
             //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array(); 
            $channelArr[] = $sub_channels;
			$condition['pid'] = $sub_channels;
			if($sec_search){
				$condition['name'] = array('like','%'.$sec_search.'%');
			}
            $seclist = M('channel')->where($condition)->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'id'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
			if($sec_search){
				$dataArr= $this->get_sec_channels();
				if($dataArr){
					$channelArr = $dataArr;
				}else{
					$channelArr = array_column($channel['sub_channel'],'id');//顶级
				}
			}else{
				if($this->meminfo['username'] == 'admin'){
					$channelArr = null;
				}else{
					$channelArra = array_column($channel['sub_channel'],'id');//顶级
					$channelArrb = array_column($channel['sec_channel'],'id');//子级
					$channelArr = array_merge($channelArra,$channelArrb);
				}
			}                   
        }
		$map['channel'] = array('in', $channelArr);
		//获取前端传过来的账号条件--start	
		$map['pay_time'] = array('between',array($start_time,$end_time));

		//根据条件查询出用户充值信息
		$spend_log = M('pay_log','mygame_','DB_CONFIG_CHONG');
		$spend_list = $spend_log
			->where($map)
			->select();	
		if($spend_list){
			$gameresult = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,game')->where(array('gid'=>array('in', array_column($spend_list, 'gid'))))->select();

			$gameresultnew = array();
			foreach ($gameresult as $val) {
				$gameresultnew[$val['gid']] = $val['game'];
			}

			$channelresult = M('channel', 'mygame_', 'DB_CONFIG_CHONG')->field('id,name')->where(array('id'=>array('in', array_column($spend_list, 'channel'))))->select();
			$channelresultnew = array();
			foreach ($channelresult as $val) {
				$channelresultnew[$val['id']] = $val['name'];
			}

			Vendor('IP.IP');
			foreach ($spend_list as $key=>$val) {
				$list[$key]['uid']       	  = $spend_list[$key]['uid'];
				$list[$key]['username']       = $spend_list[$key]['username'];
				$list[$key]['order_id']  	  = $spend_list[$key]['order_id'];
				$list[$key]['gid']       	  = $gameresultnew[$val['gid']];
				$list[$key]['pay_money'] 	  = $spend_list[$key]['pay_money'];
				$list[$key]['channel']   	  = $channelresultnew[$val['channel']];
				$ipArr = \IP::find($val['ip']);
				$list[$key]['ip']        	  = $spend_list[$key]['ip']."(".$ipArr[1].$ipArr[2].")";
				$list[$key]['pay_time']=date('Y-m-d H:i:s',$spend_list[$key]['pay_time']);
				$list[$key]['register_time']  = date('Y-m-d H:i:s',$val['register_time']);
				$regipArr = \IP::find($val['register_ip']);
				$list[$key]['register_ip']    = $spend_list[$key]['register_ip']."(".$regipArr[1].$regipArr[2].")";
			}
		}

		$title = array('UID','用户名','订单','游戏名','充值金额','渠道','充值IP','充值日期','注册日期','注册IP');
        $filename = $_SESSION['user'].'-'.$filename;
		$this->export($list,$filename,$title);	
	}

	/**
	 * 注册充值导出
	 * */
	public function regpay_dao() {
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

        $filename = $reg_start ."到".$reg_end.'注册充值总汇';

        $map['p.pay_time'] = array('between', array($start_time, $end_time));
        $map['p.register_time'] = array('between', array($reg_start_time, $reg_end_time));
        //时间处理-end

        if ($gid) {
            $map['p.gid'] = $gid;
            $this->assign('gid', $gid);
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
        //游戏查询
        $gamelist = M('game')
            ->field('gid,game')
            ->order('gid desc')
            ->select();
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
            $listdata = array();
            foreach ($list as $k=>$v) {
            	$listdata[$k]['uid'] = $v['uid'];
                $listdata[$k]['username'] = $v['username'];
                $listdata[$k]['order_id'] = $v['order_id'];
                $listdata[$k]['gamename'] = $v['gamename'];
                $listdata[$k]['pay_money'] = $v['pay_money'];
                $listdata[$k]['channel_name'] = $v['channel_name'];
                $ipArr = \IP::find($v['ip']);
                $listdata[$k]['ip'] = $v['ip']."(".$ipArr[1].$ipArr[2].")";
                $listdata[$k]['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
                $listdata[$k]['register_time'] = date('Y-m-d H:i:s', $v['register_time']);
                $ipArr = \IP::find($v['register_ip']);
                $listdata[$k]['register_ip'] = $v['register_ip']."(".$ipArr[1].$ipArr[2].")";
            }
        }

        $title = array('UID','用户名','订单','游戏名','充值金额','渠道','充值IP','充值日期','注册日期','注册IP');
        $filename = $_SESSION['user'].'-'.$filename;
        $this->export($listdata,$filename,$title);
    }
	
	//回款汇总
	public function back_dao(){
			$channel = $this->getChannel();
			ini_set('memory_limit','256M');
			//时间处理
			$start = $_REQUEST['start']?urldecode($_REQUEST['start'].' 00:00:00'):date('Y-m-d').' 00:00:00';
			$end = $_REQUEST['end']?urldecode($_REQUEST['end'].' 23:59:59'):date('Y-m-d H:i:s');
			$nowtime = time();
			$nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
			$start_time = strtotime($start);
			$end_time = strtotime($end);
			if($start_time > $nowtime && $end_time > $nowtime){
				$_REQUEST['end'] = urldecode(date('Y-m-d', $nowtime));
				$_REQUEST['start'] = urldecode(date('Y-m-d', $nowtime));
			}elseif($end_time > $nowtime){
				$_REQUEST['end'] = urldecode(date('Y-m-d', $nowtime));
			}
			
			//文件名
			$filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'回款总汇';
			$timeflag = ($end_time>$nowtime_start)?true:false;
			$game_id = I('get.gid','','intval');
			$uid = I('get.uid','','intval');
			if ($_REQUEST['gid']) {
				$map['gid'] = $_REQUEST['gid'];
			}	
			$sec_search = $_REQUEST['secsearch'];
			//接收栏目
			$sec_channels = $_REQUEST['sec_channel'];
			$sub_channels = $_REQUEST['sub_channel'];
			//当选择二级栏目时候
			if ($sec_channels) {
				//获得二级栏目后，查询他的一级栏目并展示
				$channelArr[]= $sec_channels;
				$sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
				 //获得二级栏目后，二级栏目列表
				$seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
			}else if($sub_channels){//当选择一级栏目时候
				$channelArr = array(); 
				$channelArr[] = $sub_channels;
				$condition['pid'] = $sub_channels;
				if($sec_search){
					$condition['name'] = array('like','%'.$sec_search.'%');
				}
				$seclist = M('channel')->where($condition)->select();
				$channelArr = array_merge($channelArr, array_column($seclist, 'id'));
				$this->assign('sub_channel_val', $sub_channels);
				$this->assign('sec_channel', $seclist);
			}else{
				//默认查询所有栏目下内容
				if($sec_search){
					$dataArr= $this->get_sec_channels();
					if($dataArr){
						$channelArr = $dataArr;
					}else{
						$channelArr = array_column($channel['sub_channel'],'id');//顶级
					}
				}else{
					$channelArra = array_column($channel['sub_channel'],'id');//顶级
					$channelArrb = array_column($channel['sec_channel'],'id');//子级
					$channelArr = array_merge($channelArra,$channelArrb);
				}                   
			}
			$map['channel'] = array('in', $channelArr);
					
			$map['pay_time'] = array('between',array($start_time,$end_time));
			$oldflag=C("IS_NEW");

			$flag = $oldflag?($timeflag?true:false):true;
			$pay_log = M('pay_log','mygame_','DB_CONFIG_CHONG');
			$map_month = $map;
			$map_month['register_time'] = array('between',array($start_time,$end_time));
			if($flag){		
				$spend_list = $pay_log
				->field('gid,sum(pay_money) as sum')
				->where($map)
				->group('gid')
				->order('gid')
				->select();	
				//查出当月spend的信息
				$spend_month = $pay_log
				->field('gid,sum(pay_money) as sum_month')
				->where($map_month)
				->group('gid')
				->order('gid')
				->select();
			}else{
				$year1 = date('Y',$start_time);
				$year2 = date('Y',$end_time);
				unset($map_month['register_time']);
				$map_month['register_time'] = array('between',array($start_time,$end_time));
				$model = M('pay_log','mygame_','DB_CONFIG_CHONG');
				//时间是同一年
				if($year1 == $year2){	
					$spend_list = $model
						->field('gid,sum(pay_money) as sum')
						->where($map)
						->order('gid')
						->group('gid')
						->select();
				//当月回款查询
					$spend_month = $model
						->field('gid,sum(pay_money) as sum_month')
						->where($map_month)
						->group('gid')
						->order('gid')
						->select();						
				//时间是跨年
				}elseif($year2-$year1 == 1){					
					$spend_list = $model
							->field('gid,sum(pay_money) as sum')
							->where($map)
							->order ('gid')
							->group('gid')
							->select();
					//当月回款查询			
					$spend_month = $model
							->field('gid,sum(pay_money) as sum_month')
							->order ('gid')
							->group('gid')
							->where($map_month)
							->select();							
				}
			}
			
			$gid1 = array();
			$gid2 = array();
			foreach($spend_list as $key =>$value){
				$gid1[] = $value['gid'];
				$sum += $value['sum'];
				$spend_sum[$value['gid']] = $value['sum'];
			}
			
			foreach($spend_month as $k =>$val){
				$gid2[] = $val['gid'];
				$sum_month += $val['sum_month'];
				$spend_sum_month[$val['gid']] = $val['sum_month'];
			}
			
			$gid_list = array_unique(array_merge($gid1,$gid2));
			
			//查询出有充值的游戏的信息
			if($spend_month || $spend_list){
				$game = M('game','mygame_','DB_CONFIG_CHONG');
				$game_list = $game->field('gid,game')->where(array('gid'=>array('in', $gid_list)))->order('gid')->select();	
			}
			$count = count($game_list); 
			$export = new ExportController();
			if($spend_list){				
				//设置游戏名和游戏的充值汇总
				for($i=0;$i<$count;$i++){
					$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'3',$game_list[$i]['game']);
					if(Current($gid1) == $game_list[$i]['gid'] ){
						$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'5',$spend_sum[Current($gid1)]);	
						next($gid1);
					}else{
						$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'5',0);	
					}	
				}
				$export->objPHPExcel->getActiveSheet()->setCellValue('B5', $sum);
			}else{
				for($i=0;$i<$count;$i++){
					$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'3',$game_list[$i]['game']);
					$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'5',0);
				}
				$export->objPHPExcel->getActiveSheet()->setCellValue('B5', 0);
			}
			if($spend_month){
				for($i=0;$i<$count;$i++){
					if(Current($gid2) == $game_list[$i]['gid'] ){
						$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'4',$spend_sum_month[Current($gid2)]);
						next($gid2);
					}else{
						$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'4',0);	
					}
				}	
				$export->objPHPExcel->getActiveSheet()->setCellValue('B4', $sum_month);				
			}else{
				$export->objPHPExcel->getActiveSheet()->setCellValue('B4', 0);
				for($i=0;$i<$count;$i++){
					$export->objPHPExcel->getActiveSheet()->setCellValue($export->chars[$i+3].'4',0);
				}	
			}		
			//设置背景颜色
			$export->objPHPExcel->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FF808080');
			
			//设置excel属性
			$export->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
			$export->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
			 //设置行高度
			
			$export->objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);
			$export->objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
			$export->objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
			//设置字体大小和宽度
			$export->objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);
			$export->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
			$export->objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$export->objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$export->objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
			$export->objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
			$export->objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
			
			//合并单元格
			$export->objPHPExcel->getActiveSheet()->mergeCells('A2:B3');
			if($count){
				$export->objPHPExcel->getActiveSheet()->mergeCells('A1:'.$export->chars[$count+2].'1');
				$export->objPHPExcel->getActiveSheet()->mergeCells('C2:'.$export->chars[$count+2].'2');	
			}else{
				$export->objPHPExcel->getActiveSheet()->mergeCells('A1:'.$export->chars[10].'1');
				$export->objPHPExcel->getActiveSheet()->mergeCells('C2:'.$export->chars[10].'2');	
			}
			
			//设置居中
			$export->objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$export->objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$export->objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$export->objPHPExcel->getActiveSheet()->setCellValue('A4','当月回款');		
			$export->objPHPExcel->getActiveSheet()->setCellValue('A5', $_REQUEST['start'] ."到". $_REQUEST['end'].'总回款');
			$export->objPHPExcel->getActiveSheet()->setCellValue('C2', '游戏回款明细');
			$export->objPHPExcel->getActiveSheet()->setCellValue('A2', $_SESSION['user']);
			$export->objPHPExcel->getActiveSheet()->setCellValue('A1', '游戏推广回款表');
            $filename = $_SESSION['user'].'-'.$filename;
			$export->createDocument($filename);	
					
	}

	/**
	 * LTV指标导出
	 * */
	public function ltv_dao() {
        $channel = $this->getChannel();
        ini_set('memory_limit','256M');
        //注册时间
        $nowtime = time();
        $starttime = strtotime($_REQUEST['start']);
        $endtime = strtotime($_REQUEST['end']);
        $start = ($starttime > $nowtime) ? date('Y-m-d',time()).' 00:00:00' :date('Y-m-d',$starttime).' 00:00:00';
        $end = ($endtime > $nowtime) ? date('Y-m-d',time()).' 23:59:59' :date('Y-m-d',$endtime).' 23:59:59';
        $_REQUEST['start'] = substr($start,0,10);
        $_REQUEST['end'] = substr($end,0,10);
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        //文件名
        $filename = $_REQUEST['start'] ."到".$_REQUEST['end'].'LTV指标总汇';
        $game_id = I('get.gid','','intval');
        if ($_REQUEST['gid']) {
            $map['gid'] = $_REQUEST['gid'];
        }


        $sec_search = $_REQUEST['secsearch'];
        //接收栏目
        $sec_channels = $_REQUEST['sec_channel'];
        $sub_channels = $_REQUEST['sub_channel'];
        //当选择二级栏目时候
        if ($sec_channels) {
            //获得二级栏目后，查询他的一级栏目并展示
            $channelArr[]= $sec_channels;
            $sub_channel_val = M('channel')->getFieldById($sec_channels, 'pid');
            //获得二级栏目后，二级栏目列表
            $seclist = M('channel')->where(array('pid'=>$sub_channel_val))->select();
        }else if($sub_channels){//当选择一级栏目时候
            $channelArr = array();
            $channelArr[] = $sub_channels;
            $condition['pid'] = $sub_channels;
            if($sec_search){
                $condition['name'] = array('like','%'.$sec_search.'%');
            }
            $seclist = M('channel')->where($condition)->select();
            $channelArr = array_merge($channelArr, array_column($seclist, 'id'));
            $this->assign('sub_channel_val', $sub_channels);
            $this->assign('sec_channel', $seclist);
        }else{
            //默认查询所有栏目下内容
            if($sec_search){
                $dataArr= $this->get_sec_channels();
                if($dataArr){
                    $channelArr = $dataArr;
                }else{
                    $channelArr = array_column($channel['sub_channel'],'id');//顶级
                }
            }else{
                if($this->meminfo['username'] == 'admin'){
                    $channelArr = null;
                }else{
                    $channelArra = array_column($channel['sub_channel'],'id');//顶级
                    $channelArrb = array_column($channel['sec_channel'],'id');//子级
                    $channelArr = array_merge($channelArra,$channelArrb);
                }
            }
        }
        if ($channelArr) $map['channel'] = array('in', $channelArr);

        $pay_log = M('pay_log');
        $member = M('member');

        //指标天数
        $xlist = array(1,3,7,15,30,60);

        $daylist = array();
        $t = $start_time;
        do{
            $daylist[] = array(
                'start' =>  $t,
                'end' =>  $t + 24*3600 - 1,
            );
            $t = $t + 24*3600;
        }while($t < $end_time);

        $pmap = $map;
        $result = array();
        foreach ($daylist as $val) {
            $row = array();
            $row['date'] = date('m月d日', $val['start']);

            $map['register_time'] = array('between', array($val['start'], $val['end']));
            $row['reg_num'] = $member->where($map)->count();
            foreach ($xlist as $v) {
                $pmap['register_time'] = array('between', array($val['start'],$val['end']));
                $pmap['pay_time'] = array('between', array($val['start'],$val['start']+$v*24*3600));
                $row['pay_sum_'.$v] = $pay_log->where($pmap)->sum('pay_money');
                $row['ltv_'.$v] = round($row['pay_sum_'.$v]/$row['reg_num'], 2);
            }
            $result[] = $row;
        }

        //导出来源url
        $title = array('日期','注册');
        foreach ($xlist as $v) {
            if ($v == 1) {
                $title[] = '当日付费';
                $title[] = '当日LTV';
            }else {
                $title[] = $v.'日付费';
                $title[] = $v.'日LTV';
            }
        }
        $filename = $_SESSION['user'].'-'.$filename;
        $this->export($result,$filename,$title);
    }

	//php导表
	/*
	*@param $list array 传入要导出的二维数组
	*@param $filename string 文件名字
	*@param $title array 表头

	*/	
	public function export($list,$filename,$title){	
		$ua = $_SERVER["HTTP_USER_AGENT"];   
		$encoded_filename = urlencode($filename);  
		$encoded_filename = str_replace("+", "%20", $encoded_filename);  
		header('Content-Type: application/octet-stream');  
		if (preg_match("/MSIE/", $ua)) {  
			header('Content-Disposition: attachment; filename="' . $encoded_filename . '.csv"');  
		} else if (preg_match("/Firefox/", $ua)) {  
			header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.csv"');  
		} else {  
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"');  
		}
		$fp = fopen('php://output', 'a'); 
		foreach ($title as $i => $one) {  
            $head[$i] = iconv('utf-8', 'utf8', $one);
        }
		// 将数据通过fputcsv写到文件句柄  
        fputcsv($fp, $head);
		// 输出Excel内容  
        foreach ($list as $one) {  
            $row = array();  
            foreach ($one as $j => $v) {  
                //$row[$j] = iconv('GBK', 'utf8', $v);
				$row[$j] =  $v;
            }
            fputcsv($fp, $row);  
        }  
		fclose($fp);
	}	
	private function gamelist(){
        $gamelist = S('gamelist');
        if (!$gamelist) {
            $gamelist = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,gamename')->order('gid desc')->select();
            S('gamelist', $gamelist, 3600);
        }
        return $gamelist;
    }
}