<?php
/**
 * 实时数据查询控制器
 * Created by qinfan qf19910623@gmail.com.
 * Date: 2017/1/4
 */
namespace Third\Controller;
use Org\Util\Rbac;
use \Think\Controller;
use Think\Crypt\Driver\Think;

class ThirddataController extends Controller {
	 public function __construct() {
		  parent::__construct();
		 Rbac::checkLogin();       
	 }
    /**
     * 实时充值
     * */
    public function tongji(){  
		$map['user_id'] = $_SESSION['userid'];
		$channel = M('user_channel')->where($map)->select();
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
		$start_date = date('Ymd', $start_time);
		$end_date = date('Ymd', $end_time);
		$newgmap['date'] = array('between',array($start_date,$end_date));
		$channelarr = array_column($channel,'cid');
		if($channelarr){			
			//查询注册总数
			$newgmap['total_channels'] = array('in',$channelarr );
			$total_register = M('tj_member')->where($newgmap)->sum('register_num');	
			//查询充值总数
			$total_pay = M('tj_pay_bygid')->where($newgmap)->sum('now_money');
			//安装总数
			$total_install = M('tj_install_bygid')->where($newgmap)->sum('install_num');
			
			
			
			//查询试玩注册人数并分页
			$trialmap['p.total_channel'] = array('in',$channelarr );
			$trialmap['p.register_time'] = array('between', array($start_time, $end_time));
			$count = M('member_trial p')	
				->where($trialmap)
				->count();
				
			//查询试玩已激活人数
			$active_count = M('member_trial t')
            ->join('INNER JOIN mygame_member p ON p.uid=t.uid')
            ->where($trialmap)
            ->count();
			$total_register = $total_register+$count-$active_count;			
		}
		echo $count;
		echo $active_count;
		$total_register = $total_register?$total_register:0;
		$total_pay = $total_pay?$total_pay:0;
		$total_install = $total_install?$total_install:0;
		$this->assign('total_register',$total_register);
		$this->assign('total_pay',$total_pay);
		$this->assign('total_install',$total_install);
		
	
        $this->display();
    } 
}