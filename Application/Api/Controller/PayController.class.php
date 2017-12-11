<?php
/**
 * Created by PhpStorm.
 * User: DaiLinLin
 * Date: 2017/1/3
 * Time: 19:58
 * for: 支付相关的接口
 */

namespace Api\Controller;
use Org\Util\ApiHelper;
use Org\Util\Response;
use Api\Model\ClickNotify;

class PayController extends ApiController
{
    /**
     * 充值统计API
     */
    public function paylog(){
        $data['uid'] =$_REQUEST['uid'];
        $data['username'] =$_REQUEST['username'];
        $data['gid'] =$_REQUEST['gid'];
        $data['order_id'] = $_REQUEST['orderid'];
        $data['pay_money'] = $_REQUEST['paymoney'];
        $data['channel'] = $_REQUEST['channel'];
        $data['device_id'] = $_REQUEST['device'];
        $data['time'] = $_REQUEST['time'];
        $data['secret'] = $this->getSecret($data['gid']);
        $sign = $_REQUEST['sign'];

        ApiHelper::timeout($data['time']);
        ApiHelper::mustParams($data);
        ApiHelper::validateSign($data,$sign);
        $data = ApiHelper::dataUnset($data,array('time','secret'));       
       
        $user = M('member')->where(array('uid'=>$data['uid']))->find();        
        if(!$user){
            Response::apiReturn(-103,'用户不存在');
        }	
		$channel = $this->getChannel($data['channel']); //获取媒体
        $data['channel'] = $channel['id']; //获取媒体id
        $data['total_channel'] = $channel['pid']; //获取上级媒体id		
		
        $payed = M('pay_log')->where(array('order_id'=>$data['order_id']))->find();
        if($payed){
            Response::apiReturn(-104,'重复订单');
        }
        $data['ip'] = get_client_ip(0,true);
        $data['pay_time'] = time();
        $data['register_time'] = $user['register_time'];
        $data['register_ip'] = $user['register_ip'];
        $result =  M('pay_log')->data($data)->add();
        if($result){
            Response::apiReturn(200,'充值成功');
        }
    }		/**     * 充值统计API     */    public function paylog_wap(){        $data['uid'] =$_REQUEST['uid'];        $data['username'] =$_REQUEST['username'];        $data['gid'] =$_REQUEST['gid'];        $data['order_id'] = $_REQUEST['orderid'];        $data['pay_money'] = $_REQUEST['paymoney'];              $data['time'] = $_REQUEST['time'];        $data['secret'] = $this->getSecret($data['gid']);        $sign = $_REQUEST['sign'];        ApiHelper::timeout($data['time']);        ApiHelper::mustParams($data);        ApiHelper::validateSign($data,$sign);        $data = ApiHelper::dataUnset($data,array('time','secret'));           $user = M('member')->where(array('uid'=>$data['uid']))->find();                if(!$user){            Response::apiReturn(-103,'用户不存在');        }			$data['channel'] = $user['channel'];        $data['device_id'] = $user['device_id'];				$data['total_channel']  =$user['total_channel'];			        $payed = M('pay_log')->where(array('order_id'=>$data['order_id']))->find();        if($payed){            Response::apiReturn(-104,'重复订单');        }        $data['ip'] = $_REQUEST['ip'];        $data['pay_time'] = time();        $data['register_time'] = $user['register_time'];        $data['register_ip'] = $user['register_ip'];        $result =  M('pay_log')->data($data)->add();        if($result){            Response::apiReturn(200,'充值成功');        }    }
}