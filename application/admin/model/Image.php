<?php
namespace app\admin\model;
use think\Db;
class Image
{
	// 添加图片进数据库
	public function addImage($data)
	{
		return Db::name('image')
				->insert($data);
	}

	public function imageAllList()
	{
		return Db::name('image')
					->select();
					// ->paginate(3);
	}
	
	// 查找所有图片用于图片列表页显示
	public function imageList()
	{
		// 用于每页显示
		$page =  Db::name('image,wed_goods')
				->where('wed_image.gid = wed_goods.gid')
				->where('wed_image.is_del = 0')
				->field('wed_image.id,wed_goods.gname,wed_image.iname,wed_image.src')
				->paginate(3);
		$total = count(Db::name('image,wed_goods')
					->where('wed_image.gid = wed_goods.gid')
					->select());
		return [$page,$total];
	}

	// 查找回收站图片用于图片列表页显示
	public function imageDelList()
	{
		// 用于每页显示
		return $page =  Db::name('image,wed_goods')
				->where('wed_image.gid = wed_goods.gid')
				->where('wed_image.is_del = 1')
				->field('wed_image.id,wed_goods.gname,wed_image.iname,wed_image.src')
				->paginate(3);
	}


	// 更新商品图片
	public function updateGoodsImage($gid,$src)
	{
		return Db::name('image')
				->where('gid',$gid)
				->update(['src'=>$src]);
	}

	// 将图片is_del属性置为1，不允许显示
	public function del_image($delid)
	{
		return Db::name('image')
				->where('id',$delid)
				->update(['is_del'=>1]);
	}
	
	// 从回收站放出图片，总经理有此权限
	public function displayImage($did)
	{
		return Db::name('image')
				->where('id',$did)
				->update(['is_del'=>0]);
	}
}