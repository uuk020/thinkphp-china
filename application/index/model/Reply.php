<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/18
 * Time: 22:20
 */

namespace app\index\model;

use think\Exception;
use think\Model;

class Reply extends Model
{
    /**
     * 设置时间格式
     *
     * @var string
     */
    protected $dateFormat = 'Y/m/d H:i:s';

    /**
     * 开启自动写入时间
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 回帖积分
     */
    const REPLY_SCORE = 5;

    public function addReply($param = [])
    {
        // 开启事务
        $this->startTrans();
        try {
            $user = session('user');
            $newType = 0;
            $userId = $user['id'];
            // 防止未登录的人回复帖子
            if (!isset($userId) && empty($userId)) {
                return json(['status' => 0, 'msg'=>'回复失败, 请登录账号', 'action' => url("index/user/login")]);
            }
            if (session('user.email_status') !== 1) {
                return json(['status' => 0, 'msg' => '邮箱未验证', 'action' => url('index/topic/detail', "id={$param['post_id']}")]);
            }
            $postId       = intval($param['post_id']);
            $replyId      = intval($param['reply_id']);
            $replyContent = $param['content'];
            $this->data([
                'content'  => $replyContent,
                'reply_id' => $replyId,
                'post_id'  => $postId,
                'user_id'  => $userId,
            ]);
            // 扣除积分
            if (!Experience::minusExperience($userId, self::REPLY_SCORE)) {
                return json(['status' => 1, 'msg' => '积分不够']);
            }
            // 新增回复
            if ($this->save()) {
                // 获取帖子作者
                $authorId = intval(Post::where('id', $postId)->value('user_id'));
                $postTile = Post::where('id', $postId)->value('title');
                // 如果帖子作者不等于被回复的人, 则为@某人
                if ($authorId !== $replyId) {
                    $newType = 1;
                }
                // 新增消息
                Message::create([
                    'author_id'     => $replyId,
                    'user_id'       => $userId,
                    'post_id'       => $postId,
                    'object_type'   => $newType,
                    'post_title'    => $postTile,
                    'reply_content' => $replyContent,
                ]);
                $this->commit();
                return json(['status' => 0, 'msg' => '回复成功', 'action' => url('index/topic/detail', "id={$postId}")]);
            }
        } catch (\Exception $e) {
            $this->rollback();
            return json(['status' => 1, 'msg' => '回复失败']);
        }
    }

    /**
     * 获取帖子评论
     *
     * @param int $postId 帖子ID
     *
     * @return array|string
     */
    public static function getReplies($postId)
    {
        $replyInfo = [];
        try {
            $postId = (int)$postId;
            $replyContent = self::with('user')->withCount('like')
                ->where('post_id', $postId)
                ->select();
            if (!empty($replyContent) && isset($replyContent)) {
                foreach ($replyContent as $value) {
                    $replyInfo[] = $value->toArray();
                }
                foreach($replyInfo as &$item) {
                    $item['homepage'] = url('index/user/home', "uid={$item['user']['id']}");
                }
                unset($item);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $replyInfo;
    }

    /**
     * 获取用户评论
     *
     * @param int    $userId  用户ID
     * @param string $order   评论排序
     * @param int    $length  评论数量
     *
     * @return array|bool
     */
    public static function userRelies($userId, $order = 'desc', $length = 5)
    {
        $userRelies = [];
        try {
            $userReliesInfo = self::field('p.id, p.title ,c.content, c.create_time')->alias('c')
                ->join('tp_post p', 'p.id = c.post_id')->where('c.user_id', $userId)
                ->limit($length)->order('c.create_time')->select();
            foreach ($userReliesInfo as $useRelay) {
                $userRelies[] = $useRelay->toArray();
            }
            foreach ($userRelies as &$rely) {
                $rely['post_url'] = url('index/topic/detail', "id={$rely['id']}", true, true);
            }
            unset($rely);
        } catch (Exception $exception) {
            return false;
        }
        return $userRelies;
    }

    /**
     * 获取回帖最多的用户
     *
     * @return array|string
     */
    public function getMostCommentedUser()
    {
        try {
            $sql = "SELECT u.id, u.nickname, u.avatar, COUNT(*) AS reply_count FROM tp_user u
                    INNER JOIN tp_reply r ON u.id = r.user_id 
                    GROUP BY u.id HAVING COUNT(*) >= 20 ORDER BY reply_count DESC LIMIT 12";
            $userComments = $this->query($sql);
            foreach ($userComments as &$userComment) {
                $userComment['homepage'] = url("index/user/Home", "uid={$userComment['id']}");
            }
            unset($userComment);
        } catch (\Exception $e) {
            return '数据库发生异常';
        }
        return $userComments;
    }

    /**
     * 一对多关联用户表(相对)
     *
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * 一对多关联点赞表
     *
     * @return \think\model\relation\HasMany
     */
    public function like()
    {
        return $this->hasMany('Like', 'reply_uid');
    }
}