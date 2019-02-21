<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// Route::get('/', function () {
//     return 'Hello world!';
// });
// Route::post('face_sets', 'api/ai/face_sets'); // 定义POST请求路由规则

// Route::post('face_deal', 'api/ai/face_deal'); // 定义POST请求路由规则

Route::post(
    [
        'face_sets' => 'api/ai/face_sets',
        'face_deal' => 'api/ai/face_deal',
    ]
); // 定义POST请求路由规则

Route::get('user/:id', function ($id) {
    return $id;
}); // 定义GET请求路由规则
// Route::put('new/:id', 'News/update'); // 定义PUT请求路由规则
// Route::delete('new/:id', 'News/delete'); // 定义DELETE请求路由规则
// Route::any('new/:id', 'News/read'); // 所有请求都支持的路由规则

//Route::rule([
// '路由规则1'=>'路由地址和参数',
// '路由规则2'=>['路由地址和参数','匹配参数（数组）','变量规则（数组）']
// ...
// ],'','请求类型','匹配参数（数组）','变量规则');
