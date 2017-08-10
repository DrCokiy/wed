<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Goods;
use app\admin\model\Image as ImageModel;
class Image extends Base
{
	// 显示添加图片页面，默认显示服装列表，smalllist方法显示小商品列表
	public function image_add(Goods $goods)
	{
		$data = $goods->goodsList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 返回小商品信息用于图片添加页遍历
	public function smallList(Goods $goods)
	{
		echo json_encode($goods->smallList());
	}

	// 执行文件上传操作,将图片路径写入数据库
	public function upload(ImageModel $image)
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
		if ($image->addImage($data)) {
			$this->success('添加成功');
		} else {
			$this->error('添加失败');
		}
	}

	// 显示图片列表页
	public function imageList(ImageModel $image,Request $request)
	{

		if ($request->isAjax()) {
			$data = $image->imageList()[0];
			echo json_encode($data);
		} else {
			$data = $image->imageList()[0];
			$total = $image->imageList()[1];
			$this->assign('data',$data);
			$this->assign('total',$total);
			return $this->fetch();
		}
		

		$data = $image->imageList();
		$this->assign('data',$data);
		return $this->fetch();

	}

	// 显示回收站图片列表页
	public function garbageimage(ImageModel $image)
	{
		$data = $image->imageDelList();
		$this->assign('data',$data);
		return $this->fetch();
	}

	// 路径中的斜线替换
	protected function replaceDS($path)
	{
		return str_replace('\\','/',$path);
	}

	// 将图片放入回收站
	public function image_del(ImageModel $image)
	{
		$delid = input('delid');
		$res = $image->del_image($delid);
		echo $res;
	}

	// 从回收站放出图片
	public function displayimage(ImageModel $image)
	{
		$did = input('did');
		$res = $image->displayImage($did);
		echo $res;
	}
}