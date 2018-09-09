<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Post  as PostModel;
use app\index\model\Sign  as SignModel;
use app\index\model\Reply as ReplyModel;

class Index extends Controller
{
    /**
     * 论坛首页页面
     *
     * @return mixed|string
     */
    public function index()
    {
        try {
            $postModel  = new PostModel();
            $posts      = $postModel->getPosts();
            $hotPosts   = PostModel::getHotPosts(10);
            $adminPost  = $postModel->getAdminPosts();
            $signModel  = SignModel::getDays(session('user.id'));
            $days       = $signModel['days'];
            $replyModel = new ReplyModel();
            $replyTop   = $replyModel->getMostCommentedUser();
        } catch (\Exception $exception) {
            return '数据库内部发生错误';
        }
        $this->assign([
            'category'  => config('category'),
            'adminPost' => $adminPost,
            'posts'     => $posts,
            'days'      => $days,
            'hotPosts'  => $hotPosts,
            'replyTop'  => $replyTop,
        ]);
        return $this->fetch();
    }

}
