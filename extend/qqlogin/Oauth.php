<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

// require_once(CLASS_PATH."Recorder.class.php");
// require_once(CLASS_PATH."URL.class.php");
// require_once(CLASS_PATH."ErrorCase.class.php");
namespace extend\qqlogin;
class Oauth{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    // protected $recorder;
    public $urlUtils;
    public $state;
    // protected $error;
    public $appid = "10***87";
    public $callback = "http://www.***.cn/index/user/qqcallback";
    public $scope = "get_user_info";
    

    function __construct(){
        // $this->recorder = new Recorder();
        $this->urlUtils = new URL();
        // $this->error = new ErrorCase();
    }

    public function qq_login(){
        

        //-------生成唯一随机串防CSRF攻击
        $this->state = md5(uniqid(rand(), TRUE));
        // $this->recorder->write('state',$state);
        session('state',$this->state);

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->appid,
            "redirect_uri" => $this->callback,
            "state" => $this->state,
            "scope" => $this->scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        return $login_url;
        // header("Location:$login_url");
    }

    public function qq_callback(){
        // $state = $this->recorder->read("state");

        //--------验证state防止CSRF攻击

        if(input('state') != session('state')){
            // $this->error("30001");
            exit('30001');
        }

        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->appid,
            "redirect_uri" => urlencode($this->callback),
            "client_secret" => '286***e3f4020e',
            "code" => input('get.code')
        );

        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            // if(isset($msg->error)){
            //     $this->error->showError($msg->error, $msg->error_description);
            // }
        }
// dump($response);
        $params = array();
        parse_str($response, $params);
// dump($params);
// dump(input('session.'));
// die;
        // $this->recorder->write("access_token", $params["access_token"]);
        // return $params["access_token"];
        session('access_token',$params["access_token"]);

    }

    public function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => session('access_token')
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        // if(isset($user->error)){
        //     $this->error->showError($user->error, $user->error_description);
        // }

        //------记录openid
        // $this->recorder->write("openid", $user->openid);
        // return $user->openid;
        session('openid',$user->openid);
    }
}
