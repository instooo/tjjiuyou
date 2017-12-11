<?php
/**
 * 控制器
 * Created by qinfan qf19910623@gmail.com.
 * Date: 2017/1/4
 */
namespace Admin\Controller;
use Think\Controller;

class TestController extends Controller {


    //生成用户数据
    public function addmember() {
        $clist = M('channel','mygame_','DB_CONFIG_ZHU')->select();
        foreach ($clist as $val) {
            if ($val['pid'] > 0) {
                $clist2[] = $val;
            }else {
                $clist1[] = $val;
            }
        }
        $clist = array_column($clist, 'id');
        $clist1 = array_column($clist1, 'id');
        $clist2 = array_column($clist2, 'id');

        $game = M('game','mygame_','DB_CONFIG_ZHU')->select();
        $gameArr = array_column($game,'gid');
        $osArr = array('android','ios');
        for ($i=1;$i<=100;$i++) {
            $info = M('member')->order('uid desc')->limit(1)->select();
            $data = array();
            $data['uid'] = $info[0]['uid']?$info[0]['uid'] + $i:1 + $i;
            $data['username'] = \Org\Util\String::randString(8);
            $data['gid'] = $gameArr[mt_rand(0,count($gameArr)-1)];
            $data['device_id'] = \Org\Util\String::randString(15);
            $data['ip'] = '127.0.0.1';
            $data['channel'] = $clist[mt_rand(0,count($clist)-1)];
            $data['register_time'] = mt_rand(1482595200,1483929949);
            $data['os'] = $osArr[mt_rand(0,count($osArr)-1)];
            $r = M('member','mygame_','DB_CONFIG_ZHU')->add($data);
        }

    }

    //生成登录数据
    public function login() {
        $count = M('member')->count();
        for ($i=0;$i<100;$i++) {
            $minfo = M('member')->limit(mt_rand(0,$count-1), 1)->select();
            $data = array();
            $data['uid'] = $minfo[0]['uid'];
            $data['username'] = $minfo[0]['username'];
            $data['gid'] = $minfo[0]['gid'];
            $data['device_id'] = $minfo[0]['device_id'];
            $data['ip'] = '127.0.0.1';
            $data['channel'] = $minfo[0]['channel'];
            $data['login_time'] = mt_rand($minfo[0]['register_time'], time());
            $data['logout_time'] = mt_rand($data['login_time'], time());
            $data['activity_time'] = $data['logout_time'] - $data['login_time'];
            M('login_log','mygame_','DB_CONFIG_ZHU')->add($data);
        }
    }

    //生成充值数据
    public function pay() {
        $count = M('member','mygame_','DB_CONFIG_ZHU')->count();
        for ($i=0;$i<100;$i++) {
            $minfo = M('member','mygame_','DB_CONFIG_ZHU')->limit(mt_rand(0,$count-1), 1)->select();
            $data = array();
            $data['uid'] = $minfo[0]['uid'];
            $data['username'] = $minfo[0]['username'];
            $data['gid'] = $minfo[0]['gid'];
            $data['order_id'] = \Org\Util\String::randString(10);
            $data['pay_money'] = mt_rand(10,1000);
            $data['channel'] = $minfo[0]['channel'];
            $data['device_id'] = $minfo[0]['device_id'];
            $data['ip'] = '127.0.0.1';
            $data['pay_time'] = mt_rand($minfo[0]['register_time'],time());
            $data['register_time'] = $minfo[0]['register_time'];
            $data['register_ip'] = '127.0.0.1';
            M('pay_log','mygame_','DB_CONFIG_ZHU')->add($data);
        }
    }

    //生成点击数据
    public function clicklog() {
        $clist = M('channel','mygame_','DB_CONFIG_ZHU')->select();
        for ($i=0;$i<100;$i++) {
            $data = array();
            $data['cps_name'] = $clist[mt_rand(0,count($clist)-1)]['short_name'];
            $data['ip'] = '127.0.0.1';
            $data['addtime'] = mt_rand(1483632000,1483669511);
            M('click_log','mygame_','DB_CONFIG_ZHU')->add($data);
        }
    }

    //生成安装记录数据
    public function install_log() {
        $count = M('member','mygame_','DB_CONFIG_ZHU')->count();
        //$clist = M('channel')->select();
        $game = M('game')->select();
        $gameArr = array_column($game,'gid');
        $osArr = array('android','ios');
        for ($i=0;$i<100;$i++) {
            $minfo = M('member','mygame_','DB_CONFIG_ZHU')->limit(mt_rand(0,$count-1), 1)->select();
            $data = array();
            $data['gid'] = $gameArr[mt_rand(0,count($gameArr)-1)];
            $data['device_id'] = $minfo[0]['device_id'];
            $data['ip'] = '127.0.0.1';
            $data['channel'] = $minfo[0]['channel'];
            $data['install_time'] = mt_rand($minfo[0]['register_time'],time());
            $data['os'] = $osArr[mt_rand(0,count($osArr)-1)];
            M('install','mygame_','DB_CONFIG_ZHU')->add($data);
        }
    }

}