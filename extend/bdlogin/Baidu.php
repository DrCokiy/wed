<?php
namespace extend\baidu;

class Baidu
{
	/**
	* 从百度开发者平台申请到的两个东西
	* @var string
	*/
	protected $api_key = 'ihPIcvNv***OhWN';
	protected $serect_key = '9sP8ph***Vr62kG';
	/**
	 * 回调地址，输入账号信息之后百度会返回到这个页面一个code，下一步需要拿这个code去百度api接口换取acess token
	 * @var string
	 */
	protected $redirect_url = 'http://www.***.cn/index/user/bdcallback';
	/**
	 * 百度开放平台基础地址，之后所有操作都在这个平台之上
	 * @var string
	 */
	protected $apibase_url = 'https://openapi.baidu.com/rest/2.0/';
	/**
	 * 退出的时候，需要到百度注销已授权的账号信息，注销完百度将自动重定向到这个页面
	 * @var string
	 */
	protected $logout = 'http://www.***.cn';
	protected $accesstoken;

	public function login()
	{
		// 返回百度登录的页面地址
		return $getcode_url = "http://openapi.baidu.com/oauth/2.0/authorize?response_type=code&client_id=" . $this->api_key . "&redirect_uri= " . $this->redirect_url;
	}

	public function callback()
	{
		$code = input('code');
		if($code){
			// 换取access token的地址
		    $getaccesstoken_url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=authorization_code&code=" . $code . "&client_id=" . $this->api_key . "&client_secret=" . $this->serect_key . "&redirect_uri=" . $this->redirect_url;

		    $https_res = $this->getdata($getaccesstoken_url);
		    // 从数组中提取access token
		    $this->accesstoken = $https_res['access_token'];
		    // 拿access token获得用户uid,用户名uname,用户头像portrait
		    // 要获取更多用户信息，把getLoggedInUser换成getInfo
		    $userinfo_url = $apibase_url.'passport/users/getInfo'.'?access_token='.$this->accesstoken;
		    $user_res = $this->getdata($userinfo_url);

		    return $user_res;
		} else {
			exit('获取code时发生了一个错误');
		}
	}

	public function logout()
	{
		return $logout_url = "https://openapi.baidu.com/connect/2.0/logout?access_token=" . $this->accesstoken . "&next=". $this->logout;
	}

	protected function getdata($url)
	{
		// 初始化一个cURL会话
        $ch = curl_init();
        // 设为false后cURL将终止从服务端进行验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        // 需要获取的URL地址
        curl_setopt($ch,CURLOPT_URL,$url);
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        //执行一个cURL会话,成功时返回 TRUE， 或者在失败时返回 FALSE
        $temp = curl_exec($ch);
        if($temp === FALSE){
            return false;
        }
        // json格式解析出数组
        $output = json_decode($temp,TRUE);
        curl_close($ch);
        return $output;
	}

}