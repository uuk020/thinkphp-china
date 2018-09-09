<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/22
 * Time: 22:02
 */

namespace app\admin\controller;

use think\Controller;


class Base extends Controller
{
    /**
     * 基本控制器的初始化, 来检测链接的权限.
     * @return array|void
     */
    public function _initialize()
    {
        if (!session('?username')) {
            $loginUrl = url('login/index');
            if(request()->isAjax()){
                return msg(111, $loginUrl, '登录超时');
            }
            $this->error('请登录', url('login/index'));
        }

        $control = lcfirst($this->request->controller());
        $action = lcfirst($this->request->action());
        if (authCheck($control . '/' . $action) == false) {
            if ($this->request->isAjax()) {
                return msg(403, '', '您没有权限');
            }
            $this->error('403 您没有权限');
        }
        $this->assign([
            'username' => session('username'),
            'roleName' => session('role'),
            'avatar'   => session('avatar'),
        ]);
    }
}