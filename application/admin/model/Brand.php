<?php
namespace app\admin\model;
use think\Db;
class Brand
{
	// 查询所有品牌在品牌列表页显示
	public function brandList()
	{
		return Db::name('brand')
				->field('brandid,brandname,description')
				// ->select();
				->paginate(5);
	}

	// 将新添加品牌写进数据库
	public function addBrand($data)
	{
		return Db::name('brand')->insert($data);
	}

	// 根据查找条件找出相应品牌信息
	public function getBrandByCondition($bandid)
	{
		return Db::name('brand')
				->where('brandid',$bandid)
				->find();
	}

	// 更新品牌信息，只能更改描述,写进数据库
	public function updatebrand($data)
	{
		return Db::name('brand')
				->update($data);
	}
}