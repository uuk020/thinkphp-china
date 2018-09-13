<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/2/18
 * Time: 20:47
 */

namespace app\index\controller;


use think\Controller;
use app\index\model\Experience;
use app\index\model\Post  as PostModel;
use app\index\model\Like  as LikeModel;
use app\index\model\Reply as ReplyModel;


/**
 * 帖子控制器
 * Class Topic
 * @package app\index\controller
 */
class Topic extends Controller
{
    /**
     * 管理员ID
     */
    const ADMIN_ID = 1;

    /**
     * 发帖积分
     */
    const POST_SCORE = 20;

    /**
     * 帖子列表
     *
     * @param int    $page   页数
     * @param string $filter 筛选字段
     *
     * @return mixed|string
     */
    public function index($page = 1, $filter = 'all')
    {
        try {
            $page       = (int)$page;
            $nav        = PostModel::getNav($page);
            $hotPosts   = PostModel::getHotPosts();
            $posts      = $this->hasFilterPosts($filter, $page);
            $this->assign([
                'category'  => config('category'),
                'hotPosts'  => $hotPosts,
                'posts'     => $posts,
                'page'      => $nav['page'],
                'pageNum'   => $nav['pageNum'],
                'showMenus' => $nav['showMenus'],
                'filter'    => $filter,
            ]);
        } catch (\Exception $exception) {
            return '数据库发生异常.';
        }
        return $this->fetch();
    }

    public function category($id = '', $page = 1, $filter = 'all')
    {
        try {
            $page          = (int)$page;
            $nav           = PostModel::getCategoryNav($page, $id);
            $categoryPosts = $this->hasFilterCategoryPosts($filter, $page, $id);
            $hotPosts      = PostModel::getHotPosts();
            $this->assign([
                'category'      => config('category'),
                'categoryID'    => $id,
                'categoryPosts' => $categoryPosts,
                'page'          => $nav['page'],
                'pageNum'       => $nav['pageNum'],
                'showMenus'     => $nav['showMenus'],
                'hotPosts'      => $hotPosts,
                'filter'        => $filter,
            ]);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        return $this->fetch();
    }

    /**
     * 是否有筛选字段的帖子
     *
     * @param $filter
     * @param $page
     *
     * @return array|int|mixed
     */
    private function hasFilterPosts($filter, $page)
    {
        if (empty($filter)) {
            $posts = PostModel::getPostInfo($page);
        } else {
            $sort  = $this->filterPost($filter);
            $posts = PostModel::getPostInfo($page, 8, $sort);
        }
        return $posts;
    }

    /**
     * 是否有筛选字段的分类帖子
     *
     * @param string $filter      筛选字段
     * @param int    $page        当前页数
     * @param int    $categoryID  分类ID
     *
     * @return array|string
     */
    private function hasFilterCategoryPosts($filter, $page, $categoryID)
    {
        if (empty($filter)) {
            $posts = PostModel::getCategoryPosts($page, $categoryID, 8);
        } else {
            $sort  = $this->filterPost($filter);
            $posts = PostModel::getCategoryPosts($page, $categoryID, 8, $sort);
        }
        return $posts;
    }

    /**
     * 匹配筛选字段
     *
     * @param string $filter 筛选字段
     *
     * @return array
     */
    private function filterPost($filter)
    {
        $sort = [];
        switch ($filter) {
            case 'all':
                $sort['field'] = 'create_time';
                $sort['order'] = 'DESC';
                break;
            case 'likes':
                $sort['field'] = 'like_count';
                $sort['order'] = 'DESC';
                break;
            case 'reply':
                $sort['field'] = 'replies_count';
                $sort['order'] = 'DESC';
                break;
            default:
                $sort['field'] = 'replies_count';
                $sort['order'] = 'ASC';
                break;
        }
        return $sort;
    }

    /**
     * 新增帖子
     *
     * @return mixed|\think\response\Json
     *
     * @throws \think\exception\PDOException
     */
    public function add()
    {
        if (!session('?user')) {
            $this->error('请登录后才能发帖','User/login');
        }
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            $user = session('user');
            if (!captcha_check($postData['vercode'])) {
                return json(['status' => 0, 'msg' => '验证码错误']);
            }
            $post = new PostModel();
            $post->startTrans();
            try {
                if (!Experience::minusExperience($user['id'], self::POST_SCORE)) {
                    return json(['status' => 0, 'msg' => '积分不足以发帖']);
                }
                $post->title = $postData['title'];
                $post->content = $postData['content'];
                $post->user_id = $user['id'];
                $post->category_id = $postData['class'];
                $post->save();
                $post->commit();
                return json(['status' => 0, 'msg' => '发帖成功', 'action' => url('Topic/index')]);
            } catch (\Exception $exception) {
                $post->rollback();
                return json(['status' => 0, 'msg' => '发帖失败']);
            }
        }
        $this->assign([
            'category' => config('category'),
        ]);
        return $this->fetch();
    }

