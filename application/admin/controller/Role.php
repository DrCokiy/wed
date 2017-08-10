<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Index;
class Role extends Base
{
	// 显示修改角色权限页面
	public function ro_ju(Index $index)
	{
		$rid = input('rid');
		$rname = $index->getInfoById($rid);
		// 取出所有菜单选项
		$big = $index->getTopMenu()[0];
		$small = $index->getTopMenu()[1];
		$this->assign('rname',$rname);
		// var_dump($rname,$big,$small);die;
		return $this->fetch('',['big'=>$big,'small'=>$small]);
	}

	// 修改角色权限
	public function powerchange(Index $index)
	{
		// echo json_encode($_POST['jid']);
		$array = $_POST['jid'];
		$rid = $_POST['rid'];
		// 返回值是1000的所有键名，表示没有该权限,此值+1即为ro_ju表中jid的值
		$arr = array_keys($array,1000);
		$arr = array_map([$this,'addone'],$arr);
		$str = implode(',',$arr);
		$res = $index->changeRolePower($rid,$str);
		if ($res) {
			echo 1;		// 更改成功
		} else {
			echo 2;		// 修改失败
		}
	}

	protected function addone($a)
	{
		return $a + 1;
	}
}