<?php
namespace app\admin\controller;

use think\Request;

class Error 
{
    public function index(Request $request)
    {
        //根据当前控制器名来判断要执行那个城市的操作
        $cityName = $request->controller();
        return $this->_action($cityName);
    }
    
    //注意 city方法 本身是 protected 方法
    protected function _action($name)
    {
        //和$name这个城市相关的处理
         return 'no '.$name.' action';
    }

    public function _empty($name)
    {
        //把所有城市的操作解析到city方法
        return 'no '.$name.' function';
    }
    
}