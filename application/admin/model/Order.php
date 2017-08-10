<?php
namespace app\admin\model;
use think\Db;
class Order
{
	// 查询新生成订单
	public function newOrder()
	{
		return Db::name('order o,wed_image i,wed_goods g')
				->field('o.oid,o.orderid,o.uid,o.gid,i.src,g.gname,o.address,o.payable,o.state')
				->where('o.gid=g.gid and o.gid=i.gid and o.state in (0,1)')
				->order('o.state')
				->paginate(3);
	}

	// 改变订单状态，待发货改为已发货
	public function changedFtOYi($id)
	{
		return Db::name('order')
				->where('oid',$id)
				->update(['state'=>2]);
	}

	// 查询已发货订单
	public function fahuoOrder()
	{
		return Db::name('order o,wed_image i,wed_goods g')
				->field('o.oid,o.orderid,o.uid,o.gid,i.src,g.gname,o.address,o.payable,o.state')
				->where('o.gid=g.gid and o.gid=i.gid and o.state=2')
				->select();
	}

	// 查询已完成订单
	public function readyOrder()
	{
		return Db::name('order o,wed_image i,wed_goods g')
				->field('o.oid,o.orderid,o.uid,o.gid,i.src,g.gname,o.address,o.payable,o.state')
				->where('o.gid=g.gid and o.gid=i.gid and o.state=3')
				->select();
	}
}