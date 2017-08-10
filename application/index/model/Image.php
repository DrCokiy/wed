<?php
namespace app\index\model;
use think\Db;
use think\Session;

class Image
{

	public function gallery()
	{
		$data = Db::name('image')->where('gid','=',0)->select();
		return $data;
	}
	public function akind($class)
	{
		$data = Db::name('image,wed_goods')
				->where("wed_image.gid = wed_goods.gid and wed_goods.class=$class")
				// ->buildSql();
				 ->select();
		return $data;
	}
	public function minikind($class)
	{
		$data = Db::name('image,wed_goods')
				->where("wed_image.gid = wed_goods.gid and wed_goods.class=$class")
				// ->buildSql();
				 ->select();
		return $data;
	}
	public function skind($class)
	{
		$data = Db::name('image,wed_goods')
				->where("wed_image.gid = wed_goods.gid and wed_goods.class=$class")
				// ->buildSql();
				 ->select();
		return $data;
	}
	public function tkind($class)
	{
		$data = Db::name('image,wed_goods')
				->where("wed_image.gid = wed_goods.gid and wed_goods.class=$class")
				// ->buildSql();
				 ->select();
		return $data;
	}
	public function pkind($class)
	{
		$data = Db::name('image,wed_goods')
				->where("wed_image.gid = wed_goods.gid and wed_goods.class=$class")
				// ->buildSql();
				 ->select();
		return $data;
	}
}