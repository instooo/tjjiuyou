<?php
/**
 * API接口基类
 * @author DaiLinLin
 * @config
 * return array(
		//'配置项'=>'配置值'
		"appid"=>"test",
		'appsecert'=> 'test',
		'appip'=>'',//允许访问的IP 为空不限制
		);
 */

namespace Api\Controller;
use Org\Util\Response;
use Think\Controller;


class ApiController extends Controller {


	/**
	 * 获取秘钥
	 * @param $gid
	 * @return bool
	 */
	protected function getSecret($gid){
		$game = M('game')->where(array('gid'=>$gid,'status'=>1))->find();

		if($game){
			return $game['secret'];
		}else{
			Response::apiReturn('406','游戏不存在');
		}
	}

	protected function getChannel($channel){
		$channel = M('channel')->where(array('short_name'=>$channel,'status'=>1))->find();
		if($channel){
			return $channel;
		}else{
			return 0;
		}
	}

}
	