    /**
     * 帖子详情
     *
     * @param  integer $id 帖子ID
     *
     * @return mixed
     */
    public function detail($id)
    {
        $postId = intval($id);
        try {
            $getPost        = PostModel::get($postId, 'user');
            if (empty($getPost)) {
                $this->error('此帖子不存在', 'index/topic/index');
            }
            $post           = $getPost->toArray();
            $postEdit       = url('index/topic/edit', "id={$postId}");
            $authorHomepage = url('index/user/home', "uid={$post['user']['id']}");
            $hotPosts       = PostModel::getHotPosts();
            $replyArr       = ReplyModel::getReplies($postId);
            $postLikeCount  = LikeModel::where('post_id', $postId)->where('flag', 0)->count();
            $postReplyCount = ReplyModel::where('post_id', $postId)->count();
            if (is_array($replyArr) && !empty($replyArr)) {
                $replyInfo = $replyArr;
            } else {
                $replyInfo = [];
            }
            $this->assign([
                'post'           => $post,
                'authorHomepage' => $authorHomepage,
                'postEdit'       => $postEdit,
                'hotPosts'       => $hotPosts,
                'category'       => config('category'),
                'likeCount'      => $postLikeCount,
                'replyCount'     => $postReplyCount,
                'replyInfo'      => $replyInfo,
            ]);

        } catch (\Exception $e) {
            $this->error('操作失败,请重新尝试', url('index/topic/index'));
        }
        return $this->fetch();
    }

    /**
     * 编辑帖子
     *
     * @param  int $id 帖子ID
     *
     * @return mixed|\think\response\Json
     */
    public function edit($id)
    {
        $postId  = intval($id);
        $userId  = session('user.id');
        try {
            $getPost = PostModel::get($postId, 'user');
            if (empty($getPost)) {
                $this->error('此帖子不存在', 'index/index/index');
            }
            $post = $getPost->toArray();
            if ($post['user_id'] !== $userId && $userId !== self::ADMIN_ID) {
                $this->error('你没有权限修改帖子', 'index/topic/index');
            }
            if ($this->request->post()) {
                $userInput = $this->request->post();
                if (!captcha_check($userInput['vercode'])) {
                    return json(['status' => 1, 'msg' => '验证码错误']);
                }
                PostModel::where('id', $postId)
                    ->update(['title' => $userInput['title'], 'content' => $userInput['content'], 'category_id' => $userInput['class']]);
                return json(['status' => 0, 'msg' => '编辑帖子成功', 'action' => url('Topic/detail', "id={$postId}")]);
            }
            $this->assign([
                'post'     => $post,
                'category' => config('category'),
                'catName'  => getCategoryNames($post['category_id']),
            ]);
        } catch (\Exception $e) {
            $this->error('操作失败,请重新尝试', url('index/topic/index'));
        }
        return $this->fetch();
    }
}