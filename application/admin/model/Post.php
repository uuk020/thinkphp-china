<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/24
 * Time: 0:21
 */

namespace app\admin\model;

use think\exception\PDOException;
use think\Model;
use traits\model\SoftDelete;

class Post extends Model
{
    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    // 自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;
    // 输出格式化时间
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 审核帖子内容
     * @param  integer $postId  帖子ID
     * @param  string  $content  修改内容
     * @return array   修改成功和失败提示数组
     */
    public function editPost($postId, $content)
    {
        try {
            $result = $this->save([
               'content' => $content
            ], ['id' => $postId]);
            if (false == $result) {
                return msg(1, '', '修改成功');
            } else {
                return msg(-1, '', '修改失败');
            }
        } catch (\PDOException $e) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }

    /**
     * 封帖子
     * @param  int       $postId  帖子ID
     * @return false|int 封成功或者失败
     */
    public function blockPost($postId)
    {
        try {
            $this->save(['blocked' => 1], ['id' => $postId]);
            return msg(1, '', '封禁文章成功');
        } catch (\PDOException $e) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' .$e->getMessage());
        }
    }

    /**
     * 删除帖子
     * @param $postId
     * @return array
     */
    public function delPost($postId)
    {
        try {
            self::destroy(['id' => $postId]);
            return msg(1, '', '删除文章成功');
        } catch (\PDOException $e) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' .$e->getMessage());
        }
    }

    /**
     * @param $where
     * @return mixed
     */
    public function postTotal($where)
    {
        return self::withTrashed()->where($where)->count();
    }

    public function getPostWhere($where, $offset, $limit)
    {
        try {
            $result = self::withTrashed()->where($where)->limit($offset, $limit)->order('id desc')->select();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public function getMyPostWhere($where, $offset, $limit)
    {
        try {
            $result = self::withTrashed()->where('user_id', session('id'))->where($where)->limit($offset, $limit)->order('id desc')->select();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public function myPostTotal($where)
    {
        return self::withTrashed()->where('user_id', session('id'))->where($where)->count();
    }
}