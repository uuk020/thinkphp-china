<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/2/18
 * Time: 21:58
 */

namespace app\index\model;

use think\Model;

class Info extends Model
{
    // 自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;
    // 输出格式化时间
    protected $dateFormat = 'Y-m-d H:i:s';

}