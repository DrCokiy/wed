<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
class Base extends Controller
{
	public function _initialize()
	{
		$res = session('?rname');
		if (!$res) {
			$this->redirect('admin/user/login');
		}
	}
}