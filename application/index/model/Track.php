<?php
namespace app\index\model;
use think\Db;
class Track
{
	public function addTrack($uid,$gid)
	{
		Db::name('track')->insert(['uid'=>$uid,'gid'=>$gid]);
	}	
}