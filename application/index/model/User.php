<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/2/15
 * Time: 0:33
 */

namespace app\index\model;

use think\Model;

/**
 * 用户模型类
 * 功能1: 注册用户和登录用户逻辑处理
 * 功能2: 重置密码和修改密码
 * 功能3: 检测用户激活邮箱TOKEN
 *
 * @package app\index\model
 */
class User extends Model
{
    /**
     * 自动写入时间
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;
    /**
     * 时间格式
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 加密密码
     *
     * @param  string $value  密码字段
     *
     * @return string
     */
    public function setPasswordAttr($value)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($value, PASSWORD_BCRYPT, $options);
    }

    /**
     * 注册用户功能
     *
     * @param array  $userInfo POST传递的数据
     *
     * @return array
     *
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register(array $userInfo)
    {
        if (!captcha_check($userInfo['verifycode'])) {
            return array(false, '验证码输入有误, 请重新输入');
        }
        if ($this->where('email', $userInfo['email'])->find()) {
            return array(false, '该邮箱已注册');
        }
        if ($this->where('username', $userInfo['username'])->find()) {
            return array(false, '该用户名已注册');
        }
        if ($userInfo['password'] !== $userInfo['password_confirmation']) {
            return array(false, '两次密码不一致');
        }
        $regTime = time();
        $token   = randomStr($userInfo, 6);
        $flag    = encode($userInfo['username'], config('Mail.key'));
        $tokenTime = $regTime + 20 * 60;
        $otherInfo = [
            'city'        => '',
            'company'     => '',
            'signature'   => '',
            'email_token' => $token,
            'update_time' => $tokenTime,
        ];
        $status = regMail($userInfo['email'], $userInfo['username'], url('User/active', "flag={$flag}&token={$token}", 'html', true));
        if (!$status[0]) {
            return [false, '注册失败, 邮件服务出了问题'];
        }
        $this->allowField(true)->save($userInfo);
        $this->info()->save($otherInfo);
        $this->defaultExperience($this->id);
        return [true, '注册成功, 请查看邮箱'];
    }


    private function defaultExperience($uid)
    {
        define('BASIC_EXPERIENCE', 20);
        Experience::create([
           'uid'        => $uid,
           'experience' => BASIC_EXPERIENCE,
        ]);
    }

    /**
     * 登录用户
     *
     * @param array $userInfo 用户输入信息
     *
     * @return array 返回执行数组
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login(array $userInfo)
    {
         $user = self::get(['email|username' => $userInfo['login'], 'delete_time' => null], 'info');
         if (empty($user)) {
             return array(false, '用户不存在');
         }
         $userData = $user->toArray();
         if (!password_verify($userInfo['password'], $userData['password'])) {
            return array(false, '用户名或密码错误');
         }
         if ($userData['blocked'] == 1) {
             return array(false, '用户被封禁');
         }
         if (Experience::get(['uid' => $userData['id']])) {
             $userExperienceInfo = Experience::get(['uid' => $userData['id']])->toArray();
         } else {
             $userExperienceInfo = ['experience' => 0];
         }
         $experience         = $userExperienceInfo['experience'];
         $saveSession = [
             'id'           => $userData['id'],
             'nickname'     => $userData['nickname'],
             'email'        => $userData['email'],
             'avatar'       => $userData['avatar'],
             'role_id'      => $userData['role_id'],
             'email_status' => $userData['info']['email_status'],
             'experience'   => $experience,
         ];
         session('user', $saveSession);
         return array(true, '登录成功');
    }


    /**
     * 修改用户信息
     *
     * @param integer $id   用户ID
     * @param array   $info POST传递来用户修改信心
     *
     * @return array  返回执行后结果数组
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function setInfo($id, array $info)
    {
        $user = self::get($id, 'info');
        $userInfo = $user->toArray();
        $email = $info['email'];
        $nickname = $info['nickname'];
        if ($userInfo['nickname'] !== $nickname) {
            $user->nickname = $nickname;
            session('user.nickname', $nickname);
            $user->save();
        }
        if ($userInfo['email'] !== $email) {
            $checkEmail = self::get(['email' => $email]);
//          检测填写邮箱是否存在
            if (!$checkEmail) {
                $user->email = $email;
                session('user.email', $email);
                $user->save();
//              修改邮箱, 把验证状态设置为未验证
                $info['email_status'] = 0;
            } else {
                return array(false, '所填写邮箱已有注册');
            }
        }
        unset($info['nickname']);
        unset($info['email']);
        if (!$user->info->save($info)) {
            return array(false, '服务器内部发生错误. 修改失败');
        }
        return array(true, '个人资料更新成功');
    }

    /**
     * 用户设置新密码
     *
     * @param integer  $id       用户ID
     * @param array    $passArr  POST传递用户密码数组
     *
     * @return array
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function resPassword($id, array $passArr)
    {
        $user = self::get($id)->toArray();
        if (!password_verify($passArr['nowpass'], $user['password'])) {
            return [false, '密码错误'];
        }
        if ($passArr['pass'] !== $passArr['repass']) {
            return [false, '两次密码不一致'];
        }
        self::update(['id' => $id, 'password' => $passArr['pass']]);
        return [true, '密码修改成功'];
    }

    /**
     * 检查用户激活token
     *
     * @param string  $flag   用户加密信息
     * @param string  $token  GET传递token
     * @param integer $time   激活时间
     *
     * @return array
     *
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function checkEmailToken($flag, $token, $time)
    {
        $username = decode($flag, config('Mail.key'));
        $user = self::get(['username' => $username], 'info');
        $userInfo = $user->toArray();
        if (!$user) {
            return [false, '用户不存在'];
        }
        if ($userInfo['info']['email_status']) {
            return [true, '用户邮箱已验证'];
        }
        if ($token !== $userInfo['info']['email_token']) {
            return [false, '验证码不正确'];
        }
        if (strtotime($userInfo['info']['update_time']) <= $time){
            return [false, '验证码已经过期'];
        }
        $updateInfo = [
            'email_token'  => '',
            'email_status' => 1
        ];
        if (!$user->info->save($updateInfo)) {
            return [false, '数据库内部发生错误'];
        }
        return [true, '激活成功'];
    }

    /**
     * 忘记密码逻辑处理
     *
     * @param array $param 用户POST传递数组
     *
     * @return array
     */
    public function forgetPassword($param = [])
    {
        if (!empty($param)) {
            try {
                $userEmail = self::get(['email' => $param['email']]);
                if (!$userEmail) {
                    return [false, '该用户没有注册'];
                }
                if (!captcha_check($param['vercode'])) {
                    return array(false, '验证码输入有误, 请重新输入');
                }
                $userEmailInfo = $userEmail->toArray();
                $flag           =  encode($userEmailInfo['username'], config('Mail.key'));
                $token          =  randomStr($userEmailInfo, 5);
                $setPasswordUrl = url('index/user/setPassword', "flag={$flag}&token={$token}", true, true);
                $result         = forgetEmail($userEmailInfo['email'], $userEmailInfo['nickname'], $setPasswordUrl);
                if (!$result[0]) {
                    return [false, '邮件服务出了问题'];
                }
                $userEmail->info->save([
                    'email_token' => $token,
                    'update_time' => time() + 20 * 60,
                ]);
                return [true, '发送成功'];
            } catch (\Exception $exception) {
                return [false, '数据库发生异常'];
            }
        }
    }

    public static function collectionCount($uid)
    {
        try {
             $data = self::withCount('collection')->where('id', $uid)->find();
             if ($data) {
                 $data = $data->toArray();
                 return $data['collection_count'];
             } else {
                 return '没有收藏';
             }
        } catch (\Exception $e) {
             return '数据库异常';
        }
    }


    /**
     * 与用户信息类一对一关联
     *
     * @return \think\model\relation\HasOne
     */
    public function info()
    {
        return $this->hasOne('Info');
    }

    /**
     * 一对一关联积分表
     *
     * @return \think\model\relation\HasOne
     */
    public function experience()
    {
        return $this->hasOne('Experience');
    }

    public function collection()
    {
        return $this->hasMany('Collection', 'uid');
    }
}