<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\admin\model\User as UserModel;
class User extends Controller
{
	// 显示登录页面
	public function login()
	{
		return $this->fetch();
	}

	// 验证是否允许登录
	public function checkLogin(UserModel $user)
	{
		$data = input();
		$res = $user->checkLogin($data)[0];
		$uid = $user->checkLogin($data)[1];
		$allowlogin = $user->checkLogin($data)[2];
		if (!$res) {
			$this->error('用户名或密码错误');
			exit;
		}
		if ($allowlogin == 1) {
			$this->error('您的账号被锁定，暂无法登录，请联系管理员');
			exit;
		}
		if (substr($res,0,1) > 3) {
			$this->error('对不起，您没有权限登录后台');
			exit;
		}
		session('realname',$data['realname']);	// 真实姓名
		session('rname',substr($res,1));	// 角色名
		session('uid',$uid);	// 用户id
		session('rid',substr($res,0,1));	// 角色id
		$this->success('登陆成功','/admin/index/index');
	}

	// 退出登录
	public function logout()
	{
		session(null);
		$this->redirect('/admin/user/login');
	}

	// 处理ajax对验证码的验证请求，登录页面的验证码
	public function ajaxYzm()
	{
		$data = input();
		$captcha = $data['yzm'];
		if(!captcha_check($captcha)){
			echo 1;		//验证失败
		} else {
			echo 2;		//验证成功
		}
	}

	// 显示用户列表页面
	public function user_list(UserModel $user)
	{
		$data = $user->customerList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示店员列表页面
	public function clerk_list(UserModel $user)
	{
		$rid = session('rid');
		$data = $user->getUserList($rid);
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 改变店员状态，是否允许登录
	public function changeLock(UserModel $user)
	{
		if (input('?startuid')) {
			$uid = input('startuid');
			$res = $user->allowLogin($uid);		// 更新成功返回影响条数，否则返回0
		} elseif (input('?stopuid')) {
			$uid = input('stopuid');
			$res = $user->forbidLogin($uid);	
		}
		if (0 == $res) {
			echo 1; 	// 更新失败
		} else {
			echo 2; 	// 更新成功
		}
	}

	// 改变顾客状态，是否允许登录
	public function changeCustomerLock(UserModel $user)
	{
		if (input('?startuid')) {
			$uid = input('startuid');
			$res = $user->allowCustomerLogin($uid);		// 更新成功返回影响条数，否则返回0
		} elseif (input('?stopuid')) {
			$uid = input('stopuid');
			$res = $user->forbidCustomerLogin($uid);
		}
		if (0 == $res) {
			echo 1; 	// 更新失败
		} else {
			echo 2; 	// 更新成功
		}
	}

	// 显示添加店员页面
	public function clerk_add(UserModel $rolelist)
	{
		$role = $rolelist->roleList();
		$this->assign('role',$role);
		return $this->fetch();
	}

	// 执行添加店员操作
	public function addClerk(UserModel $role)
	{
		$data = input();
		$data['regtime'] = time();
		$rid = $data['rname'];
		unset($data['rname']);
		$res = $role->addClerk($data);
		if ($res) {
			$ress = $role->insertUs_Ro($rid,$res);
			if ($ress) {
				$this->success('添加成功');
			} else {
				$this->error('向关联表写入数据失败');
			}	
		} else {
			$this->error('添加失败');
		}
	}

	// 显示角色管理页面
	public function role_list(UserModel $user)
	{
		$role = $user->getRoleList();
		// dump($role);
		// die;
		$this->assign('role',$role);
		return $this->fetch();
	}


	// 显示店员信息编辑页面
	public function clerk_edit(UserModel $user)
	{
		$uid = input('uid');
		$data = $user->getUserInfo($uid);
		return $this->fetch('',['data'=>$data]);
	}

	// 显示修改密码页面
	public function change_pwd()
	{
		$data = input();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 修改密码操作
	public function changepwd(UserModel $user)
	{
		$data = input();
		$data['uid'] = session('uid');
		$data['password'] = md5($data['password']);
		$res = $user->changeUserPwd($data);
		if ($res) {
			echo 1;		// 更改密码成功
		} else {
			echo 2;		// 修改密码失败
		}
	}

	// 删除店员操作
	public function deleteUser(UserModel $user)
	{
		$uid = input('uid');
		$res = $user->deleteUser($uid);
		if ($res) {
			echo true;
		} else {
			echo false;
		}
	}

	// 修改店员信息
	public function updateUser(UserModel $user)
	{
		$data = input();
		$res = $user->updateUser($data);
		if ($res) {
			echo 1;
		} else {
			echo false;
		}
	}

	// 显示修改用户角色的页面
	public function role_change(UserModel $user)
	{
		$users = $user->getUserAndRole()[0];
		$roles = $user->getUserAndRole()[1];
		return $this->fetch('',['users'=>$users,'roles'=>$roles]);
	}

	// 显示修改单个用户角色的页面
	public function role_single_change(UserModel $user)
	{
		$uid = input('uid');
		$realname = $user->getNameByUid($uid);
		$roles = $user->getUserAndRole()[1];
		return $this->fetch('',['realname'=>$realname,'roles'=>$roles]);
	}

	// 执行修改用户角色的功能
	public function changerole(UserModel $user)
	{
		$data = input();
		$res = $user->changeUserRole($data);
		if ($res) {
			echo 1;		// 修改角色成功
		} else {
			echo 2;		// 修改失败
		}
	}

	// 根据关键词搜索顾客信息，发回给ajax
	public function findUserInfo(UserModel $user)
	{
		$keyword = '%' . input('kwd') . '%';
		$data = $user->findUserInfo($keyword);
		echo json_encode($data);
	}

	// 根据关键词搜索顾客信息，弹窗显示
	public function selectUserInfo(UserModel $user)
	{
		$keyword = '%' . input('kwd') . '%';
		$data = $user->selectUserInfo($keyword);
		$this->assign('data',$data);
		return $this->fetch();
	}
}