<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/2/15
 * Time: 18:01
 */

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username|nickname' => 'require|max:20|min:5|token',
        'email' => 'require|email',
        'password' => 'require|min:6',
    ];
}