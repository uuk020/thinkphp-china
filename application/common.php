<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use PHPMailer\PHPMailer\PHPMailer;

/**
 * 封装发送邮件
 *
 * @param $to       string  邮件地址
 * @param $name     string  邮件名字
 * @param $title    string  邮件标题
 * @param $content  string  邮件内容
 * @return array    发送成功或者失败
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendMail($to, $name, $title, $content)
{
    $msg = '邮件发送成功';
    $mailConfig = config('Mail');
    $mail = new PHPMailer();
    // 设置邮箱配置
    //$mail->SMTPDebug = $mailConfig['SMTPDebug'];
    $mail->isSMTP();
    $mail->Host       = $mailConfig['host'];
    $mail->SMTPAuth   = $mailConfig['SMTPAuth'];
    $mail->Username   = $mailConfig['username'];
    $mail->Password   = $mailConfig['password'];
    $mail->SMTPSecure = $mailConfig['SMTPSecure'];
    $mail->Port       = $mailConfig['port'];
    $mail->CharSet    = 'UTF-8';
    // 发送邮件
    $mail->setFrom($mailConfig['username'], '网站管理员');
    $mail->addAddress($to, $name);
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body    = $content;
    $status = $mail->send();
    if (!$status) {
        $msg = $mail->ErrorInfo;
        return [false, $msg];
    }
    return [true, $msg];
}

/**
 * 激活邮件
 *
 * @param $to
 * @param $name
 * @param $url
 *
 * @return array
 * @throws \PHPMailer\PHPMailer\Exception
 */
function regMail($to, $name, $url)
{
    $title      = '用户邮箱激活通知';
    $regContent = "<h2>请在20分钟内激活邮箱</h2><br/><p>为了你的账号安全, 请勿将链接发送给其他人. 请点击链接激活你的账号.</p> <br/> <a href='{$url}'>{$url}</a>";
    $result     = sendMail($to, $name, $title, $regContent);
    return $result;
}

function forgetEmail($to, $name, $url)
{
    $title      = '用户修改密码链接';
    $regContent = "<h2>请在20分钟内修改密码</h2><br/><p>为了你的账号安全,请勿将链接发给其他人.请点击链接修改你的密码</p><br/><a href='{$url}'>{$url}</a>";
    $result     = sendMail($to, $name, $title, $regContent);
    return $result;
}
/**
 * 随机生成Token
 *
 * @param  array  $param  参数数组
 *
 * @param  int     $len 盐值长度
 *
 * @return string  加密后字符串
 */
function randomStr(Array $param, $len = 5)
{
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $num = mt_rand(65, 90);
        $str .= chr($num);
    }
    $key = uniqid();
    return md5($param['username'] . $key . $str . $param['email']);
}

/**
 * 简单加密算法
 *
 * @param $str
 *
 * @param $key
 *
 * @return int
 */
function encode($str, $key)
{
    $base64 = base64_encode($str);
    $code   = $base64 ^ $key;
    return $code;
}

/**
 * 解密算法
 *
 * @param $str
 *
 * @param $key
 *
 * @return bool|string
 */
function decode($str, $key)
{
    return base64_decode($str^$key);
}
/**
 * 获取分类名称
 * @param integer $id 分类ID
 * @return array
 */
function getCategoryNames($id) {
    $category = config('category');
    foreach($category as $key => $cat) {
        foreach($cat as $catId => $categoryName) {
            if ($catId == $id) {
                return [$key, $categoryName];
            }
        }
    }
    return [];
}