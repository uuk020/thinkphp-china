<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/7/29
 * Time: 23:47
 */

namespace app\api\controller;


use think\Controller;
use app\index\model\Like       as LikeModel;
use app\index\model\Post       as PostModel;
use app\index\model\Reply      as ReplyModel;
use app\index\model\Message    as MessageModel;
use app\index\model\Sign       as SignModel;
use app\index\model\Collection as CollectionModel;

class Index extends Controller
{
    public function _initialize()
    {
        if (!session('?user')) {
            return json(['status' => 1, 'msg' => '用户没登录']);
        }
    }

    public function upload()
    {

    }

    public function like()
    {
        $user = session('user');
        $userId = $user['id'];
        $param = $this->request->post();
        try {
            $hasLike    = LikeModel::get(['user_id' => $userId, 'post_id' => $param['pid'], 'reply_uid' => $param['rid']]);
            $postTitle  = PostModel::field('title')->where('id', $param['pid'])->find()->toArray();
            $author     = ReplyModel::get(['user_id' => $param['rid']])->toArray();
            $hasMessage = MessageModel::get(['user_id' => $userId]);
            $authorId   = $author['user_id'];
            if ($hasLike && !empty($hasLike)) {
                $hasLike->delete();
                $hasMessage->delete(true);
                return json(['status' => 0, 'msg' => '你已经点赞过了', 'flag' => 1]);
            } else {
                // 排除自己给自己点赞
                if ($authorId !== $userId) {
                    LikeModel::create([
                        'user_id'   => $userId,
                        'post_id'   => $param['pid'],
                        'reply_uid' => $param['rid'],
                        'flag'      => 1,
                    ]);
                    // 新增消息
                    MessageModel::create([
                        'author_id'     => $authorId,
                        'user_id'       => $userId,
                        'post_id'       => $param['pid'],
                        'object_type'   => 2,
                        'post_title'    => $postTitle,
                        'reply_content' => $author['content'],
                    ]);
                    return json(['status' => 0, 'msg' => '点赞成功', 'flag' => 0]);
                }
            }
        } catch (\Exception $e) {
            return json(['status' => 2, 'msg' => '点赞失败']);
        }
    }

    public function likePost()
    {
        $user = session('user');
        $userId = $user['id'];
        $postId = $this->request->param('pid');
        try {
            $hasLike    = LikeModel::get(['user_id' => $userId, 'post_id' => $postId]);
            $author     = PostModel::get($postId)->toArray();
            $hasMessage = MessageModel::get(['user_id' => $userId]);
            $authorId   = $author['user_id'];
            if ($hasLike && !empty($hasLike)) {
                $hasLike->delete();
                $hasMessage->delete(true);
                return json(['status' => 0, 'msg' => '你已经点赞过了', 'flag' => 1]);
            } else {
                if ($authorId !== $userId) {
                    LikeModel::create([
                        'user_id'   => $userId,
                        'post_id'   => $postId,
                        'flag'      => 0
                    ]);
                    // 新增消息
                    MessageModel::create([
                        'author_id'    => $authorId,
                        'user_id'      => $userId,
                        'post_id'      => $postId,
                        'object_type'  => 2,
                        'post_title'   => $author['title'],
                    ]);
                    return json(['status' => 0, 'msg' => '点赞成功', 'flag' => 0]);
                }
            }
        } catch (\Exception $e) {
            return json(['status' => 2, 'msg' => '点赞失败']);
        }
    }

    /**
     * 签到接口
     *
     * @return \think\response\Json
     *
     * @throws \think\exception\PDOException
     */
    public function sign()
    {
        $uid = session('user.id');
        $signModel = new SignModel();
        $signInfo = $signModel->setSign($uid);
        if ($signInfo[0]) {
            return json(['status' => 0, 'msg' => $signInfo[1]]);
        } else {
            return json(['status' => 1, 'msg' => $signInfo[1]]);
        }
    }

    public function collection()
    {
         $uid    = $this->request->param('uid');
         $postId = $this->request->param('pid');
         $collectionModel = new CollectionModel();
         $result          = $collectionModel->addCollection($uid, $postId);
         if ($result[0]) {
             return json(['status' => 0, 'msg' => $result[1]]);
         } else {
             return json(['status' => 1, 'msg' => $result[1]], 500);
         }
    }

    public function cancelCollection()
    {
        $id              = $this->request->param('cid');
        $collectionModel = new CollectionModel();
        $result          = $collectionModel->deleteCollection($id);
        if ($result[0]) {
            return json(['status' => 0, 'msg' => $result[1]]);
        } else {
            return json(['status' => 1, 'msg' => $result[1]]);
        }
    }
}
