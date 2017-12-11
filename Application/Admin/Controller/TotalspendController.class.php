<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\ArrayTree;

class TotalspendController extends CommonController {
    //实时注册统计
    public function regter(){
        print_r($_POST);die;
        ini_set('memory_limit','256M');
        $meminfo_cps = M('member_extend_info_cps', 'mygame_', 'DB_CONFIG_CHONG')
            ->field('mygame_member_extend_info_cps.*,mygame_member_cps.username,mygame_member_cps.nickname,mygame_member_cps.email,mygame_member_cps.user_status')
            ->join('LEFT JOIN mygame_member_cps ON mygame_member_cps.uid = mygame_member_extend_info_cps.uid')
            ->where(array('mygame_member_extend_info_cps.uid'=>$this->topuid))->find();

        $this->assign ( 'sub_account', $this->sub_account );
        $this->assign ( 'same_cps_account', $this->same_cps_account );

        $order = $_REQUEST['order']?$_REQUEST['order']:'register_time';

        $map = array();
        if ($_REQUEST['gid']) {
            $map['mi.gid'] = $_REQUEST['gid'];
            $this->assign('gid', $_REQUEST['gid']);
        }

        if ($meminfo_cps['subsign'] != 0) {
            $map['mi.sub_channels'] = $meminfo_cps['uid'];
        }else {
            $map['mi.total_channels'] = $meminfo_cps['uid'];
        }

        if (!empty($_REQUEST['subsign'])) {
            $map['mi.sub_channels'] = $_REQUEST['subsign'];
        }
        if (empty($_REQUEST ['subsign']) && isset( $_REQUEST ['subsign1'] ) && $_REQUEST ['subsign1'] != '') {
            $condition = array();
            $condition['username'] = array('like', '%'.$_REQUEST['subsign1'].'%');
            $cpsresult = M('member_cps', 'mygame_', 'DB_CONFIG_CHONG')->where($condition)->select();
            if ($cpsresult) $map['mi.sub_channels'] = array('in', array_column($cpsresult, 'uid'));
        }

        //注册时间
        $nowtime = time();
        $nowtime_start = strtotime(date('Y-m-d', $nowtime).' 00:00:00');
        $this->assign('nowtime', date('Y-m-d H:i:s', $nowtime));
        $this->assign('nowtime_start', date('Y-m-d', $nowtime).' 00:00:00');
        $start = $_REQUEST['start']?urldecode($_REQUEST['start'].' 00:00:00'):date('Y-m-d').' 00:00:00';
        $end = $_REQUEST['end']?urldecode($_REQUEST['end'].' 23:59:59'):date('Y-m-d H:i:s');
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        if ($_REQUEST['start'] || $_REQUEST['end']) $getdata = true;
        $map['mi.register_time'] = array('between', array($start_time, $end_time));
        $this->assign('start', date('Y-m-d', $start_time));
        $this->assign('end', date('Y-m-d', $end_time));

        //$is_old = $_REQUEST['is_old'];
        $is_new = C('IS_NEW');
        $getdata = $_REQUEST['is_submit'];
        while ($getdata) { //初次打开页面时不查询数据
            $pagesize = $_REQUEST['pagesize']?$_REQUEST['pagesize']:20;
            $this->assign('pagesize', $pagesize);
            if ($start_time >= $nowtime_start || !$is_new) {
                //如果查询内容包含今天的，则查主库
                $count = M('member_extend_info mi', 'mygame_', 'DB_CONFIG_CHONG')
                    ->join('LEFT JOIN mygame_member m ON m.uid=mi.uid')
                    ->where($map)
                    ->count();
                $page = new \Think\Page($count, $pagesize);
                $list = M('member_extend_info mi', 'mygame_', 'DB_CONFIG_CHONG')
                    ->join('LEFT JOIN mygame_member m ON m.uid=mi.uid')
                    ->where($map)
                    ->order('mi.'.$order.' desc')
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }elseif ($start_time < $nowtime_start && $end_time >= $nowtime_start) {
                $map1 = $map;
                $map2 = $map;
                unset($map1['mi.register_time']);
                unset($map2['mi.register_time']);
                $map1['mi.register_time'] = array('between', array($nowtime_start, $end_time));
                $map2['mi.register_time'] = array('between', array($start_time, $nowtime_start-1));

                $count1 = M('member_extend_info mi', 'mygame_', 'DB_CONFIG_CHONG')
                    ->join('LEFT JOIN mygame_member m ON m.uid=mi.uid')
                    ->where($map1)
                    ->count();
                $year1 = (int)date('Y', $start_time);
                $year2 = (int)date('Y', $nowtime_start-1);
                if ($year1 == $year2) {
                    $table = M('members_'.$year1.' mi');
                    $count2 = $table->where($map2)->count();
                }elseif ($year2 - $year1 == 1) {
                    $table1 = 'tongji_members_'.$year1.' mi';
                    $table2 = 'tongji_members_'.$year2.' mi';
                    $m = new Model();
                    $countArr = $m->field('count(mi.id) as count')
                        ->table($table1)
                        ->union(array('field'=>'count(mi.id) as count','table'=>$table2,'where'=>$map2), true)
                        ->where($map2)
                        ->select();
                    $count2 = $countArr[0]['count'] + $countArr[1]['count'];
                }else {
                    break;
                }
                $count = $count1 + $count2;
                $page = new \Think\Page($count, $pagesize);

                if ( ($page->firstRow + $page->listRows - 1) <= $count1) {
                    //当页数据全在mygame_member_extend_info
                    $list = M('member_extend_info mi', 'mygame_', 'DB_CONFIG_CHONG')
                        ->join('LEFT JOIN mygame_member m ON m.uid=mi.uid')
                        ->where($map1)
                        ->order('mi.'.$order.' desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }elseif ( ($page->firstRow + 1) > $count1 ) {
                    //当页数据全在tongji_members
                    $limit = ($page->firstRow - $count1).','.$page->listRows;
                    if ($year1 == $year2) {
                        //都在同一年
                        $table = M('members_'.$year1.' mi');
                        $list = $table->where($map2)
                            ->order('mi.'.$order.' desc')
                            ->limit($limit)
                            ->select();
                    }else {
                        //跨年
                        $table1 = 'tongji_members_'.$year1.' mi';
                        $table2 = 'tongji_members_'.$year2.' mi';
                        $list_sql = $m->field('mi.*')
                            ->table($table1)
                            ->union(array('field'=>'mi.*','table'=>$table2,'where'=>$map2), true)
                            ->where($map2)
                            ->select(false);
                        $list = $m->table('('.$list_sql.') a')->limit($limit)->order('a.'.$order.' desc')->select();
                    }

                }else {
                    //当页数据两边都有
                    $listRows1 = $count1 - $page->firstRow;
                    $listRows2 = $page->listRows - $listRows1;

                    $list1 = M('member_extend_info mi', 'mygame_', 'DB_CONFIG_CHONG')
                        ->field('mi.uid,mi.register_time,mi.register_ip,mi.total_channels,mi.sub_channels,mi.gid,mi.sid,mi.subsign,m.username')
                        ->join('LEFT JOIN mygame_member m ON m.uid=mi.uid')
                        ->where($map1)
                        ->order('mi.register_time desc')
                        ->limit($page->firstRow.','.$listRows1)
                        ->select();

                    if ($year1 == $year2) {
                        //都在同一年
                        $table = M('members_'.$year1.' mi');
                        $list2 = $table->where($map2)
                            ->order('mi.'.$order.' desc')
                            ->limit('0,'.$listRows2)
                            ->select();
                    }else {
                        //跨年
                        $table1 = 'tongji_members_'.$year1.' mi';
                        $table2 = 'tongji_members_'.$year2.' mi';
                        $list_sql = $m->field('mi.*')
                            ->table($table1)
                            ->union(array('field'=>'mi.*','table'=>$table2,'where'=>$map2), true)
                            ->where($map2)
                            ->select(false);
                        $list2 = $m->table('('.$list_sql.') a')->limit('0,'.$listRows2)->order('a.'.$order.' desc')->select();
                    }
                    $list = array_merge($list1, $list2);
                }

            }else {
                $start_year = (int)date('Y', $start_time);
                $end_year = (int)date('Y', $end_time);
                if ($start_year == $end_year) {
                    $table = M('members_'.$start_year.' mi');
                    $count = $table->where($map)->count();
                    $page = new \Think\Page($count, $pagesize);
                    $list = $table->where($map)->order('mi.'.$order.' desc')->limit($page->firstRow.','.$page->listRows)->select();
                }elseif ($end_year - $start_year == 1) {
                    $start_table = 'tongji_members_'.$start_year.' mi';
                    $end_table = 'tongji_members_'.$end_year.' mi';

                    $m = new Model();
                    $countArr = $m->field('count(mi.id) as count')
                        ->table($start_table)
                        ->union(array('field'=>'count(mi.id) as count','table'=>$end_table,'where'=>$map), true)
                        ->where($map)
                        ->select();
                    $count = $countArr[0]['count'] + $countArr[1]['count'];
                    $page = new \Think\Page($count, $pagesize);
                    $list_sql = $m->field('mi.*')
                        ->table($start_table)
                        ->union(array('field'=>'mi.*','table'=>$end_table,'where'=>$map), true)
                        ->where($map)
                        ->select(false);
                    $list = $m->table('('.$list_sql.') a')->limit($page->firstRow.','.$page->listRows)->order('a.'.$order.' desc')->select();
                }else {
                    break;
                }
            }
            $page->setConfig('theme', '<i style="font-size: 14px;background-color: #FFF;padding: 2px 12px;display: inline-block">共%TOTAL_ROW%条</i> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $this->assign('count', $count);
            if ($page) $this->assign('pagebar', $page->show());

            if (!$list) break;
            //游戏区服、推广用户名等补充信息
            $member_cps_arr = array();
            $member_cps_arr[$meminfo_cps['uid']] = $meminfo_cps['username'];
            foreach ($this->sub_account as $val) {
                $member_cps_arr[$val['uid']] = $val['username'];
            }
            $gameresult = M('game', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,gamename')->where(array('gid'=>array('in', array_column($list, 'gid'))))->select();
            $serverresult = M('server', 'mygame_', 'DB_CONFIG_CHONG')->field('gid,sid,servername')->where(array('sid'=>array('in', array_column($list, 'sid'))))->select();
            $serverresultnew = array();
            foreach ($serverresult as $val) {
                $serverresultnew[$val['sid']] = $val['servername'];
            }
            $gameresultnew = array();
            foreach ($gameresult as $val) {
                $gameresultnew[$val['gid']] = $val['gamename'];
            }
            //角色等级
            $rolemap = array();
            $rolemap['uid'] = array('in', array_column($list, 'uid'));
            $rolemap['gid'] = array('in', array_unique(array_column($list, 'gid')));
            $rolelist = M('role', 'mygame_', 'DB_CONFIG_CHONG')
                ->field('uid,gid,level,role')
                ->where($rolemap)
                ->order('level asc')
                ->select();
            $role = array();
            foreach ($rolelist as $val) {
                $role[$val['uid'].'|'.$val['gid']] = $val['level'];
            }
            Vendor('IP.IP');
            foreach ($list as $key=>$val) {
                $list[$key]['username1'] = $member_cps_arr[$val['sub_channels']];
                $list[$key]['username2'] = $member_cps_arr[$val['total_channels']];
                $list[$key]['gamename'] = $gameresultnew[$val['gid']];
                $list[$key]['servername'] = $serverresultnew[$val['sid']]?$serverresultnew[$val['sid']]:'未知';
                $role_k = $val['uid'].'|'.$val['gid'];
                $list[$key]['level'] = isset($role[$role_k])?$role[$role_k]:'0';
                $ipArr = \IP::find($val['register_ip']);
                $list[$key]['iplocation'] = $ipArr[1].$ipArr[2];
            }
            $obj = new ChannelController();
            $list = $obj->getReferrer($list);
            $this->assign('list', $list);

            break;
        }

        //游戏列表
        $gamelist = $this->gamelist();
        $this->assign('gamelist', $gamelist);

        if ($_REQUEST['subsign1']) $this->assign('subsign1', $_REQUEST['subsign1']);
        $this->assign('subsign', $_REQUEST['subsign']);
        $this->assign('flag', $meminfo_cps['subsign']);

        $this->display();

    }	


}