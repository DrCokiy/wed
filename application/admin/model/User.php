<?php
namespace app\admin\model;
use think\Db;
use think\Session;
class User
{
	// 查询数据库判断是否允许登录后台
	public function checkLogin($data)
	{
		// 查询后台用户表是否有相应记录
		$res = Db::name('admin_user')
				->where('realname','like',$data['realname'])
				->where('password',md5($data['password']))
				->find();
		// 有相应记录再查询此用户角色
		if ($res) {
			return [$this->getRoleByUid($res['uid']),$res['uid'],$res['allowlogin']];
		} else {
			return false;
		}
	}

	// 根据用户id查询相应角色
	public function getRoleByUid($uid)
	{
		// 在关联表查询角色id
		$rid = Db::name('us_ro')
				->where('uid',$uid)
				->find()['rid'];
		// 在角色表查询角色名
		$rname = Db::name('role')
				->where('rid',$rid)
				->find()['rname'];
		return $rid . $rname;	// 以 ‘3总经理’ 这种格式返回		
	}



	// 查询角色列表、除去总经理，用于添加店员时遍历
	public function roleList()
	{
		$rid = session('rid');
		if (2 == $rid) {
			return Db::name('role')
				->field('rid,rname')
				->where('rid > 2')
				->select();
		}
		return Db::name('role')
				->field('rid,rname')
				->where('rid != 1')
				->select();
	}

	// 将新添加的店员写进数据库
	public function addClerk($data)
	{
		$data['password'] = md5($data['password']);
		return Db::name('admin_user')
				->insertGetId($data);
	}

	// 查询顾客表用户信息
	public function customerList()
	{
		return Db::name('customer')
				->field('uid,realname,sex,phone,email,city,regtime,allowlogin')
				->select();
	}

	// 向用户、角色关联表写数据
	public function insertUs_Ro($rid,$res)
	{
		$data = ['rid' => $rid,'uid' => $res];
		return Db::name('us_ro')
				->insert($data);
	}

	// 查询店员列表，用于列表页显示
	public function getUserList($data)
	{
		if (1 == $data) {
			return Db::name('admin_user u,wed_us_ro us,wed_role r')
					->field('u.uid,u.realname,u.sex,r.rname,u.phone,u.email,u.city,u.regtime,u.allowlogin')
					->where('u.uid=us.uid and us.rid=r.rid')
					->order('r.rid')
					->select();
		} else if (2 == $data) {
			return Db::name('admin_user u,wed_us_ro us,wed_role r')
					->field('u.uid,u.realname,u.sex,r.rname,u.phone,u.email,u.city,u.regtime,u.allowlogin')
					->where('u.uid=us.uid and us.rid=r.rid and r.rid != 1')
					->order('r.rid')
					->select();
		} else {
			$uid = session('uid');
			return Db::name('admin_user u,wed_us_ro us,wed_role r')
					->field('u.uid,u.realname,u.sex,r.rname,u.phone,u.email,u.city,u.regtime,u.allowlogin')
					->where('u.uid=us.uid and us.rid=r.rid and u.uid =' . $uid)
					->select();
		}
		
	}

	// 查询用户信息，以供修改编辑
	public function getUserInfo($uid)
	{
		return Db::name('admin_user')
				->where('uid',$uid)
				->find();
	}

	// 查询角色列表，用于列表页显示
	public function getRoleList()
	{
		return Db::query("select r.rid,r.rname,u.uid,u.realname,group_concat(realname SEPARATOR ','),r.description
			from wed_role r,wed_admin_user u,wed_us_ro ur
			where r.rid = ur.rid and u.uid=ur.uid
			group by r.rid");
		// return Db::name('role')->select();
	}

	// 更新admin_user表，禁止某用户登录
	public function forbidLogin($uid)
	{
		// 更新成功返回影响条数，否则返回0
		return Db::name('admin_user')
				->where('uid',$uid)
				->update(['allowlogin' => 1]);
	}

	// 更新admin_user表，允许某用户登录
	public function allowLogin($uid)
	{
		// 更新成功返回影响条数，否则返回0
		return Db::name('admin_user')
				->where('uid',$uid)
				->update(['allowlogin' => 0]);
	}

	// 更新customer表，禁止某顾客登录
	public function forbidCustomerLogin($uid)
	{
		// 更新成功返回影响条数，否则返回0
		return Db::name('customer')
				->where('uid',$uid)
				->update(['allowlogin' => 1]);
	}

	// 更新customer表，允许某顾客登录
	public function allowCustomerLogin($uid)
	{
		// 更新成功返回影响条数，否则返回0
		return Db::name('customer')
				->where('uid',$uid)
				->update(['allowlogin' => 0]);
	}


	// 更改用户密码
	public function changeUserPwd($data)
	{
		return Db::name('admin_user')
				->update($data);
	}

	// 执行删除用户操作，从数据库删掉记录
	public function deleteUser($uid)
	{
		return Db::name('admin_user')->delete($uid);
	}

	// 更新店员信息
	public function updateUser($data)
	{
		return Db::name('admin_user')
				->update($data);
	}

	// 查询所有用户名、角色名，用于修改用户对应角色
	public function getUserAndRole()
	{
		$users = Db::name('admin_user')
				->field('uid,realname')
				->select();
		$roles = Db::name('role')
				->field('rid,rname')
				->select();
		return [$users,$roles];
	}

	// 根据uid查询用户名字
	public function getNameByUid($uid)
	{

		return Db::name('admin_user')
				->field('uid,realname')
				->where('uid',$uid)
				->find();
	}

	// 更新用户角色关联表（us_ro），更改用户角色
	public function changeUserRole($data)
	{
		return Db::name('us_ro')
				->update($data);
	}

	// 搜索匹配到关键词的用户信息
	public function findUserInfo($keyword)
	{
		return Db::name('customer')
				->field('uid,realname,sex,phone,email,city,regtime,allowlogin')
				->where('realname','like',$keyword)
				->whereOr('phone','like',$keyword)
				->whereOr('email','like',$keyword)
				->select();
				
	}

	// 搜索匹配到关键词的用户信息
	public function selectUserInfo($keyword)
	{
		return Db::name('customer')
				->field('uid,realname,sex,phone,email,city,regtime,allowlogin')
				->where('realname','like',$keyword)
				->whereOr('phone','like',$keyword)
				->whereOr('email','like',$keyword)
				->paginate(1);
				
	}

}