<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/22
 * Time: 21:49
 */

namespace app\admin\controller;
use app\admin\model\Node as NodeModel;
use app\admin\model\User as UserModel;
use app\admin\model\Post as PostModel;
use think\Db;

class Index extends Base
{
    /**
     * 渲染侧边栏的菜单
     * @return mixed
     */
    public function index()
    {
        $nodeModel = new NodeModel();
        $this->assign([
            'menu'   => $nodeModel->getMenus(session('rule')),
        ]);
        return $this->fetch('/index');
    }

    /**
     * 渲染出主页的内容
     * @return mixed
     */
    public function indexPage()
    {
        return $this->fetch('index');
    }

    /**
     * 渲染出统计(数据包含用户,点赞, 帖子, 回复)页面
     * @return mixed
     */
    public function count()
    {
        $user = new UserModel();
        $userTotal = $user->getAllUser('');
        $likeTotal = Db::name('like')->count();
        $post = new PostModel();
        $postTotal = $post->postTotal('');
        $replyTotal = Db::name('reply')->count();
        $this->assign([
            'userTotal'  =>  $userTotal,
            'postTotal'  =>  $postTotal,
            'likeTotal'  =>  $likeTotal,
            'replyTotal' =>  $replyTotal,
        ]);
        return $this->fetch();
    }
}