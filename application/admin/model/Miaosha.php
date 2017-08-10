<?php
namespace app\admin\model;
use think\Db;
class Miaosha
{
	// 添加秒杀商品信息，写进数据库
	public function addMiao($data)
	{
		// 将商品表中商品秒杀状态改为1
		$gid = $data['gid'];
		Db::name('goods')->where('gid =' . $gid)->update(['is_miao'=>1]);
		return Db::name('miaosha')
				->insert($data);
	}

	// 查询秒杀商品信息，列表显示
	public function miaoList()
	{
		return Db::name('miaosha m,wed_goods g,wed_image i')
				->field('m.id,g.gid,g.gname,i.src,m.time,m.price')
				->where('m.gid=g.gid and g.gid=i.gid')
				->select();
	}

	// 从数据库删除秒杀商品
	public function delmiao($gid)
	{
		return Db::name('miaosha')
				->where('gid = ' . $gid)
				->delete();
	}

	// 秒杀信息编辑页，查询某一件商品信息
	public function getOneMiaoInfo($id)
	{
		return Db::name('miaosha m,wed_goods g')
				->field('m.id,g.gname,m.time,m.price')
				->where('g.gid = m.gid and m.id=' . $id)
				->find();
	}

	// 更新秒杀信息
	public function updateMiao($data)
	{
		return Db::name('miaosha')
				->update($data);
	}
}