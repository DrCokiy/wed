<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Product as ProductModel;
use app\index\model\Brand;
use app\index\model\Track;

class Product extends Controller
{
    protected $good;
    public function _initialize()
    {
        $this->good = new ProductModel();
    }

    // 秒杀至1
    public function miaozhi0(ProductModel $pro)
    {
        $gid = input('gid');
        $pro->miaozhi0($gid);
    }
    //dr列表
    public function lists(Brand $brand)
    {
        $brandid = input('brand');
        // $class = input('class');
        // 1die;
        // 展示女装
        if(!empty($brandid)){
            $res = $this->good->mens($brandid);
        }else{
            $res = $this->good->mens1();
        }
         //dump($res);
        // 女装品牌
        $womenbrands = $brand->getWomenBrands();
        // 男装品牌
        $menbrands = $brand->getMenBrands();
        $this->assign('womenbrands',$womenbrands);
        $this->assign('menbrands',$menbrands);
        $res = $this->good->lists();
        $this->assign('res',$res);
        // dump($res);
    	return $this->fetch();
    }

    // 删除订单
    public function del_ding(ProductModel $pro)
    {
        $oid = input('oid');
        $res = $pro->del_ding($oid);
        if ($res) {
            echo 1;
        } else {
            echo 2;
        }
    }
    
    public function show(Brand $brand)
    {
         $brandid = input('brand');
        // $class = input('class');
        // 1die;
        // 展示女装
        if(!empty($brandid)){
            $res = $this->good->mens($brandid);
        }else{
            $res = $this->good->mens1();
        }
         //dump($res);
        // 女装品牌
        $womenbrands = $brand->getWomenBrands();
        // 男装品牌
        $menbrands = $brand->getMenBrands();
        $this->assign('womenbrands',$womenbrands);
        $this->assign('menbrands',$menbrands);
        $this->assign('res',$res);
    	return $this->fetch();
    }

    // 显示服装列表页面(女装)
    public function womens(Brand $brand)
    {
        $brandid = input('brand');
        // $class = input('class');
        // 1die;
        // 展示女装
        if(!empty($brandid)){
            $res = $this->good->womens($brandid);
        }else{
            $res = $this->good->womens1();
        }
        // dump($res);die;
        // 女装品牌
        $womenbrands = $brand->getWomenBrands();
        // 男装品牌
        $menbrands = $brand->getMenBrands();
        $this->assign('womenbrands',$womenbrands);
        $this->assign('menbrands',$menbrands);
        $this->assign('res',$res);
    	return $this->fetch();
    }

    // 根据服装品牌查询所拥有的类型，并拿出来一件商品作展示
    public function getBrandClassAndGoods(ProductModel $pro)
    {
        $brandid = input('brand');
        $data = $pro->getBrandClassAndGoods($brandid);
        echo json_encode($data);
    }


    public function womens_single(Track $track,ProductModel $product)
    {
        $gid = input('gid');
        if (session('?uid')) {
            $track->addTrack(session('uid'),$gid);
        }
        // $product->incrementCount($gid);
        //查询商品信息
        $res = $this->good->womens_single($gid);
        $this->assign('res',$res);
        if ($res['is_miao'] == 1) {
            $arr = $this->good->miaosha($gid);
            $time = $arr[0]['time'];
            $this->assign('time',$time);
            // dump($arr);die;
        }
        //查看秒杀商品
        if ($res['is_miao'] == 1 ) {
            $price = $arr[0]['price'];
            $this->assign('price',$price);
        }
        //商品的评论
        $con = $this->good->single($gid);
        //dump($con);
        $this->assign('con',$con);
        $this->assign('gid',$gid);
        // die;
       //dump($res);
        return $this->fetch();
    }
    public function womens_accessories(Brand $brand)
    {
       $brandid = input('brand');
        // $class = input('class');
        // 1die;
        // 展示女装
        if(!empty($brandid)){
            $res = $this->good->mens($brandid);
        }else{
            $res = $this->good->mens1();
        }
         //dump($res);
        // 女装品牌
        $womenbrands = $brand->getWomenBrands();
        // 男装品牌
        $menbrands = $brand->getMenBrands();
        $this->assign('womenbrands',$womenbrands);
        $this->assign('menbrands',$menbrands);
        $res = $this->good->womens_accessories();
        $this->assign('res',$res);
        // dump($res);
        return $this->fetch();
    }
    public function mens(Brand $brand)
    {
         $brandid = input('brand');
        // $class = input('class');
        // 1die;
        // 展示女装
        if(!empty($brandid)){
            $res = $this->good->mens($brandid);
        }else{
            $res = $this->good->mens1();
        }
         //dump($res);
        // 女装品牌
        $womenbrands = $brand->getWomenBrands();
        // 男装品牌
        $menbrands = $brand->getMenBrands();
        $this->assign('womenbrands',$womenbrands);
        $this->assign('menbrands',$menbrands);
        $this->assign('res',$res);
        return $this->fetch();
    }
    
    public function mens_single()
    {
        return $this->fetch();
    }
    public function detail()
    {

        return $this->fetch();
    }

    // 添加购物车
    public function add()
    {
        $uid = session('uid');
        if ($uid == null) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $ip = ($ip == "::1") ? "127.0.0.1" : $ip;
            $data = [];
            $data['addip'] = ip2long($ip);
        }else{
            $data['uid'] = $uid;
        }
        $data1 = input();
        $data['addtime'] = time();
        $con = array_merge($data1,$data);
        $res = json_encode($con);
        //echo $res;
        $con =  $this->good->checkAdd($con);
        if ($con != null ) {
            echo 1;//添加成功
        }else{
             echo 2;//添加失败
        }
    }
    //添加收藏
    public function addsc()
    {
        $data['gid'] = input('gid');
        $data['uid'] = session('uid');
        $data['keep_time'] = time();
         //echo $data;
        $res = $this->good->addsc($data);
        if ($res) {
            echo 1;
        }else{
            echo 2;
        }
    }
    //秒杀商品加入订单
    public function addms()
    {
        $data = input();
        $res= json_encode($data);
        //echo $res;
        $data['now_price'] = input('price');
        $data['uid'] = session('uid');
        $data['addtime'] = time();
        $gid = input('gid');
        // //查询商品的信息
        $info = $this->good->goodinof($gid);
        if ($info) {
            $data['color'] = $info['colorstr'];
            $data['size'] = $info['sizenum'];
        }
        // $res= json_encode($con);
        // echo $res;
        unset($data['price']);
        $ms = $this->good->addms($data);
        if ($ms) {
           echo 1;
        }else{
            echo 2;
        }
    }
    //商品加入购物车
     public function addgood()
    {
        $data = input();
        $res= json_encode($data);
        $data['uid'] = session('uid');
        $data['addtime'] = time();
        $gid = input('gid');
        // //查询商品的信息
        $info = $this->good->goodinof($gid);
        if ($info) {
            $data['color'] = $info['colorstr'];
            $data['size'] = $info['sizenum'];
            $data['now_price'] = $info['price'];
        }
        // $res= json_encode($data);
        // // echo $res;
        // unset($data['price']);
        $ms = $this->good->addms($data);
        if ($ms) {
           echo 1;
        }else{
            echo 2;
        }
    }
}