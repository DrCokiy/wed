<?php
namespace app\index\model;
use think\Db;
use think\Session;

class Shopcar
{
	//查询购物车
	public function checkCar()
	{
		$uid = session('uid');
		$res = Db::name("shop_car c,wed_image i")
				->field('c.id,c.gid,i.src,c.color,c.size,i.iname,c.now_price')
				->where("c.gid = i.gid and c.is_del=0 and c.uid = ". $uid  )
				->select();
		return $res;
	}
	//删除购物车
	public function delcar($data)
	{
		$id = $data['id'];
		$res = Db::name('shop_car')
				->where("id = $id")
				->delete();
		return $res;
	}
	//清空购物车
	public function qk_car()
	{
		$data['is_del'] = 1;
		$uid = session('uid');
		$res = Db::name('shop_car')
				->where("uid = $uid")
				->update($data);
		return $res;
	}
	//计算价格
	public function price($data)
	{
		$arr =$data['gid']; 
		$res = Db::name('goods')
				->where("gid in ($arr)")
				->sum('price');
		return $res;
	}
	//准备生成订单
	public function cart_order($data)
	{
		$time = time();
		$gid = $data['gid'];
		$res = Db::name('goods,wed_image')
				->where("wed_goods.gid in ($gid) and wed_image.gid=wed_goods.gid ")
				->select();
		return $res;
	}
	//删除地址
	public function deladdr($data)
	{
		$id = $data['id'];
		$res = Db::name('address')
				->where("id = $id")
				->delete();
		return $res;
	}
	//展示地址信息
	public function changeaddr($data)
	{
		$id = $data['id'];
		$res = Db::name('address')
				->where("id = $id")
				->select();
		return $res;
	}
	//提交订单式查询地址
	public function goodsaddress($id)
	{
		$res = Db::name('address')
				->field('address')
				->where("id = $id")
				->find();
		return $res;
	}
	//添加订单
	public function insertorder($data)
	{
		$res = Db::name('order')
				->insertGetId($data);
		return $res;
	}
	//添加地址
	public function address($data)
	{
		$res = Db::name('address')
				->insert($data);
		return $res;
	}
	//查询购物车生成订单信息
	public function showcar()
	{
	 	$res = Db::name('address')
	 			->where('uid',session('uid'))
	 			->select();
	 	return $res; 
	}
	public function is_del($gid)
	{
		$data['is_del'] = 1;
		$res = Db::name('shop_car')
				->where("gid in ($gid)")
				->update($data);
	}
	public function zf($oid)
	{
		// $data['state'] = 1;
		$id = $oid['oid']; 
		$res = Db::name('order')
				->where("oid in ($id)")
				->update(['state'=>1]);
				// ->buildSql();
		return $res; 
	}
}