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

    /**
     * 设置相关数据
     *
     * @return mixed|\think\response\Json
     */
    public function stepOne()
    {
        if (!in_array(session('step'), [1, 3])) $this->redirect('install/index/index');
        if ($this->request->isPost()) {
            if (!$this->request->post('username') && !$this->request->post('resetPassword')) {
                return json(['status' => 0, 'message' => '缺少相应参数'], 400);
            }
            $databaseUserConfig = [
                'username' => $this->request->post('username'),
                'password' => $this->request->post('password'),
            ];
            $resetPassword = $this->request->post('resetPassword');
            $installUserConfig = ['reset_password' => password_hash($resetPassword, PASSWORD_DEFAULT)];
            $getDatabaseConfig = APP_PATH . 'database.php';
            $getInstallConfig = APP_PATH . 'install' . DS . 'config.php';
            if (setConfig($getDatabaseConfig, $databaseUserConfig) && setConfig($getInstallConfig, $installUserConfig)) {
                session('step', 2);
                return json(['status' => 1, 'message' => '设置成功'], 200);
            }
            return json(['status' => 0, 'message' => '设置失败'], 200);
        }
        return $this->fetch();
    }

    /**
     * 检测环境
     *
     * @return mixed
     */
    public function stepTwo()
    {
        if (session('step') !== 2 ) {
            $this->redirect('install/index/stepOne');
        }
        $env = checkEnv();
        $func = checkFunc();
        $files = checkDirOrFile();
        $this->assign([
            'env' => $env,
            'func' => $func,
            'files' => $files
        ]);
        return $this->fetch();
    }

    public function stepThree()
    {

    }

    /**
     * 重装系统
     *
     * @return mixed
     */
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