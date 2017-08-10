<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use app\admin\model\Image;
class Goods
{
	// 向goods库存表写入数据
	public function addGoods($data)
	{
		return Db::name('goods')->insert($data);
	}

	// 取出数据用于商品列表页显示
	public function goodsImageList()
	{
		return Db::name('goods as g,wed_image as i,wed_brand b')
				->field('g.gid,i.src,g.gname,b.brandname,g.keyword,g.price,g.gcount,g.is_sale')
				->where('g.gid=i.gid and g.brand=b.brandid')
				->order('g.gid')
				// ->select();
				->paginate(5);		// 少查了一个缩略图，需要查另外一个表
	}

	// 查询商品id、名字，用于给商品添加图片
	public function goodsList()
	{
		return Db::name('goods')
				->field('gid,gname')
				->where('class != 7')
				->select();
	}

	// 取出小商品信息用于图片添加页遍历
	public function smallList()
	{
		return Db::name('goods')
				->field('gid,gname')
				->where('class = 7')
				->select();
	}

	public function selectByCondition($data)
	{
		return Db::name('goods as g,wed_image as i,wed_brand b')
				->field('g.gid,i.src,g.gname,b.brandname,g.keyword,g.price,g.is_sale')
				->where('g.gid=i.gid and g.brand=b.brandid')
				->where('keyword','like',"%" . $data . "%")
				->whereOr('b.brandname','like',"%" . $data . "%")
				->order('g.gid')
				->limit(3)
				->select();
	}

	// 改变商品状态，上架
	public function productOnSale($gid)
	{
		return Db::name('goods')
				->where('gid',$gid)
				->update(['is_sale' => 0]);
	}

	// 改变商品状态，上架
	public function productNoSale($gid)
	{
		return Db::name('goods')
				->where('gid',$gid)
				->update(['is_sale' => 1]);
	}

	// 查询商品信息，以供修改
	public function getGoodsInfo($gid)
	{
		return Db::name('goods')
				->field('gid,gname,price')
				->where('gid',$gid)
				->find();
	}

	// 更新商品信息：名字、价格、图片
	public function updateDoods($data)
	{
		$image = new Image();
		$res1 = $image->updateGoodsImage($data['gid'],$data['src']);
		$res2 = Db::name('goods')
				->where('gid',$data['gid'])
				->update(['gname'=>$data['gname'],'price'=>$data['price']]);
		if ($res1 && $res2) {
			return ture;
		} else {
			return false;
		}		
	}
}