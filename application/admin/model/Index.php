<?php
namespace app\admin\model;
use think\Db;
class Index
{
	// 取得总经理的菜单项
	public function getTopMenu()
	{	
		$big = Db::name('juri')
				->field('jid,power')
				->where('pid=0')
				->select();
		$small = Db::name('juri')
				->field('jid,pid,power,src')
				->where('pid != 0')
				->select();
		return [$big,$small];
	}

	// 取得店长的菜单项
	public function getSecondMenu($rid)
	{
		$jid = Db::name('ro_ju')
				->where('rid',$rid)
				->find()['jid'];
		$big = Db::name('juri')
				->field('jid,power')
				->where('pid=0')
				->where('jid','not in','(' . $jid . ')')
				->select();
		$small = Db::name('juri')
				->field('pid,power,src')
				->where('pid != 0')
				->where('jid','not in','(' . $jid . ')')
				->select();
		return [$big,$small];
	}

	// 取得店员的菜单项
	public function getClerkMenu($rid)
	{
		$jid = Db::name('ro_ju')
				->where('rid',$rid)
				->find()['jid'];
		$big = Db::name('juri')
				->field('jid,power')
				->where('pid=0')
				->where('jid','not in','(' . $jid . ')')
				->select();
		$small = Db::name('juri')
				->field('pid,power,src')
				->where('pid != 0')
				->where('jid','not in','(' . $jid . ')')
				->select();
		return [$big,$small];
	}
	
	// 根据角色获得角色名，以及对应权限
	public function getInfoById($rid)
	{
		return Db::name('role r,wed_ro_ju rj')
				->field('r.rid,r.rname,rj.jid')
				->where('r.rid=rj.rid and r.rid=' . $rid)
				->find();
	}

	// 修改角色、权限关联表，改变角色权限
	public function changeRolePower($rid,$str)
	{
		return Db::name('ro_ju')
				->where('rid = ' . $rid)
				->update(['jid'=>$str]);
	}
}