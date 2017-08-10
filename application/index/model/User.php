<?php
namespace app\index\model;
use think\Db;
use think\Session;

class User
{	
	public function nameLogin($data)
	{
		// 查询后台用户表是否有相应记录
		$res = Db::name('customer')
				->where('realname',$data)
				->find();
		return $res;
	}
	// 查询数据库判断是否允许登录
	public function checkLogin($data)
	{
		// 查询后台用户表是否有相应记录
		$res = Db::name('customer')
				->where('realname',$data['realname'])
				->where('password',md5($data['password']))
				->find();
		return $res;
	}
	public function checkRegister($data)
	{
		$res = Db::name('customer')
				->insert($data);
		return $res;
	}
	public function checkPhone($data)
	{
		//dump($data);
		$res= Db::name('customer')
				->where('phone',$data)
				->find();
		return $res;
	}
	public function phoneName($data)
	{
		//dump($data);
		$res= Db::name('customer')
				->where('phone',$data)
				->select();
		return $res;
	}
	public function checkPlace($data)
	{
		$id = session('id');
		$res = Db::name('cus_pl')
				->insert($data);
		return $res;
	}
	public function checkInfo($data)
	{
		$uid = session('uid');
		$arr = Db::name('customer')
				->where("uid = $uid")
				->update($data);
		return $arr;
	}
	public function checkArea($uid)
	{
		$res = Db::name('place,wed_cus_pl')
				->where("wed_place.id = wed_cus_pl.pid")
				->select();
		return $res;
	}
	public function checkShowcar()
	{
		$res = Db::name('car')
				->select();
		return $res;
	}
	public function checkCar($data)
	{

		$con = Db::name('cus_car')
				->insert($data);
		return $con;
	}
	public function member_car()
	{
		$res = Db::name('car,wed_cus_car')
				->where("wed_car.id= wed_cus_car.cid")
				->select();
		return $res;
	}
	public function delCar($data)
	{
		$id=$data['id'];
		$res = Db::name('cus_car')
				->where("id  =$id")
				->delete();
		return $res;
	}


