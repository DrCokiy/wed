<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\admin\model\Comment as CommentModel;
use extend\email\Smtp;

class Comment extends Controller
{
	protected $comment;
    public function _initialize()
    {
        $this->comment = new CommentModel();
    }
    //查询评论
	public function Comment()
	{
		$id = input('class');
		$res = $this->comment->comment($id);
		$this->assign('res',$res);
		// dump($res);
		// die;
		return $this->fetch();
	}
	//删除评论
	public function delComment()
	{
		$id = input('id');
		//echo $id;
		$res = $this->comment->del($id);
		if ($res) {
			echo 1;
		}else{
			echo 2;
		}
	}
	//查找用户邮箱
	public function email()
	{
		$uid = input('uid');
		$res = $this->comment->user($uid);
		$this->assign('res',$res);
		return $this->fetch();
	}
	//发送邮箱
	public function send()
	{
		//******************** 配置信息 ********************************

		$smtpusermail = "zzzjp1314@163.com";//SMTP服务器的用户邮箱
		$smtpemailto = $_GET['toemail'];//发送给谁
		$mailtitle = $_GET['title'];//邮件主题
		$mailcontent = $_GET['content'];
		//$mailcontent = "<h1>".$_POST['content']."</h1>";//邮件内容
		$mailtype = "TXT";//邮件格式（HTML/TXT）,TXT为文本邮件
		//************************ 配置信息 ****************************
		// echo json_encode($_GET);
		// $smtp = new Smtp($mailcontent);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		// $smtp->debug = true;//是否显示发送的调试信息
		// $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

		echo 1; 	// 发送成功

	}
}
