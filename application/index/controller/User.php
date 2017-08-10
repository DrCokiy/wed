<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User as UserModel;
use extend\alidayu\TopClient;
use extend\alidayu\AlibabaAliqinFcSmsNumSendRequest;
use extend\qqlogin\QC;
use extend\baidu\Baidu;
use extend\sina\SaeTOAuthV2;
use extend\sina\SaeTClientV2;
use extend\mycurl\MyCurl;


class User extends Controller
{
    protected $model;
    public function _initialize()
    {
        $this->model = new UserModel();
    }
    //注册界面
    public function register()
    {
        return $this->fetch();
    }
    //注册验证
    public function userRegister(UserModel $user)
    {
        $data = input();
        $username = $data['realname'];
        $password = $data['password'];
        $repassword = $data['repassword'];
        $data['regtime'] = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = ($ip == "::1") ? "127.0.0.1" : $ip;
        $data['regip'] = ip2long($ip);
        //各种验证
        if (empty($username)){
            $this->error('用户名不能为空');
            exit;
        }else if (!(strlen($username) > 3 || strlen($username) < 6) ){
            $this->error('用户名不能大于6位，小于3位');
            exit;
        }else{
            $data['realname'] = $username;
        }
        if ($password != $repassword) {
            $this->error('两次输入的密码不一致');
        }else if (empty($password)) {
            exit('密码不能为空');
        }else{
            $data['password'] = md5($password);# code
        }   
        $phone = $data['phone'];
        $pattern ='/^1[3|4|5|8][0-9]\\d{4,8}$/';
        if (!preg_match($pattern , $phone , $matches)) {
            $this->error('手机号不正确');
        } 
        $email = $data['email'];
        $pattern ='/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
        if (!preg_match($pattern , $email , $matches)) {
            $this->error('邮箱格式不正确');
        } 
        $data['email'] = $email;
        $captcha = $data['yzm'];
        if(!captcha_check($captcha)){
          $this->error('验证码输入不正确');
        }
        //验证结束
        unset($data['register']);
        unset($data['repassword']);
        unset($data['yzm']);
        //开始注册
        $res = $user->checkRegister($data);
        //返回值
        if($res){
            $this->success('注册成功','index/index/index');
        }else{
            $this->error('注册失败');
        }

    }
    //显示登陆界面
    public function login()
    {
        return $this->fetch();
    }
    public function doLogin(UserModel $user)
    {
        $pwd = input('post.password');
        $name = input('post.realname');
        $res = $user->nameLogin($name);
        if ($res == null) {
            echo 1;     // 用户名不存在
            die;
        }else{
            $data['realname'] = $name;
            $data['password'] = $pwd;
            $arr = $user->checkLogin($data);
            if (!$arr) {
               echo 2;      //  密码错误
            }else{
                $realname = $arr['realname'];
                $uid = $arr['uid'];
                session('uid',$uid);
                session('username',$realname);
                $ip = $_SERVER['REMOTE_ADDR'];
                $ip = ($ip == "::1") ? "127.0.0.1" : $ip;
                $ip = ip2long($ip);
                $res = $user->updateCar($ip);
                echo 3;     // 登陆成功
            }
        }
    }

    // 处理qq登录
    public function qqlogin()
    {
        $qq = new QC();
        $url = $qq->qq_login();
        $this->redirect($url);
    }

    // qq登录回调函数
    public function qqcallback(UserModel $user)
    {
        $qq = new QC();
        $qq->qq_callback();
        $qq->get_openid();
        $qq = new QC();
        $datas = $qq->get_user_info();
        $data['name'] = $datas['nickname'];
        $data['nage'] = 0;
        $data['pic'] = $datas['figureurl_qq_2'];
        $data['city'] = $datas['city'];
        $data['regtime'] = time();
        $ip = $_SERVER['REMOTE_ADDR'] == '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        $data['regip'] = ip2long($ip);
        $res = $user->addThirdUser($data);
        if ($res) {
            session('uid',$res);
            session('third',1);     // 从qq登录，third写1
            session('username',$data['name']);
            $this->success('登陆成功','/index/index/index');
        } else {
            $this->error('登录失败');
        }
    }

    // 百度登录
    public function bdlogin()
    {
        $baidu = new Baidu();
        $url = $baidu->login();
        // dump($url);
        // die;
        $this->redirect($url);
    }

    // 百度登录回调函数
    public function bdcallback()
    {
        $baidu = new Baidu();
        // var_dump(1345);
        // die;
        $datas = $baidu->callback();
        var_dump($datas);
    }

