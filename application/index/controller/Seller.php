<?php
namespace app\index\controller;
use think\Controller;
class Seller extends Controller
{
   
       //酒店预订页面
    public function cases()
    {
    	return view('cases');
    }
        //门票预订页
    public function taocan()
    {
    	return view('taocan');
    }
    //特色食品
    public function taocanshow()
    {
    	return view('taocanshow');
    }
   }