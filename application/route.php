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
// 用户相关路由
Route::rule('/', 'index/index/index');
Route::rule('login', 'index/user/login', 'POST|GET');
Route::rule('register', 'index/user/sign_up', 'POST|GET');
Route::rule('logout', 'index/user/logout');
Route::rule('user', 'index/user/index');
Route::rule('user/set', 'index/user/set');
Route::rule('users/:uid', 'index/user/home');
Route::rule('user/message', 'index/user/message');
Route::post('user/upload', 'index/user/upload');
Route::post('user/password', 'index/user/password');
Route::rule('user/forget', 'index/user/forget');
Route::rule('user/reset/:flag/:token', 'index/user/setPassword');

// 帖子相关路由
Route::get('topics', 'index/topic/index', [], []);
Route::rule('articles/create', 'index/topic/add', 'GET|POST');
Route::get('articles/:id', 'index/topic/detail');
Route::get('category/:name/[:page]/[:filter]', 'index/topic/category', [], []);
Route::rule('articles/edit/:id', 'index/topic/edit', 'GET|POST');

// 回帖相关路由
Route::post('replies/create', 'index/reply/newReply', [], []);
