<?php
namespace app\admin\model;
use think\Db;
use think\Session;

class Comment
{
	public function comment($id)
	{ 
		$res = Db::name('comment c,wed_customer u,wed_image i')
				->field("i.src,u.realname,u.phone,c.id,c.content,c.class,u.uid")
				->where("c.uid = u.uid and i.gid = c.gid and c.class = $id")
				->select();
		return $res;
	}
	public function del($id)
	{
		$res = Db::name('comment')
				->where("id = $id")
				->delete();
		return $res;
	}
	//查询用户的邮箱
	public function user($uid)
	{
		$res= Db::name('customer')
				->field('email')
				->where("uid = $uid")
				->find();
		return $res;
	}
}