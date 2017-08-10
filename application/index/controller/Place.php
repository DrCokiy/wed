<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Place as PlaceModel;


class Place extends Controller
{
	protected $area;
    public function _initialize()
    {
        $this->model = new PlaceModel();
    }
    public function place()
    {
    	$res = $this->model->checkPlace();
    	$this->assign('res',$res);
    	return $this->fetch();

    }
    
}