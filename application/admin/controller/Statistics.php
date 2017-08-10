<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Statistics extends Base
{
	// 显示柱状图，每日订单
	public function histogram()
	{
		
		return $this->fetch();
	}

	// 显示折线图
	public function line_chart()
	{
		return $this->fetch();
	}

	// 显示时间轴
	public function time_chart()
	{
		return $this->fetch();
	}

	// 显示饼状图
	public function bing_chart()
	{
		return $this->fetch();
	}
}