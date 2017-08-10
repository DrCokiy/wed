<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Image as ImageModel;

class Image extends Controller
{
    protected $image;
    public function _initialize()
    {
        $this->image = new ImageModel();
    }
    //A字型婚纱
    public function akind()
    {
        $class = input('class');
        $data = $this->image->akind($class);
        $this->assign('data',$data);
    	return $this->fetch();
    }
    //精品展示
    public function gallery()
    {
        $data = $this->image->gallery();
        $this->assign('data',$data);
    	return $this->fetch();
    }
    public function minikind()
    {
        $class = input('class');
        $data = $this->image->minikind($class);
        $this->assign('data',$data);
    	return $this->fetch();
    }
    public function photo()
    {
    	return $this->fetch();
    }
    public function pkind()
    {
         $class = input('class');
        $data = $this->image->pkind($class);
        $this->assign('data',$data);
    	return $this->fetch();
    }
    public function skind()
    {
        $class = input('class');
        $data = $this->image->skind($class);
        $this->assign('data',$data);
    	return $this->fetch();
    }
    public function tkind()
    {
        $class = input('class');
        $data = $this->image->tkind($class);
        $this->assign('data',$data);
    	return $this->fetch();
    }
    public function west()
    {
    	return $this->fetch();
    }
    public function video()
    {
        return $this->fetch();
    }
    public function video_detail()
    {
        return $this->fetch();
    }
}