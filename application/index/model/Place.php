<?php
namespace app\index\model;
use think\Db;
use think\Session;

class Place
{
	public function checkPlace()
	{
		return Db::name('place')
			->select();
	}
	
	 
}