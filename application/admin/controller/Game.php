<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Game extends Base
{
	public function game()
	{
		return $this->fetch();
	}
}