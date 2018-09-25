<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/9/24
 * Time: 17:12
 */

namespace app\install\controller;

use think\Controller;
use think\Db;
use think\Exception;

class Index extends Controller
{
    /**
     * 前置操作, 访问install模块都检测是否安装
     *
     * @var array
     */
    protected $beforeActionList = ['checkHasInstall' => ['except' => 'reset']];

    /**
     * 检测是否安装过
     *
     * @return bool
     */
    public function checkHasInstall()
    {
        if (hasInstallFile()) {
            $this->redirect('install/index/reset');
        }
        return true;
    }

    /**
     * 安装首页
     *
     * @return mixed
     */
    public function index()
    {
        $this->assign('next', '下一步');
        session('step', 1);
        session('error', false);
        return $this->fetch();
    }

    public function stepOne()
    {
        
        return $this->fetch();
    }

    public function stepTwo()
    {
        echo checkMySQLVersion();
       //return $this->fetch();
    }

    public function reset()
    {
        $password = $this->request->param('password');
        if ($this->request->isPost()) {
            if (!password_verify(config('password'), $password)) {
                $this->error('密码不一致');
            }
            try {
                if (file_exists(ROOT_PATH . 'forum.lock')) {
                    unlink(ROOT_PATH . 'forum.lock');
                    $dataName = config('database.database');
                    $sql = "DROP DATABASE IF EXISTS {$dataName}";
                    Db::execute($sql);
                    $this->success('重置成功', 'install/index/index');
                } else {
                    throw new Exception('文件不存在');
                }
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        }
        return $this->fetch();
    }

}