<?php
namespace app\index\model;
use think\Db;
class Brand
{
	// 取得女装品牌列表
	public function getWomenBrands()
	{
		return Db::name('brand')
				->field('brandid,brandname')
				->where('sex',2)
				->select();
	}

	// 取得男装品牌列表
	public function getMenBrands()
	{
		return Db::name('brand')
				->field('brandid,brandname')
				->where('sex',1)
				->select();
	}
}