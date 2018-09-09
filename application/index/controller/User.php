<?php
namespace app\index\controller;


use think\Exception;
use app\index\model\Post       as PostModel;
use app\index\model\User       as UserModel;
use app\index\model\Reply      as ReplyModel;
use app\index\model\Message    as MessageModel;
use app\index\model\Collection as CollectionModel;

/**
 * 用户控制器
 *
 * @package app\index\controller
 */
class User extends Base
{
    private $_responseData = [];

    /**
     * 注册用户
     *
     * @return mixed|\think\response\Json
     *
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register()
    {
        if (session('?user')) {
            $this->success('你已经登录过了', 'Index/index');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['avatar'] = $this->request->domain() . '/static/images/avatar.jpg';
            // 实例化模型对象新增数据
            $user = new UserModel();
            $result = $user->register($data);
            if ($result[0]) {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/login')];
            } else {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/register')];
            }
            return json($this->_responseData);
        }
        return $this->fetch();
    }

    /**
     * 处理用户激活邮件
     *
     * @param string  $flag  加密用户名
     * @param string  $token 随机生成token
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function active($flag, $token)
    {
        $user = new UserModel();
        $time = time();
        $result = $user->checkEmailToken($flag, $token, $time);
        if (!$result[0]) {
            $this->error($result[1], 'User/set');
        }
        $this->success($result[1], 'Index/index');
    }

    /**
     * 登录用户
     *
     * @return mixed|\think\response\Json
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login()
    {
        if (session('?user')) {
            $this->success('你已经登录过了', 'Index/index');
        }
        if ($this->request->isPost()) {
            $login = $this->request->post();
            $user = new UserModel();
            $result = $user->login($login);
            if ($result[0]) {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('Index/index')];
            } else {
                $this->_responseData = ['status' => 1, 'msg' => $result[1]];
            }
            return json($this->_responseData);
        }
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('user', null);
        cookie('userToken', null);
        $this->success('退出成功', 'Index/index');
    }

    /**
     * 用户中心
     *
     * @return mixed
     *
     * @throws \think\Exception
     */
    public function index()
    {
        if (!session('?user')) {
            $this->error('请登录账号', 'User/login');
        }
        $userId = session('user.id');
        try {
            $userPosts           = is_array(PostModel::userPosts($userId)) ? PostModel::userPosts($userId) : '';
            $userCollectionCount = is_int(UserModel::collectionCount($userId)) ? UserModel::collectionCount($userId) : 0;
            $userCollectionModel = new CollectionModel();
            $userCollectionPosts = $userCollectionModel->getCollection($userId);
            $this->assign([
                'userPosts'       => $userPosts,
                'userId'          => $userId,
                'collectionCount' => $userCollectionCount,
                'collectionPosts' => $userCollectionPosts,
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
        return $this->fetch();
    }

    /**
     * 个人主页
     *
     * @param $uid
     * @return mixed
     *
     * @throws \think\Exception
     */
    public function home($uid)
    {
        try {
            $userInfo   = UserModel::get([ 'id' => $uid], 'info')->toArray();
            $userPosts  = is_array(PostModel::userPosts($uid)) ? PostModel::userPosts($uid) : '';
            $userRelies = is_array(ReplyModel::userRelies($uid)) ? ReplyModel::userRelies($uid) : '';
            $this->assign([
                'userInfo'   => $userInfo,
                'userPosts'  => $userPosts,
                'userRelies' => $userRelies,
            ]);
        } catch (Exception $exception) {
            throw $exception;
        }
        return $this->fetch();
    }

    /**
     * 用户设置资料
     *
     * @return mixed|\think\response\Json
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function set()
    {
        if (!session('?user')) {
            $this->error('请登录', 'login');
        }
        $id = session('user.id');
        $user = UserModel::get($id, 'info');
        if ($this->request->isPost()) {
            $info = $this->request->post();
            $result = UserModel::setInfo($id, $info);
            if ($result[0]) {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('Index/index')];
            } else {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/set')];
            }
            return json($this->_responseData);
        }
        $data = $user->toArray();
        $this->assign([
            'data'   => $data,
            'userId' => $id,
        ]);
        return $this->fetch();
    }

    /**
     * 用户上传头像
     *
     * @return \think\response\Json
     *
     * @throws \think\exception\DbException
     */
    public function upload()
    {
        if (!session('user')) {
            $this->error('你没有登录', 'User/login');
        }
        $id = session('user.id');
        $this->_responseData = ['status' => 0, 'msg' => '上传头像成功'];
        $file = $this->request->file('file');
        if (!$file) {
            $this->_responseData = ['status' => -1, 'msg' => '服务器内部错误'];
        }
        $pathName = ROOT_PATH . 'public' . DS . 'uploads';
        $fileInfo = $file->validate(['size' => 153600, 'ext' => 'jpg,png,gif'])->move($pathName);
        if (!$fileInfo) {
            $error = $file->getError();
            $this->_responseData = ['status' => -2, 'msg' => $error];
        }
        $linkPath = $this->request->domain() . '/uploads/' . str_replace('\\', '/',$fileInfo->getSaveName());
        $user = UserModel::get($id);
        $user->avatar = $linkPath;
        if (!$user->save()) {
            $this->_responseData = ['status' => -3, 'msg' => '数据库发生异常'];
        }
        session('user.avatar', $linkPath);
        return json($this->_responseData);
    }

    /**
     * 用户修改密码
     *
     * @return \think\response\Json
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function password()
    {
        if (!session('?user')) {
            $this->error('你没有登录', 'User/login');
        }
        $id = session('user.id');
        if ($this->request->post()) {
            $passwordInfo = $this->request->post();
            $user = new UserModel();
            $result = $user->resPassword($id, $passwordInfo);
            if ($result[0]) {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/set')];
            } else {
                $this->_responseData = ['status' => -1, 'msg' => $result[1]];
            }
        }
        return json($this->_responseData);
    }

    /**
     * 消息页面
     *
     * @return mixed
     */
    public function message()
    {
        $user = session('user');
        $userId = $user['id'];
        $messageInfo = MessageModel::messageInfo($userId);
        $this->assign([
            'messageInfo' => $messageInfo,
            'userId'      => $userId,
        ]);
        return $this->fetch();
    }


    /**
     * 邮件验证页面
     *
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     */
    public function activate()
    {
        if (!session('?user')) {
            $this->error('你没有登录', 'User/login');
        }
        $id = session('user.id');
        try {
            $user = UserModel::get($id, 'info');
            $userInfo = $user->toArray();
            $userEmail = $userInfo['email'];
            $status = $userInfo['info']['email_status'];
            $this->assign([
                'email'  => $userEmail,
                'status' => $status,
                'userId' => $id,
            ]);
            if ($this->request->post('email')) {
                $this->againEmail($user, $status);
                return json($this->_responseData);
            }
        } catch (Exception $exception) {
            return json(['status' => 1, 'msg' => '内部发生错误']);
        }
        return $this->fetch();
    }

    /**
     * 再次验证邮件
     *
     * @param \app\index\model\User $user   用户模型
     * @param  integer              $status 邮件激活状态
     * @throws \think\Exception
     */
    public function againEmail(UserModel $user, $status)
    {
        $this->_responseData = ['status' => 0, 'msg' => '发送成功'];
        $userInfo = $user->toArray();
        if ($status) {
            $this->_responseData = ['status' => 1, 'msg' => '邮件已激活'];
        }
        $token = randomStr($userInfo, 6);
        $flag  = encode($userInfo['username'], Config('Mail.key'));
        $updateInfo = [
            'email_token' => $token,
            'update_time' => time() + 20 * 60,
        ];
        try {
            $user->info->save($updateInfo);
            regMail($userInfo['email'], $userInfo['username'], url('User/active', "flag={$flag}&token={$token}", 'html', true));
        } catch (\Exception $e) {
            $this->_responseData = ['status' => 1, 'msg' => '邮件发送错误'];
        }
    }

    /**
     * 忘记密码页面
     *
     * @return mixed|\think\response\Json
     */
    public function forget()
    {
        if ($this->request->post()) {
            $userInputFrom  = $this->request->post();
            $user = new UserModel();
            $result = $user->forgetPassword($userInputFrom);
            if ($result[0]) {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/login')];
            } else {
                $this->_responseData = ['status' => 0, 'msg' => $result[1], 'action' => url('User/forget')];
            }
            return json($this->_responseData);
        }
        return $this->fetch();
    }

    /**
     * 重置密码
     *
     * @param string $flag   加密用户名
     * @param string $token  临时token
     *
     * @return mixed|\think\response\Json
     */
    public function setPassword($flag, $token)
    {
        $username = decode($flag, config('Mail.key'));
        try {
            $user = UserModel::get(['username' => $username], 'info');
            $userToArray = $user->toArray();
            if ($this->request->isGet()) {
                if (strtotime($userToArray['info']['update_time']) <= time()) {
                    $this->error('激活码已过期', 'User/login');
                }
                if ($userToArray['info']['email_token'] !== $token) {
                    $this->error('激活码不正确', 'User/login');
                }
            }
            if ($this->request->isPost()) {
                $userInput = $this->request->post();
                $setToken = '';
                $this->_responseData = ['status' => 0, 'msg' => '修改密码成功', 'action' => url('User/login')];
                if ($userInput['pass'] !== $userInput['repass']) {
                    $this->_responseData = ['status' => 0, 'msg' => '两次密码不正确'];
                }
                if (!captcha_check($userInput['vercode'])) {
                    $this->_responseData = ['status' => 0,  'msg' => '验证码输入有误, 请重新输入'];
                }
                $user->isUpdate(true)->save(['password' => $userInput['pass']]);
                $user->info->save(['email_token' => $setToken, 'update_time' => time()]);
                return json($this->_responseData);
            }
        } catch (Exception $exception) {
            $this->error('数据库异常', 'User/login');
        }
        $this->assign('username', $username);
        return $this->fetch();
    }
}
