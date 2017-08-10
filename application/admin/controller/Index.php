<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Index as IndexModel;
class Index extends Base
{
	public function index(IndexModel $index)
	{
		$rid = session('rid');
		if (1 == $rid) {
			$big = $index->getTopMenu()[0];
			$small = $index->getTopMenu()[1];
		} elseif (2 == $rid) {
			$big = $index->getClerkMenu($rid)[0];
			$small = $index->getClerkMenu($rid)[1];
		} else {
			$big = $index->getClerkMenu($rid)[0];
			$small = $index->getClerkMenu($rid)[1];
		}
		

		return $this->fetch('',['big'=>$big,'small'=>$small]);
	}

	public function welcome()
	{
		return $this->fetch();
	}
}