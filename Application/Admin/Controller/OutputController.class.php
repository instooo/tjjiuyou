<?php
namespace Admin\Controller;
use Org\Util\Rbac;
use \Think\Controller;
use Think\Crypt\Driver\Think;

class OutputController extends CommonController {
	public function _initialize() {
        parent::_initialize();
    }

    public function down_output(){
		$channel = $this->getChannel();
		$secChannel = $channel['sec_channel'];
		$sec_data = array();
		$sec_data = array_column($secChannel,'name');
		$this->assign('sec_data',json_encode($sec_data));
        //时间处理
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
        //查询游戏名及id
        $gamelist = $this->getUserGames();

        $this->assign('start', $start);
        $this->assign('end', $end);
		$this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);
        $this->assign('gamelist', $gamelist);
		$this->display();	
	}
	
	public function reg_output(){
		$channel = $this->getChannel();
		$secChannel = $channel['sec_channel'];
		$sec_data = array();
		$sec_data = array_column($secChannel,'name');
		$this->assign('sec_data',json_encode($sec_data));
        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);

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

        //查询游戏名及id
        $gamelist = $this->getUserGames();
        $this->assign('gamelist', $gamelist);
		$this->display();	
	}

    public function trial_output(){
        $channel = $this->getChannel();
        $secChannel = $channel['sec_channel'];
        $sec_data = array();
        $sec_data = array_column($secChannel,'name');
        $this->assign('sec_data',json_encode($sec_data));
        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);

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

        //查询游戏名及id
        $gamelist = $this->getUserGames();
        $this->assign('gamelist', $gamelist);
        $this->display();
    }

	public function real_output(){
		$channel = $this->getChannel();
		$secChannel = $channel['sec_channel'];
		$sec_data = array();
		$sec_data = array_column($secChannel,'name');
		$this->assign('sec_data',json_encode($sec_data));
		
        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);

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

        //查询游戏名及id
        $gamelist = $this->getUserGames();
        $this->assign('gamelist', $gamelist);
		$this->display();	
	}

	/**
	 * 注册充值导出
	 * */
	public function regpay_output() {
        $channel = $this->getChannel();
        $secChannel = $channel['sec_channel'];
        $sec_data = array();
        $sec_data = array_column($secChannel,'name');
        $this->assign('sec_data',json_encode($sec_data));

        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);

        $nowtime = time();

        $start = date('Y-m-d', $nowtime);
        $end = date('Y-m-d', $nowtime);

        $reg_start = date('Y-m-d', $nowtime);
        $reg_end = date('Y-m-d', $nowtime);

        $this->assign('start', $start);
        $this->assign('end', $end);

        $this->assign('reg_start', $reg_start);
        $this->assign('reg_end', $reg_end);

        //查询游戏名及id
        $gamelist = $this->getUserGames();
        $this->assign('gamelist', $gamelist);
        $this->display();
    }

	public function dailyReg_output(){
		$start = date('Y-m-d');
		$end = date('Y-m-d');
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->display();
	}
	public function backTrend_output(){
		$same_cps_account = $this->same_cps_account;
		$this->assign ( 'same_cps_account', $same_cps_account );
		$game = M('game','mygame_','DB_CONFIG_CHONG');
        //查询游戏名及id
        $gamelist = $this->getUserGames();
		$this->assign('game_list',$gamelist);
		//时间
		$start = date('Y-m-d');
		$end = date('Y-m-d');
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->display();
	}
	/*二级下线充值回款统计表*/
	public function sub_output(){	
		$sub_account = $this->sub_account;
		$this->assign ( 'sub_account', $sub_account );
		//时间
		$start = date('Y-m-d');
		$end = date('Y-m-d');
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->display();
	}

	/**
	 * LTV指标导出
	 * */
	public function ltv_output() {
        $channel = $this->getChannel();
        $secChannel = $channel['sec_channel'];
        $sec_data = array();
        $sec_data = array_column($secChannel,'name');
        $this->assign('sec_data',json_encode($sec_data));
        $this->assign('sub_channel', $channel['sub_channel']);
        $this->assign('sec_channel', $channel['sec_channel']);

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

        //查询游戏名及id
        $gamelist = $this->getUserGames();
        $this->assign('gamelist', $gamelist);
        $this->display();
    }
}