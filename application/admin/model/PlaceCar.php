<?php
namespace app\admin\model;
use think\Db;
class PlaceCar
{
	// 把新增场地写进数据库
	public function addPlace($data)
	{
		return Db::name('place')->insert($data);
	}

	// 查询所有数据用于场地列表页面显示
	public function placeList()
	{
		return Db::query("select p.pname,p.src,p.rental,c.realname,cp.start_time,cp.stop_time from wed_place p LEFT JOIN wed_cus_pl cp on p.id=cp.pid left JOIN wed_customer c on c.uid=cp.uid");
	}

	// 把新增婚车写进数据库
	public function addCar($data)
	{
		return Db::name('car')->insert($data);
	}

	// 查询所有数据用于婚车列表页面显示:包括用户、用车信息
	public function carList()
	{
		return Db::query("select c.brand,c.number,c.src,c.rental,u.realname,cv.start_time,cv.stop_time from wed_car c LEFT JOIN wed_cus_car cv on c.id=cv.cid left JOIN wed_customer u on u.uid=cv.uid");
	}
}