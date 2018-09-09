<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/28
 * Time: 22:41
 */

namespace app\admin\controller;
use app\admin\model\Role as RoleModel;
use app\admin\model\User as UserModel;
use think\Controller;
use think\Request;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch('/login');
    }

    /**
     * 登录
     *
     * @param \think\Request $request
     *
     * @return string|\think\response\Json
     */
    public function doLogin(Request $request)
    {
        $username = $request->post('user_name');
        $password = $request->post('password');
        $captcha  = $request->post('code');
        $result   = $this->validate(compact('username', 'password', 'captcha'), 'User');
        if (true !== $result) {
            return json(msg(-1, '1', $result));
        }
        if (!captcha_check($captcha)) {
            return json(msg(-2, '2', '验证码错误'));
        }
        $user = new UserModel();
        try {
            $hasUser = $user->getNameUser($username);
            if (empty($hasUser)) {
                return json(msg(-3, '3', '用户不存在'));
            }
            if (!password_verify($password, $hasUser['password'])) {
                return json(msg(-4, '', '密码输入不正确'));
            }
            $roleModel = new RoleModel();
            $userRole = $roleModel->getRoleInfo($hasUser['role_id']);
            session('username', $username);
            session('id', $hasUser['id']);
            session('avatar', $hasUser['avatar']);
            session('role', $userRole['role_name']);  // 角色名
            session('rule', $userRole['rule']);  // 角色节点
            session('action', $userRole['action']);  // 角色权限
            return json(msg(1, url('admin/index/index'), '登录成功'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        session('username', null);
        session('id', null);
        session('role', null);  // 角色名
        session('rule', null);  // 角色节点
        session('action', null);  // 角色权限

        $this->redirect(url('index'));
    }
}