    // 新浪微博登录
    public function wb_login(SaeTOAuthV2 $sina)
    {
        $url = $sina->getAuthorizeURL();
        $this->redirect($url);
    }

    // 新浪微博回调函数
    public function wb_callback(UserModel $user)
    {
        // 获取code
        $sina = new SaeTOAuthV2();
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = "http://www.geniusbarst.cn/index/user/wb_callback";
            $token = $sina->getAccessToken('code', $keys);  
        } else {
            exit('获取code失败');
        }

        // 获取用户信息
        $c = new SaeTClientV2($token['access_token']);
        $uid_get = $c->get_uid();
        $uid = $uid_get['uid'];
        $user_message = $c->show_user_by_id( $uid);

        // 转换信息准备写进数据库
        $data['name'] = $user_message['screen_name'];
        $data['nage'] = 2;
        $data['pic'] = $user_message['avatar_large'];
        $data['city'] = $user_message['location'];
        $data['regtime'] = time();
        $ip = $_SERVER['REMOTE_ADDR'] == '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        $data['regip'] = ip2long($ip);

        // 写进数据库
        $res = $user->addThirdUser($data);
        if ($res) {
            session('uid',$res);
            session('third',2);     // 从新浪登录，存2
            session('username',$data['name']);
            $this->success('登陆成功','/index/index/index');
        } else {
            $this->error('登录失败');
        }
    }

    // 处理验证码
    public function ajaxYzm()
    {
        $data = input();
        $captcha = $data['yzm'];
        if(!captcha_check($captcha)){
            echo 1;     //验证失败
        } else {
            echo 2;     //验证成功
        }
    }
    public function out(Baidu $baidu)
    {
        
        session(null); 
        $this->success('您已成功退出','index/index/index');
   
    }
    public function phone()
    {
        return  $this->fetch();
    }
     
    public function sendSMS(UserModel $user)
    {
        $data = input();
        $phone = $data['mobile'];//手机号
        //验证手机好是否存在
       
        $c = new TopClient;//大于客户端   
        $c->format = 'json';//设置返回值得类型
        $c->appkey = "24462735";//阿里大于注册应用的key
        $c->secretKey = "4f62fd1d588cf9aedf6c211c306c9cbe";//注册的secretkey                                 
        //请求对象，需要配置请求的参数   
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");//公共回传参数，可以不传
        $req->setSmsType("normal");//短信类型，传入值请填写normal
        //签名，阿里大于-控制中心-验证码--配置签名 中配置的签名，必须填
        $req->setSmsFreeSignName("wuli盼");
        //你在短信中显示的验证码，这个要保存下来用于验证
        $num = rand(100000,999999);
        session('smscode',$num);
        //短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，传参时需传入{"code":"1234","product":"alidayu"}
        $req->setSmsParam("{\"name\":\"五星期\",\"number\":\"$num\"}");//模板参数                                 
        //短信接收号码。
        $req->setRecNum($phone);
        //短信模板。阿里大于-控制中心-验证码--配置短信模板 必须填
        $req->setSmsTemplateCode("SMS_71326104");
        $resp = $c->execute($req);//发送请求
        echo 1;
            // var_dump($resp);
            // $smscode = session('smscode');
            // // dump($smscode);die;
            // if ($smscode == $phone) {
            //     $this->success("验证成功","index/index/index");
            // }else{
            //     $this->error("验证失败");
            // }
                 
    }

    public function checkSms()
    {
        $sms = input('sms');
        if ($sms == session('smscode')) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function news_detail()
    {
        return $this->fetch();
    }
    //查看订单
    public function member_order()
    {
        $res = $this->model->member_order();
        $this->assign('res',$res);
        //dump($res);
        return $this->fetch();
    }///
     public function member_order_detail()
    {
        $data = input();
        $res = $this->model->member_order_detail($data);
        //dump($res);
        // dump($res);
        $orderid = $res[0]['orderid'];
        $payable = $res[0]['payable'];
        $addtime = $res[0]['addtime'];
        $src = $res[0]['src'];
        $address = $res[0]['address'];
        $orderid = $res[0]['orderid'];
        $iname = $res[0]['iname'];
        $colorstr = $res[0]['colorstr'];
        $sizenum = $res[0]['sizenum'];
        $material = $res[0]['material'];
        $gname = $res[0]['gname'];
        //die;
        $this->assign('src',$src);
        $this->assign('sizenum',$sizenum);
        $this->assign('colorstr',$colorstr);
        $this->assign('payable',$payable);
        $this->assign('addtime',$addtime);
        $this->assign('address',$address);
        $this->assign('orderid',$orderid);
        $this->assign('material',$material);
        $this->assign('iname',$iname);
        $this->assign('gname',$gname);
        $this->assign('colorstr',$colorstr);
        //地址信息
        $addr = $this->model->addr($address);
        if ($addr != null) {
            $addcode = $addr[0]['addcode'];
            $phone = $addr[0]['phone'];
            $consignee = $addr[0]['consignee'];
        }else{
            $this->error('地址信息被删除，无法查询快递信息');
        }
        //dump($addr);
        $addcode = $addr[0]['addcode'];
        $phone = $addr[0]['phone'];
        $consignee = $addr[0]['consignee'];
        $this->assign('addcode',$addcode);
        $this->assign('phone',$phone);
        $this->assign('consignee',$consignee);
        return $this->fetch();
    }
    //展示地址信息
    public function member_addr()
    {
        $res = $this->model->member_addr();
        $this->assign('res',$res);
        // dump($res);
        return $this->fetch();
    }
    public function deladdr()
    {
        $id = input();
        $res = json_encode($id);
        // echo $res;
        $con = $this->model->deladdr($id);
        if ($con) {
           echo 1;
        }
    }
    public function member_avatar()
    {
        return $this->fetch();
    }
     public function member_collect(UserModel $user)
    {
        $data = $user->shoucang();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function member_index()
    {

        $res = $this->model->see_good();
        // dump($res);die;
        $this->assign('res',$res);
        return $this->fetch();
    }
    //个人信息验证
     public function doInfo()
    {
        $arr = input();
        $year = $arr['year'];
        $month = $arr['month'];
        $day = $arr['day'];
        $data['birthday'] = $year . '-' . $month . '-' .  $day;
        $data['regtime'] = time();
        $data['email'] = $arr['email'];
        $data['realname'] = $arr['realName'];
        $data['phone'] = $arr['phone'];
        $data['sex'] = $arr['sex'];
        $res = json_encode($data);
        //echo $res;
        $arr = $this->model->checkInfo($data);
        if ($arr) {
            echo 1; //添加成功
        }else{
            echo 2; //添加失败
        }
    }
     public function member_info()
    {
        $res = $this->model->member_info();
        $this->assign('res',$res);
        $birthday = $res['birthday']; 
        $data  = explode('-',$birthday);
        $year = $data['0'];
        $month = $data['1'];
        $day = $data['2'];
        $this->assign('year',$year);
        $this->assign('month',$month);
        $this->assign('day',$day);
         // dump($data);
        // die;
        return $this->fetch();
    }
    
    public function member_pwd()
    {
        return $this->fetch();
    }
    public function member_detail()
    {
        return $this->fetch();
    }
    public function member_car()
    {
        $res = $this->model->member_car();
        $this->assign('res',$res);
        return $this->fetch();
    }
    public function member_showcar()
    {
        $res = $this->model->checkShowcar();
        $this->assign('res',$res);
        $id = input('id');
        session('id',$id);
        return $this->fetch();
    }
    public function doCar()
    {
        $uid = session('uid');
        $arr['uid'] = $uid;
        $data = input();
        $data['start_time'] = time();
        $data['cid'] = session('id');
        $data1 = array_merge($data,$arr);
        $res = json_encode($data1);
        //echo $res;
       
        if ($uid != null) {
            $arr = $this->model->checkCar($data1);
            if($arr){
                echo 1; //添加成功
            }else{
                echo 2; //添加失败
                }
        }else{
            echo 3;  //没有登陆
        }
    }

    //删除租车信息
    public function delCar()
    {
        $data = input();
        $id = json_encode($data);
        
        $res = $this->model->delCar($data);
        if ($res) {
            echo 1;//删除成功
        }else{
            echo 2;//删除失败
        }

    }
    //展示租赁场地信息
    public function member_place()
    {
        $uid = session('uid');
        $res = $this->model->checkArea($uid);
        $id = input('id');
        session('id',$id);
        $this->assign('res',$res);
        return $this->fetch();
    }
    //添加场地信息
    public function doPlace()
    {
        $data = input();
        $uid = session('uid');
        $data['pid'] =session('id');
        $data['uid'] = $uid;
        $res = json_encode($data);
       // echo $res;
        if ($uid != null) {
            $arr = $this->model->checkPlace($data);
            //echo $arr;
            if ($arr) {
                echo 1; //添加成功
            }else{
                echo 2; //添加失败
            }
        }else{
            echo 3;
        }
    }
    public function delPlace()
    {
        $data = input();
        $id = json_encode($data);
        
        $res = $this->model->delPlace($data);
        if ($res) {
            echo 1;//删除成功
        }else{
            echo 2;//删除失败
        }
    }

    public function member_bank()
    {
        return $this->fetch();
    }
    //未付款
    public function member_buy()
    {
        $res = $this->model->momber_buy();
        // dump($res);
        // die;
        $this->assign('res',$res);
        return $this->fetch();
    }
    //删除未付款商品
    public function delwfk()
    {
        $oid = input();
        $res= $this->model->delwfk($oid);
        $res = json_encode($oid);
        //echo $res;
        if ($res) {
           echo 1;
        }
    }
    //已付款
    public function state1()
    {
        $res = $this->model->state1();
        // dump($res);
        // die;
        $this->assign('res',$res);
        return $this->fetch();
    }
    //待收货
    public function state2()
    {
        $res = $this->model->state2();
        // dump($res);
        // die;
        $this->assign('res',$res);
        return $this->fetch();
    }
    //确认收货
    public function state3()
    {
        $res = $this->model->state3();
        // dump($res);
        // die;
        $this->assign('res',$res);
        return $this->fetch();
    }

    public function member_appraise()
    {
        $oid = input('oid');
        //dump($oid);
        $res = $this->model->image($oid);
        //dump($res);die;
        $src = $res[0]['src'];
        $gid = $res[0]['gid'];        
        $this->assign('src',$src);
        $this->assign('gid',$gid);
        //dump($src);
       return $this->fetch();
    }
    //添加评论
    public function addcomment()
    {
        $data = input();
        $data['addtime'] = time();
        $data['uid'] = session('uid');
        $res = json_encode($data);
        //echo $res;
        $con = $this->model->addcomment($data);
        if ($con) {
            echo 1;
        }else{
            echo 2;
        }
        //查看商品是否评价
        // $gid = $data['gid'];
        // $comment = $this->model->comment($gid);
        // $content = json_encode($comment);
        // $this->assign('content',$content);
        // echo $gid;
        
    }
    public function member_sell()
    {
        return $this->fetch();
    }
     public function member_back()
    {
        return $this->fetch();
    }
     public function member_showplace()
    {
        $data = input();
        $id = $data['id'];
        session('id',$id);
        $this->assign('data',$data);
        return $this->fetch();
    }
    //确认收货
    public function bady()
    {
        $oid = input();
        $res = json_encode($oid);
        //echo $res;
        $res = $this->model->bady($oid);
        if ($res == 1) {
            echo 1;
        }

    }
    //添加收获地址
    public function addcar()
    {
        $arr = input();
        $con = json_encode($arr);
        // echo $con;
        $data['address'] =  $arr['province'] . $arr['city'] . $arr['country'] . $arr['address'];
        $data['uid'] = session('uid');
        $data['addtime'] = time();
        $data1 = array_merge($arr,$data);
        $res = json_encode($data1);
        //echo $res;
        $con = $this->model->address($data1);
       // dump($con);
        if ($con) {
            echo 1;
        }else {
            echo 2;
        }
    }
    //修改密码
    public function repassword()
    {
        $data = input('post.password');
        $data1 = input('post.newpassword');
        $pwd1 = $this->model->oldpassword();
        $pwd = json_encode($pwd1);
        //echo $pwd;
        if (md5($data) == $pwd1['password']) {
            $pwd = $this->model->password($data1);
            echo 1;
        }else{
            echo 2;
        }
    }
    //查询快递信息
    public function courier()
    {
        $data = input();
        $res = $this->model->kd($data);
        $orderid = $res[0]['orderid'];
        $addtime = $res[0]['addtime'];
        $this->assign('orderid',$orderid);
        $this->assign('addtime',$addtime);
        //显示快递的信息
        $url = "http://v.juhe.cn/exp/index?com=yd&no=3973210243695&dtype=&key=b4b4fb964bb2399fad7b4aa3bc632eb6";
        $curl = MyCurl::get($url);
        $arr = json_decode($curl,true);
        echo "<hr/>";
        $title = "快递公司：".$arr['result']['company']."快递单号：".$arr['result']['no']; 
        $content = $arr['result']['list'];
        $this->assign('content',$content);
        // dump($content);
        return $this->fetch();
    }
    //收藏
    
}