<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Order as OrderModel;
class Order extends Base
{
	// 显示新订单页面
	public function newOrder(OrderModel $order)
	{
		$data = $order->newOrder();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示已发货订单页面
	public function fahuoorder(OrderModel $order)
	{
		$data = $order->fahuoOrder();
		// dump($data);
		// die;
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示已完成订单页面
	public function readyorder(OrderModel $order)
	{
		$data = $order->readyOrder();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 改变订单状态，待发货改为已发货
	public function faToYi(OrderModel $order)
	{
		$oid = input('orderid');
		$res = $order->changedFtOYi($oid);
		echo $res;
	}
}