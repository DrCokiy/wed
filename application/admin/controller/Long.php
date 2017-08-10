<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\PlaceCar;
class Long extends Base
{
	// 显示场地列表页面
	public function place_list(PlaceCar $place)
	{
		$data = $place->placeList();
		// dump($data);die;
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示添加场地页面
	public function place_add()
	{
		return $this->fetch();
	}

	// 执行添加场地操作
	public function addplace(PlaceCar $place)
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
		$res = $place->addPlace($data);
		if ($res) {
			$this->success('添加场地成功','admin/long/place_list');
		} else {
			$this->error('添加失败');
		}
	}

	// 路径中的斜线替换
	protected function replaceDS($path)
	{
		return str_replace('\\','/',$path);
	}

	// 显示婚车列表页面
	public function car_list(PlaceCar $place)
	{
		$data = $place->carList();
		// dump($data);die;
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 显示添加婚车页面
	public function car_add()
	{
		return $this->fetch();
	}

	// 执行添加婚车操作
	public function addcar(PlaceCar $place)
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
		$res = $place->addCar($data);
		if ($res) {
			$this->success('添加车辆成功','admin/long/car_list');
		} else {
			$this->error('添加失败');
		}
	}
}