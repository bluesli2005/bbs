<?php
/**
 * WeCenter Framework
 *
 * An open source application development framework for PHP 5.2.2 or newer
 *
 * @package     WeCenter Framework
 * @author      WeCenter Dev Team
 * @copyright   Copyright (c) 2011 - 2014, WeCenter, Inc.
 * @license     http://www.wecenter.com/license/
 * @link        http://www.wecenter.com/
 * @since       Version 1.0
 * @filesource
 */

/**
 * WeCenter APP 函数类
 *
 * @package     WeCenter
 * @subpackage  App
 * @category    Model
 * @author      WeCenter Dev Team
 */

require_once(AWS_PATH . 'slidecaptcha/geetestlib.php');

if (!defined('IN_ANWSION'))
{
    die;
}

class tools_class extends AWS_MODEL
{
    public function checkSmsCode($mobile,$chk){
        $sms = AWS_APP::session()->send_info[$mobile];
        if($sms['mobile'] == $mobile){
            if(time() > $sms['expire']){
                H::ajax_json_output(AWS_APP::RSM(null, -1, "短信验证码已过期"));
            }
            if($sms['code'] != $chk){
                H::ajax_json_output(AWS_APP::RSM(null, -1, "短信验证码输入有误"));
            }
            if($sms['type']==1)
                return true;
            else
                H::ajax_json_output(AWS_APP::RSM(null, 1, 'ok'));
        }else{
            H::ajax_json_output(AWS_APP::RSM(null, -1, "接收验证码手机号和输入手机号不一致"));
        }
    }

    public function save_note($mobile, $send_type, $template_code, $content)
    {
        $ip_address = fetch_ip();
        $now = time();
        $to_save_note = array(
            'mobile' => $mobile,
            'send_type' => $send_type,
            'template_code' => $template_code,
            'content' => $content,
            'ip' => ip2long($ip_address),
            'add_time' => $now,
        );
        
        return $this->insert('notes', $to_save_note);
    }

    public function geetest($data){
        $GtSdk = new GeetestLib(get_setting('geetest_id'), get_setting('geetest_key'));
        $geetest_data = array(
            "user_id" => AWS_APP::session()->user_id, # 网站用户id
            "client_type" => $data['client_type'], #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address" => fetch_ip() # 请在此处传输用户请求验证时所携带的IP
        );
        
        if ($data['gt_status'] == 1 || AWS_APP::session()->gtserver == 1) {   //服务器正常
            $result = $GtSdk->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $geetest_data);
            if (!$result) {
                return false;
            }
        }else{  //服务器宕机,走failback模式
            if (!$GtSdk->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode'])) {
                return false;
            }
        }

        return true;
    }
}