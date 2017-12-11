<?php
/**
 * Created by dh2y.
 * bolg: http://blog.csdn.net/sinat_22878395
 * Date: 2017/7/7 14:42
 * functional: 渠道广告点击通知
 */

namespace Api\Model;


use Think\Log;

class ClickNotify
{

    /**
     * 通知
     * @param $extend
     * @param null $channel 广告商
     */
    public function notify($extend,$channel=null){
        switch ($channel){
            case 'jrtt': $this->jrtt($extend);break;
            case 'gdt': $this->gdt($extend);break;
            case 'momo': $this->momo($extend);break;
            default :$this->jrtt($extend);$this->gdt($extend);break;
        }
    }

    /**
     * 今日头条通知
     */
    public function jrtt($extend){
        if($extend['imei']!=''&&$extend['mac']!=''&&$extend['androidid']!=''){
            M('install_extend')->data($extend)->add();

            //当前激活信息和今日头条广告点击信息匹配
            if($extend['imei']){
                $map['imei']= md5($extend['imei']);
            }
            if($extend['mac']){
                $map['mac']=md5($extend['mac']);
            }
            if($extend['androidid']){
                $map['androidid']=$extend['androidid'];
            }
            $map['gid']=$extend['gid'];
            if($extend['imei'] || $map['mac'] || $map['androidid'] ){
                $result =  M('click_jrtt_app')->where($map)->find();
            }
            if($result){
                file_get_contents($result['callback_url']);
            }
        }
    }

    /**
     * 腾讯广点通通知
     */
    public function gdt($extend, $conv_type = 'MOBILEAPP_ACTIVITE'){
        $dh2y =($extend['os']=='android')?'strtolower':'strtoupper';

        //根据muid gid得到点击记录
        $map['muid'] = md5($dh2y($extend['imei']));
        $map['gid'] = $extend['gid'];
        //$map['conv_type'] = $conv_type;
        $result =  M('click_gdt_app')->where($map)->find();

        if($result){
            $gdtconfig = C('TENCENT_GDT');
            $config = $gdtconfig[$result['advertiser_id']];

            //构建query_string
            $query_string_params['click_id']=$result['click_id'];
            $query_string_params['muid']=$result['muid'];
            $query_string_params['conv_time']=time();
            $query_string = http_build_query($query_string_params);

            //构建page
            $page = 'http://t.gdt.qq.com/conv/app/'.$result['appid'].'/conv?'.$query_string;

            //构建encode_page
            $encode_page = urlencode($page);

            //组装property
            $sign_key =$config['sign_key'];//（运营给）
            $property = $sign_key.'&GET&'.$encode_page;

            //生成signature
            $signature = md5($property);

            //生成base_data
            $base_data = $query_string.'&sign='.urlencode($signature);

            //生成data
            $encrypt_key = $config['encrypt_key'];//（运营给）
            $data = base64_encode($this->simple_xor($base_data,$encrypt_key));

            //构建attachment
            //$attachment_params['conv_type'] = 'MOBILEAPP_ACTIVITE';
            $attachment_params['conv_type'] = $conv_type;
            $attachment_params['app_type'] = strtoupper($result['app_type']);
            $attachment_params['advertiser_id'] = $result['advertiser_id'];
            $attachment = http_build_query($attachment_params);

            //构建最终请求
            $url = 'http://t.gdt.qq.com/conv/app/'.$result['appid'].'/conv?v='.$data.'&'.$attachment;
            $gdt = file_get_contents($url);

            if(!is_dir(LOG_PATH.'notify')){
                @mkdir(LOG_PATH.'notify');
            }
            Log::write($gdt,'INFO','',LOG_PATH.'notify/'.date('y_m_d').'.log');
        }else{
            Log::write(json_encode($map),'ERR','',LOG_PATH.'notify/'.date('y_m_d').'.log');
        }
    }

    /**
     * 陌陌回调通知
     * @param $extend
     * @return bool
     */
    public function momo($extend) {
        $map = array();
        $map['gid'] = $extend['gid'];
		$extend['os'] = strtolower($extend['os']);
        if ($extend['os'] == 'android') {
            $map['muid'] = md5($extend['imei']);
        }elseif ($extend['os'] == 'ios') {
            $map['muid'] = strtolower($extend['device']);
        }else {
            return false;
        }

        $log = M('click_momo_app')->where($map)->order('click_time desc')->select();
        if(!is_dir(LOG_PATH.'notify')){
            @mkdir(LOG_PATH.'notify');
        }

        if (!$log) {
            Log::write(json_encode($map),'ERR','',LOG_PATH.'notify/momo_'.date('y_m_d').'.log');
            return false;
        }
        $callback_url = urldecode($log[0]['callback']);
        $result = file_get_contents($callback_url);

        Log::write($result,'INFO','',LOG_PATH.'notify/momo_'.date('y_m_d').'.log');
        return true;
    }

    /**
     * uc回调通知
     * @param $extend
     * @return bool
     */
    public function uc($extend) {
        $map = array();
        $map['gid'] = $extend['gid'];
        $extend['os'] = strtolower($extend['os']);
        if ($extend['os'] == 'android') {
            $map['muid'] = strtoupper(md5($extend['imei']));
        }elseif ($extend['os'] == 'ios') {
            $map['muid'] = strtoupper($extend['device']);
        }else {
            return false;
        }
        $log = M('click_third_app')->where($map)->order('click_time desc')->select();
        if(!is_dir(LOG_PATH.'notify')){
            @mkdir(LOG_PATH.'notify');
        }
        if (!$log) {
            return false;
        }
        if ($log[0]['media'] != 'uc') {
            Log::write(json_encode($map),'ERR','',LOG_PATH.'notify/uc_'.date('y_m_d').'.log');
        }

        $callback_url = urldecode($log[0]['callback']);
        $result = file_get_contents($callback_url);

        Log::write($result,'INFO','',LOG_PATH.'notify/uc_'.date('y_m_d').'.log');
        return true;
    }


    /**
     * 简单异或加密
     * @param $data
     * @param $key
     * @param string $string
     * @return string
     */
    function simple_xor($data, $key, $string = ''){
        $len = strlen($data);
        $len2 = strlen($key);
        for($i = 0; $i < $len; $i ++){
            $j = $i % $len2;
            $string .= ($data[$i]) ^ ($key[$j]);
        }
        return $string;
    }

}