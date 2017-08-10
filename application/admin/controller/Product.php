<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\admin\model\Gclass;
use app\admin\model\Goods;
use app\admin\model\Image;
use app\admin\model\Brand;
class Product extends Base
{
	// 显示添加商品页面
	public function product_add(Gclass $class)
	{
		$classes = $class->getclasses();
		$brandes = $class->getBrand();
		$this->assign('brandes',$brandes);
		$this->assign('classes',$classes);
		return $this->fetch();
	}

	// 执行添加商品的功能
	public function doProAdd(Goods $goods)
	{
		$data = input();
		
		$data['adduid'] = session('uid'); // 添加人id
		$data['addtime'] = time();
		$data['keyword'] = $data['keyword'] . ',' . $data['gname'];

		/**
		 * 这里应该加输入检测的代码,防止sql注入
		 */
		
		$res = $goods->addGoods($data);	//传给goods model去添加进数据库
		if ($res) {
			$this->success('添加成功');	// 添加成功，应该跳回列表页
		} else {
			$this->error('添加失败'); // 添加失败，跳回上一页重新输入
		}
		
	}

	// 显示商品列表页
	public function product_list(Goods $goods)
	{
		$data = $goods->goodsImageList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示品牌列表页
	public function product_brand(Brand $brand)
	{
		$data = $brand->brandList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 更改品牌信息
	public function brand_edit(Brand $brand)
	{
		$brandid = input('brandid');
		$data = $brand->getBrandByCondition($brandid);
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 更新品牌信息，只能更改描述
	public function updatebrand(Brand $brand)
	{
		// echo json_encode(input());
		$res = $brand->updatebrand(input());
		if ($res) {
			echo 1;
		} else {
			echo 2;
		}
	}

	//添加新的品牌
	public function newBrand(Brand $brand)
	{
		$res = $brand->addBrand(input());
		if ($res) {
			echo 1;		// 写入数据库成功
		} else {
			echo 2; 	// 写入失败
		}
	}

	// 根据查找的条件搜索数据库并返回结果
	public function selectByCondition(Goods $goods,Image $image)
	{
		$word = input('get.word');
		$data = $goods->selectByCondition($word);
		if ($data) {
			echo json_encode($data);
			// $this->assign('data',$data);
			// $imgs = $image->imageList();
			// $this->assign('imgs',$imgs);
			// return $this->fetch('product/product_list');
		} else {
			echo 2;		// 没有查到数据
		}
	}

	// 改变商品状态，是否下架
	public function changesale(Goods $goods)
	{
		if (input('?startid')) {
			$gid = input('startid');
			$res = $goods->productOnSale($gid);		// 更新成功返回影响条数，否则返回0
		} elseif (input('?stopid')) {
			$gid = input('stopid');
			$res = $goods->productNoSale($gid);
		}
		if (0 == $res) {
			echo 1; 	// 更新失败
		} else {
			echo 2; 	// 更新成功
		}
	}

	// 查询产品信息以供更改
	public function product_edit(Goods $goods)
	{
		$gid = input('gid');
		if ($gid) {
			$data = $goods->getGoodsInfo($gid);
		}
		return $this->fetch('',['data'=>$data]);
	}
	
	// 更改商品信息，
	public function updategoods(Goods $goods)
	{
		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('file');
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		if ($info) {
			// 成功上传后 获取上传信息
			// 输出 /uploads/20160820/42a79759f284b767dfcb904287.jpg
			$path = '/uploads/' . $info->getSaveName();
			$path = $this->replaceDS($path);
		} else {
			// 上传失败获取错误信息
			$this->error($file->getError());
		}
		$data = input();
		$data['src'] = $path;
		$res = $goods->updateDoods($data);
		if ($res) {
			$this->success('修改信息成功','/admin/product/product_list');
		} else {
			$this->error('修改失败');
		}
	}

	// 路径中的斜线替换
	protected function replaceDS($path)
	{
		return str_replace('\\','/',$path);
	}
}