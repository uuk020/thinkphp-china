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
Route::rule('login', 'index/user/login', 'POST|GET');
Route::rule('register', 'index/user/register', 'POST|GET');
Route::rule('logout', 'index/user/logout');
Route::rule('user', 'index/user/index');
Route::rule('user/set', 'index/user/set');
Route::rule('user/:uid', 'index/user/home');
Route::rule('user/message', 'index/user/message');
