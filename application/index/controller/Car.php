<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Car as CarModel;

class Car extends Controller
{
	protected $car;
    public function _initialize()
    {
        $this->car = new CarModel();
    }

    public function car()
    {
    	$res = $this->car->checkCar();
    	$this->assign('res',$res);
    	return $this->fetch() ;
    }
    
}