	// 添加第三方登录的用户进数据库
	public function addThirdUser($data)
	{
		// 判断此用户是否已经存在数据库
		$is_cunzai = Db::name('third_user')
					->field('id')
					->where('name','like',$data['name'])
					->where('nage',$data['nage'])
					->where('city',$data['city'])
					->find();
		if ($is_cunzai) {
			return $is_cunzai['id']; 	// 如果存在，返回该条记录id，即用户id
		} else {
			// 如果不存在，写进数据库
			return Db::name('third_user')
					->insertGetId($data);
		}
	}
	public function updateCar($ip)
	{
		$uid = session('uid');
		$data['uid'] = $uid;
		$res = Db::name('shop_car')
				->where("addip = $ip")
				->update($data);
	}
	public function delPlace($data)
	{
		$id=$data['id'];
		$res = Db::name('cus_pl')
				->where("id = $id")
				->delete();
		return $res;  
	}
	//未付款状态
	public function momber_buy()
	{
		$res = Db::name('order,wed_image,wed_goods')
				// ->field("wed_image.src")
				->where("wed_order.gid = wed_image.gid and wed_image.gid = wed_goods.gid  and wed_order.state = 0")
				->paginate(4);
		return $res;
	}
	public function state1()
	{
		$res = Db::name('order,wed_image,wed_goods')
				// ->field("wed_image.src")
				->where("wed_order.gid = wed_image.gid and wed_image.gid = wed_goods.gid  and wed_order.state = 1")
				->paginate(4);
		return $res;
	}
	public function state2()
	{
		$res = Db::name('order,wed_image,wed_goods')
				// ->field("wed_image.src")
				->where("wed_order.gid = wed_image.gid and wed_image.gid = wed_goods.gid  and wed_order.state = 2")
				->paginate(4);
		return $res;
	}
	public function state3()
	{
		$res = Db::name('order,wed_image,wed_goods')
				// ->field("wed_image.src")
				->where("wed_order.gid = wed_image.gid and wed_image.gid = wed_goods.gid  and wed_order.state = 3")
				->paginate(4);
		return $res;
	}
	//删除未付款商品
	public function delwfk($gid)
	{
		$oid = $gid['oid'];
		$res = Db::name('order')
				->where("oid = $oid")
				->delete();
		return $res;
	}
	//确认收货
	public function bady($oid)
	{
		$oid = $oid['oid'];
		$data['state'] = 3;
		$res = Db::name('order')
				->where("oid = $oid")
				->update($data);
		return $res;
	}
	//查看订单状态
	public function member_order()
	{
		$res = Db::name('order,wed_image,wed_goods')
				// ->field("wed_image.src")
				->where("wed_order.gid = wed_image.gid and wed_image.gid = wed_goods.gid  and wed_order.state = 0")
				// ->select();
				->paginate(4);
		return $res;
	}
	//订单详细信息
	public function member_order_detail($data)
	{
		$gid = $data['gid'];
		$uid = session('uid');
		$res = Db::name("order o,wed_customer c,wed_image i,wed_goods g")
				->where("o.uid = $uid and o.gid = $gid and o.state = 0  and o.gid = i.gid and o.uid = c.uid and g.gid = $gid")
				->select();
		return $res;
	}
	public function addr($addr)
	{
		$res = Db::name('address')
				->where("address = '$addr'")
				->select();
		return $res;
	}
	//添加地址
	public function address($data)
	{
		$res = Db::name('address')
				->insert($data);
		return $res;
	}
	public function member_addr()
	{
		$res = Db::name('address')
				->where('uid =' . session('uid'))
				->select();
		return $res;
	}
	public function deladdr($id)
	{
		$id = $id['id'];
		$res = Db::name('address')
				->where("id = $id")
				->delete();
		return $res;
	}
	//查询评价商品的图片
	public function image($oid)
	{
		$res = Db::name('order')
				->field('gid')
				->where("oid = $oid")
				->select();
				// dump($res);die;
		$gid = $res[0]['gid'];
		$res = Db::name('image')
				->where("gid in ($gid)")
				->select();
		return $res;
	}
	//添加评价
	public function addcomment($data)
	{
		$con  = Db::name('comment')
				->insert($data);
		return $con; 
	}
	//查看商品是否评价
	public function comment($gid)
	{
		$res = Db::name('comment')
				->field('content')
				->where("gid = $gid")
				->select();
		return $res;
	}
	//修改时，查询旧密码的正确
	public function oldpassword()
	{
		$uid = session('uid');
		$res = Db::name('customer')
				->field("password")
				->where("uid = $uid")
				->find();
		return $res;
	}
	//修改密码
	public function password($data)
	{
		$uid = session('uid');
		$res = Db::name('customer')
				->where("uid = $uid")
				->update($data);
		return $res;
	}
	//展示个人信息
	public function member_info()
	{
		$uid = session('uid');
		$res = Db::name('customer')
				->where("uid = $uid")
				->find();
		return $res;

	}
	//快递查询订单信息
	public function kd($data)
	{
		$oid = $data['oid'];
		$res = Db::name('order')
				->where("oid = $oid")
				->select();
		return $res;
	}
	public function see_good()
	{
		$uid = session('uid');
		$res = Db::name('track t,wed_image i,wed_goods g')
				->field('i.src,g.price,g.gname')
				->where("t.uid = $uid and t.gid = i.gid and i.gid = g.gid and g.gid = t.gid")
				->paginate(5);
		return $res;

	}

	// 查询收藏商品
	public function shoucang()
	{
		return Db::name('track t,wed_goods g,wed_image i')
				->field('g.gid,g.gname,g.material,g.price,t.keep_time,i.src')
				->where('t.gid=g.gid and i.gid=g.gid and keep_time != 0 and t.uid=' . session('uid'))
				->select();
	}
} 

