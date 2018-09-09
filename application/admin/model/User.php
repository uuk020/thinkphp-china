<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/23
 * Time: 23:10
 */

namespace app\admin\model;


use think\exception\PDOException;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    // 自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;
    // 输出格式化时间
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 加密密码
     *
     * @param  string $value  密码字段
     * @return string 加密字符串
     */
    public function setPasswordAttr($value)
    {
        return md5(md5($value) . 'imooc');
    }

    /**
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList($where, $offset, $limit)
    {
        return self::withTrashed()->alias('u')->field('u.id, u.username, u.email, u.create_time, u.update_time, u.delete_time, u.blocked, r.role_name')
            ->join('tp_role r', 'u.role_id = r.id')->where($where)
            ->limit($offset, $limit)->order('u.id desc')
            ->select();
    }

    /**
     * 根据查询条件获取用户总数
     * @param  string $where 查询条件
     * @return int 总数
     */
    public function getAllUser($where)
    {
        return intval($this->where($where)->count());
    }

    /**
     *  插入管理员
     * @param  array $param  用户信息
     * @return array 插入成功与失败提示数组
     */
    public function insertUser($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            if (false == $result) {
                return msg(-1, '', $this->getError());
            } else {
                return msg(1, url('user/index'), '添加用户成功');
            }

        } catch (\PDOException $exception) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' . $exception->getMessage());
        }
    }

    /**
     * 编辑管理员
     * @param  array $param  用户信息
     * @return array 编辑成功与失败提示数组
     */
    public function editUser($param)
    {
        try {
            $result = $this->isUpdate(true)->save($param, ['id' => $param['id']]);
            if (false == $result) {
                return msg(-1, '', $this->getError());
            } else {
                return msg(1, url('user/index'), '编辑成功');
            }
        } catch (\PDOException $exception) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:'. $exception->getMessage());
        }
    }

    /**
     * 删除管理员
     * @param  int   $userId 管理员ID
     * @return array 删除成功与失败提示数组
     */
    public function delUser($userId)
    {
        try {
            self::destroy(['id' => $userId]);
            return msg(1, url('user/index'), '删除成功');
        } catch (\PDOException $exception) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' . $exception->getMessage());
        }
    }

    public function blockedUser($userId)
    {
        try {
            $result = $this->isUpdate(true)->save(['blocked' => 1], ['id' => $userId]);
            if (false == $result) {
                return msg(-1, '', $this->getError());
            } else {
                return msg(1, url('User/index'), '封禁成功');
            }
        } catch (\Exception $exception) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' . $exception->getMessage());
        }
    }

    /**
     * @param $username
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNameUser($username)
    {
        return $this->where('username|nickname', $username)
                ->find()
                ->toArray();
    }

    /**
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneUser($id)
    {
        try {
            $result = $this->where('id', $id)->find();
        } catch (\PDOException $e) {
            return msg(-1, '', $e->getMessage());
        }
        return $result;
    }

}