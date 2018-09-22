<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/8/13
 * Time: 22:25
 */

namespace app\index\model;

use think\Model;

class Collection extends Model
{

    protected $autoWriteTimestamp = true;

    protected $dateFormat         = 'Y-m-d H:i:s';

    public function addCollection($uid, $postId)
    {
        try {
            $hasCollection = $this->where('uid', $uid)->where('post_id', $postId)->select();
            if (!$hasCollection) {
                $this->data([
                    'uid'     => $uid,
                    'post_id' => $postId
                ]);
                $this->save();
                return [true, '收藏成功'];
            }
            return [true, '已经收藏过了'];
        } catch (\Exception $e) {
            return [false, '数据库发生异常'];
        }
    }

    public function deleteCollection($id)
    {
        try {
            $this->where('id', $id)->delete();
            return [true, '取消收藏成功'];
        } catch (\Exception $e) {
            return [false, '数据库出错'];
        }
    }

    public function getCollection($uid)
    {
        try {
            $data      = $this->field('id ,post_id, create_time')->where('uid', $uid)->select();
            $postModel = new Post();
            if ($data) {
                $collections = [];
                foreach ($data as $datum) {
                    $collections[] = $datum->toArray();
                }
                foreach ($collections as &$collection) {
                    $collection['post_title'] = $postModel->getPostTitle($collection['post_id']);
                    $collection['url']   = url('index/topic/detail', "id={$collection['post_id']}");
                    unset($collection);
                }
                return $collections;
            } else {
                return '该用户还没收藏帖子';
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
            return '数据库异常';
        }
    }

}