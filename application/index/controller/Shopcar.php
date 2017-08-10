<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Shopcar as CarModel;

class Shopcar extends Controller
{
    protected $Car;
    public function _initialize()
    {
        $this->car = new CarModel();
    }
    //查询购物车
    public function cart()
    {   
        $res = $this->car->checkcar();
        $this->assign('res',$res);
       
    	return $this->fetch();
    }
    //删除商品
    public function delcart()
    {
        $data = input();
        $uid = session('uid');
        $id = json_encode($data);
       //echo $id;
        $res = $this->car->delcar($data);
        if ($uid == null) {
           echo 3;   //没登录   
        }else{
            if ($res) {
                echo 1;      //删除成功
            }else{
                echo 2;        //删除失败
            }
        }
    }
    //清空购物车
    public function qk_shop()
    {
        $res = $this->car->qk_car();
        if ($res == 1){
            echo 1;
        }else{
            echo 2;
        }

    }
    //计算价格
    public function price()
    {
        $data = input();
        $res = json_encode($data);
        $con = $this->car->price($data);
        session('price',$con);
        echo $con;
    }
    //查询准备生成商品订单
    public function cart_order()
    {
        $data = input();
        $res = json_encode($data);
        //取得总价钱
        $price = $data['price'];
        $goodid = $data['gid'];
        session('price',$price);
        session('goodid',$goodid);
        $this->assign('price',$price);
        //查询订单商品
        $res = $this->car->cart_order($data);
        $this->assign('res',$res);
        //查询购物车
        $con = $this->car->showcar();
        $this->assign('con',$con);
        //dump($con);
    	return $this->fetch();
    } 
    //添加地址
    public function addcar()
    {
        $arr = input();
        $data['address'] =  $arr['province'] . $arr['city'] . $arr['country'] . $arr['address'];
        $data['uid'] = session('uid');
        $data['addtime'] = time();
        $data1 = array_merge($arr,$data);
        $res = json_encode($data1);
        //echo $res;
        $con = $this->car->address($data1);
       // dump($con);
        if ($con) {
            echo 1;
        }else {
            echo 2;
        }
    }
    //删除地址
    public function deladdr()
    {
        $data = input();
        $con = json_encode($data);
        //echo $con;
        $res = $this->car->deladdr($data);
        if ($res == 1) {
            echo 1;
        }
    }
    //修改地址
    public function changeaddr()
    {
        $data = input();
        $con = json_encode($data);
        //echo $con;
        $res = $this->car->changeaddr($data);
        if ($res == 1) {
            echo 1;
        }
    }
    
    //提交订单
    public function book()
    {
        $id = input('post.goodsid');
        // $res= json_encode($id);
       // echo $id;
       // 查询选择的地址
        $res = $this->car->goodsaddress($id);
        $data = $res;
        $data['gid'] = session('goodid');
        $gid = $data['gid'];
        $data['payable'] = session('price');
        // dump($data);die;
        $data['uid'] = session('uid');
        $data['orderid'] =date('Ymd',time()).time().rand(0,9);
        $data['payable'] = session('price');
        $data['addtime'] = time();
        $data['is_ready'] = 1;
        // $data['readytime'] = time();
        $res= json_encode($data);
       // echo $res;
        $con = $this->car->insertorder($data);
        if ($con) {
            $ass = $this->car->is_del($gid);
           echo $con;
        }else{
            echo 2;
        }
    }
    public function cart_order_success()
    {
    	return $this->fetch();
    }
    //支付商品
   //支付商品
    public function zf()
    {
        $oid = input();
        $res = json_encode($oid);
        $con = $this->car->zf($oid);
        //echo $con;
        if ($res) {
           echo 1;
        }
    }
}