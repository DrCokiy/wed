<?php
namespace app\admin\model;
use think\Model;
class Gclass
{
	// 查询所有分类
	public function getClasses()
	{
		return db('gclass')->select();
	}

	// 查询所有品牌（供应商）
	public function getBrand()
	{
		return db('brand')->select();
	}
}