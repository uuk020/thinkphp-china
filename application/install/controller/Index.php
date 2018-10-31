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
        if (!in_array(session('step'), [1, 3], true)) $this->redirect('install/index/index');
        if ($this->request->isPost()) {
            if (!$this->request->post('username') && !$this->request->post('resetPassword')) {
                return json(['status' => 0, 'message' => '缺少相应参数'], 400);
            }
            $databaseUserConfig = [
                'hostname' => $this->request->post('hostname', '127.0.0.1'),
                'hostport' => $this->request->post('hostport', '3306'),
                'username' => $this->request->post('username', 'root'),
                'password' => $this->request->post('password', ''),
            ];
            $adminName = $this->request->post('admin_name');
            $adminPassword = $this->request->post('admin_password');
            $getDatabaseConfig = APP_PATH . 'database.php';
            if (setConfig($getDatabaseConfig, $databaseUserConfig)) {
                session('step', 2);
                if (!empty($adminName) && !empty($adminPassword)) {
                    session('admin', $adminName);
                    session('password', password_hash($adminPassword, PASSWORD_BCRYPT));
                }
                return json(['status' => 1, 'message' => '设置成功'], 200);
            }
            return json(['status' => 0, 'message' => '设置失败'], 200);
        }
        return $this->fetch();
    }

    /**
     * 环境要求配置
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
        session('step', 3);
        $this->assign([
            'env' => $env,
            'func' => $func,
            'files' => $files
        ]);
        return $this->fetch();
    }

    /**
     * 环境检测
     *
     * @return mixed
     */
    public function stepThree()
    {
        if ($this->request->isAjax()) {
            if (session('error')) {
                $this->error('环境检测出现一些问题, 请重新设置环境');
            } else {
                $this->success('通过环境检测', 'install/index/stepThree');
            }
        }
        if (session('step') !== 3) $this->redirect('install/index/index');
        session('error', false);
        session('step', 4);
        return $this->fetch();
    }

    /**
     * 填写数据库
     *
     * @return mixed
     * @throws \Exception
     */
    public function stepFour()
    {
        if ($this->request->isPost()) {
            $database = $this->request->post('database', '');
            $prefix = $this->request->post('prefix', '');
            $cover = $this->request->post('cover', 0);
            if (empty($database) || empty($prefix)) {
                $this->error('请填写完整数据库配置');
            }
            $databaseConfig = [
                'database' => $database,
                'prefix'   => $prefix,
            ];
            $getDatabaseConfig = APP_PATH . 'database.php';
            if (!setConfig($getDatabaseConfig, $databaseConfig)) {
                $this->error('设置失败');
            }
            try {
                $db = config('database');
                unset($db['database']);
                $connect = DB::connect($db);
                $databaseName = config('database.database');
                if (!$cover) {
                    $sql = "SELECT * FROM information_schema.schemata WHERE schema_name = '{$databaseName}'";
                    $result = $connect->execute($sql);
                    if ($result) {
                        $this->error('该数据库已存在, 请更换名称或者覆盖');
                    }
                }
                $sql = "CREATE DATABASE IF NOT EXISTS `{$databaseName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
                $connect->execute($sql) || $this->error($connect->getError());
                unset($connect);
                $this->success('开始安装', 'install/index/stepFour');
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
            }
        } else {
            if (session('step') !== 4) $this->redirect('install/index/index');
            session('step', 4);
            return $this->fetch();
        }
    }

    /**
     * 安装完成
     *
     * @return mixed
     */
    public function complete()
    {
        if (session('step') !== 4) $this->error('请按照步骤安装系统', url('install/index/stepOne'));
        if (session('error')) {
            $this->error('安装出错, 请重新安装!', url('install/index/stepOne'));
        } else {
            file_put_contents(ROOT_PATH . 'forum.lock', 'lock install forum');
            session('step', null);
            session('error', null);
        }
        return $this->fetch();
    }
    /**
     * 重装系统
     *
     * @return mixed
     */
    public function reset()
    {
        $params = $this->request->param();
        if ($this->request->isPost()) {
            $username = $params['username'];
            $password = $params['password'];
            if (empty($username) && empty($password)) $this->error('用户名和密码不能为空');
            $userTable = config('database.prefix') . 'user';
            $userInfo = Db::table($userTable)->where('username', $username)->column('username, id, password');
            if (!$userInfo) $this->error('该用户不存在');
            if (!password_verify($password, $userInfo[$username]['password'])) {
                $this->error('密码错误');
            }
            if ((int)$userInfo[$username]['id'] !== 1) {
                $this->error('该用户不是管理员!');
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