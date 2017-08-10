<?php
namespace app\index\model;
use think\Db;
use think\Session;

class Car
{
	public function checkCar()
	{
		$res = Db::name('car')
				->select();
		return $res;
	}
}