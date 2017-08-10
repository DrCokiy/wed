<?php
namespace app\index\model;
use think\Db;
use think\Session;

class Product
{
	// 修改秒杀表，商品至1，秒杀结束
	public function miaozhi0($gid)
	{
		Db::name('goods')
			->where('gid =' . $gid)
			->update(['is_miao'=>0]);
	}
	
	// 删除订单
	public function del_ding($oid)
	{
		return Db::name('order')
				->where('oid = ' . $oid)
				->delete();
	}



	public function womens_single($gid)
	{
		$res = Db::name('goods,wed_image')
				->where("wed_goods.gid = $gid and wed_goods.gid = wed_image.gid ")
				->find();
		return $res;
	}
	//商品评论
	public function single($gid)
	{
		$res = Db::name('goods,wed_comment,wed_customer')
				->where("wed_goods.gid = wed_comment.gid and wed_customer.uid = wed_comment.uid")
				->select();
		return $res;
	} 
	public function lists()
	{
		$res = Db::name('image,wed_goods')
				->where('wed_image.gid = wed_goods.gid and wed_goods.class = 8')
				->paginate(4);
		return $res;
	}
	public function womens_accessories()
	{
		$res = Db::name('image,wed_goods')
				->where('wed_image.gid = wed_goods.gid and wed_goods.class = 7')
				->paginate(4);
		return $res;
	}
	public function womens1()
	{
		$res = Db::name('goods,wed_brand,wed_image')
				->where("wed_goods.gid=wed_image.gid and wed_brand.sex=2 and wed_brand.brandid=wed_goods.brand and is_sale = 0")
				->paginate(6);
				// ->select();
		return $res;
	}
	public function womens($brandid)
	{
		$res = Db::name('goods,wed_brand,wed_image')
				->where("wed_goods.gid=wed_image.gid and wed_brand.sex=2 and wed_brand.brandid=wed_goods.brand and is_sale = 0 and wed_goods.brand=" . 
					$brandid)
				->paginate(6);
		return $res;
	}
	public function mens1()
	{
		$res = Db::name('goods,wed_brand,wed_image')
				->where("wed_goods.gid=wed_image.gid and wed_brand.sex=1 and wed_brand.brandid=wed_goods.brand")
				->paginate(6);
		return $res;
	}
	public function mens($brandid)
	{
		$res = Db::name('goods,wed_brand,wed_image')
				->where("wed_goods.gid=wed_image.gid and wed_brand.sex=1 and wed_brand.brandid=wed_goods.brand and wed_goods.brand=" . 
					$brandid)
				->paginate(6);
		return $res;
	}
	// 添加购物车
	public function checkAdd($data)
	{
		
		$res = Db::name('shop_car')
				->insert($data);
		return $res;
	}

	// 查询数据库，根据服装品牌查询所拥有的类型，并拿出来一件商品作展示
	public function getBrandClassAndGoods($brandid)
	{
		return Db::name('brand b,wed_gclass c,wed_goods g,wed_image i')
				->field('b.brandid,b.brandname,c.classid,c.classname,g.gid,i.src')
				->where('b.brandid=g.brand and c.classid=g.class and g.gid=i.gid and b.brandid=' . $brandid)
				->limit(3)
				->select();
	}

	// 商品浏览量+1
	public function incrementCount($gid)
	{
		Db::query("update wed_goods set see_count = see_count+1 where gid = " . $gid);
	}
	//添加收藏
	public function addsc($data)
	{
		$res = Db::name('track')
				->insert($data);
		return $res;
	}
	//展示秒杀的商品
	public function miaosha($gid)
	{
		$res = Db::name("miaosha")
				->where("gid = $gid")
				->select();
		return $res;
	}
	//查询商品信息
	public function goodinof($gid)
	{
		$info = Db::name('goods')
				->field("colorstr,sizenum,price")
				->find();
		return $info;
	}
	//添加秒杀商品进购物车
	public function addms($data)
	{
		$res = Db::name('shop_car')
				// ->allowField(true)
				->insert($data);
		return $res;
	}
}