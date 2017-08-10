<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Goods;
use app\admin\model\Miaosha;
class Activity extends Base
{
	// 显示填写秒杀信息页面
	public function miaosha(Goods $goods)
	{
		$data = $goods->goodsList();
		return $this->fetch('',['data'=>$data]);
	}

	// 添加秒杀商品信息
	public function add(Miaosha $miao)
	{
		$data = input();
		// echo json_encode($data);
		$data['time'] = strtotime($data['time']);
		$res = $miao->addMiao($data);
		if ($res) {
			echo 1;
		} else {
			echo 2;
		}
	}

	// 显示秒杀商品列表页面
	public function miao_list(Miaosha $miao)
	{
		$data = $miao->miaoList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 删除秒杀商品
	public function delmiao(Miaosha $miao)
	{
		$gid = input('gid');
		$res = $miao->delmiao($gid);
		echo $res;
	}

	// 显示秒杀信息更改页面
	public function miao_edit(Miaosha $miao)
	{
		$id = input('id');
		// dump($id);die;
		$data = $miao->getOneMiaoInfo($id);
		return $this->fetch('',['data'=>$data]);
	}

	// 更新秒杀信息
	public function updatemiao(Miaosha $miao)
	{
		$data = input();
		$data['time'] = strtotime($data['time']);
		// echo json_encode($data);
		$res = $miao->updateMiao($data);
		if ($res) {
			echo 1;
		} else {
			echo 2;
		}
	}
}