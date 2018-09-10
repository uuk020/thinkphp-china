<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::rule('login', 'index/User/login', 'POST|GET');
Route::rule('register', 'index/User/register', 'POST|GET');
Route::rule('user/set', 'index/User/set', 'POST|GET');
Route::rule('user/:uid', 'index/User/home', 'POST|GET');
