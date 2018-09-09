<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/29
 * Time: 21:37
 */

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        ['username', 'require', '用户名不能为空'],
        ['password', 'require', '密码不能为空'],
        ['captcha', 'require', '验证码不能为空'],
    ];
}