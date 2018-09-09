<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/2/28
 * Time: 22:45
 */

namespace app\index\model;

use think\Model;

class Post extends Model
{
    protected $autoWriteTimestamp = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 获取帖子信息
     *
     * @param int   $page     当前页数
     * @param int   $length   数据条数
     * @param array $sortInfo 筛选字段
     *
     * @return array|int|mixed
     */
    public static function getPostInfo($page, $length = 6, $sortInfo = [])
    {
        try {
            $postData = self::with('User')
                ->withCount('like')
                ->withCount('replies')
                ->where('blocked', 0)
                ->page($page, $length)
                ->order($sortInfo['field'], $sortInfo['order'])
                ->select();
            $postInfo = [];
            foreach ($postData as $postDatum) {
                $postInfo[] = $postDatum->toArray();
            }
            foreach ($postInfo as &$value) {
                $value['catName'] = getCategoryNames($value['category_id']);
                $value['url'] = url('Topic/detail', "id={$value['id']}");
                $value['homepage'] = url('User/home', "uid={$value['user']['id']}");
            }
            unset($value);
            return $postInfo;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取分类的帖子
     *
     * @param int   $page
     * @param int   $categoryID
     * @param int   $length
     * @param array $sortInfo
     *
     * @return array|string
     */
    public static function getCategoryPosts($page, $categoryID, $length = 8, $sortInfo = [])
    {
        try {
            $postModel = self::with('User')
                ->withCount('like')
                ->withCount('replies')
                ->where('blocked', 0)
                ->where('category_id', $categoryID)
                ->page($page, $length)
                ->order($sortInfo['field'], $sortInfo['order'])
                ->select();
            $categoryPosts = [];
            foreach ($postModel as $item) {
                $categoryPosts[] = $item->toArray();
            }
            foreach ($categoryPosts as &$categoryPost) {
                $categoryPost['catName'] = getCategoryNames($categoryPost['category_id']);
                $categoryPost['url'] = url('Topic/detail', "id={$categoryPost['id']}");
                $categoryPost['homepage'] = url('User/home', "uid={$categoryPost['user']['id']}");
            }
            unset($categoryPost);
            return $categoryPosts;
        } catch (\Exception $exception) {
            return '数据库异常';
        }
    }


    /**
     * 分页逻辑处理
     *
     * @param int $page   当前页数
     * @param int $total  数据总数
     * @param int $length 数据条数
     * @param int $show   按钮展示数
     *
     * @return array       分页数据数组
     */
    protected static function _getPaginate($page, $total, $length = 8, $show = 2)
    {

        $page = $page < 1 ? 1 : $page;
        $maxPage = ceil($total / $length);
        $page = $page > $maxPage ? $maxPage : $page;
        $from = max(1, $page - intval($show / 2));
        $to = $from + $show - 1;
        if ($to > $maxPage) {
            $to = $maxPage;
            $from = max(1, $to - $show + 1);
        }
        $showMenus = [];
        for ($i = $from; $i <= $to; $i++) {
            $showMenus[] = $i;
        }
        return ['page' => $page, 'pageNum' => $maxPage, 'showMenus' => $showMenus];
    }

    /**
     * 分页导航
     *
     * @param int $page
     * @param int $length
     * @param int $show
     *
     * @return array
     */
    public static function getNav($page, $length = 8, $show = 2)
    {
        $total = self::with('User')
            ->where('blocked', 0)
            ->count();
        return self::_getPaginate($page, $total, $length, $show);
    }

    public static function getCategoryNav($page, $categoryID, $length = 6, $show = 2)
    {
        $total = self::with('User')
            ->where('blocked', 0)
            ->where('category_id', $categoryID)
            ->count();
        return self::_getPaginate($page, $total, $length, $show);
    }

    /**
     * 获取用户帖子
     *
     * @return array|string
     */
    public function getPosts()
    {
        try {
            $posts = [];
            $postsModel = $this->with('user')->withCount('replies')
                ->field('title, create_time')
                ->where('user_id', '<>', 1)
                ->where('blocked', 0)->limit(12)
                ->order('create_time', 'DESC')
                ->select();
            foreach ($postsModel as $postObject) {
                $posts[] = $postObject->toArray();
            }
            foreach ($posts as &$post) {
                $post['catName'] = $this->getPostCategory($post['category_id']);
                $post['url'] = url('Topic/detail', "id={$post['id']}");
                $post['homepage'] = url('User/home', "uid={$post['user']['id']}");
            }
            unset($post);
            return $posts;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取帖子分类名称
     *
     * @param int $catID 帖子分类ID
     *
     * @return mixed
     */
    private function getPostCategory($catID)
    {
        $category = getCategoryNames($catID);
        return $category[1];
    }

    /**
     * 获取管理员公告
     *
     * @return array|string
     */
    public function getAdminPosts()
    {
        try {
            $adminPosts = [];
            $adminPostsModel = $this->with('user')->withCount('replies')
                ->field('title, create_time')
                ->where('user_id', '=', 1)->limit(4)->select();
            foreach ($adminPostsModel as $adminObject) {
                $adminPosts[] = $adminObject->toArray();
            }
            foreach ($adminPosts as &$adminPost) {
                $adminPost['url'] = url('Topic/detail', "id={$adminPost['id']}");
            }
            unset($adminPost);
            return $adminPosts;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 查询用户帖子
     *
     * @param integer $userId 用户ID
     * @param string  $order  排列顺序
     * @param integer $length 帖子数量
     *
     * @return array|bool
     */
    public static function userPosts($userId, $order = 'desc', $length = 12)
    {
        $userPosts = [];
        try {
            $userPostsInfo = self::field('id, title, create_time')->withCount('like')
                ->withCount('replies')->where('user_id', $userId)
                ->limit($length)->order('create_time', $order)
                ->select();
            foreach ($userPostsInfo as $userPost) {
                $userPosts[] = $userPost->toArray();
            }
            foreach ($userPosts as &$post) {
                $post['url'] = url('Topic/detail', "id={$post['id']}", 'html', true);
                $post['edit'] = url('Topic/edit', "id={$post['id']}", 'html', true);
            }
            unset($post);
        } catch (\Exception $exception) {
            return false;
        }
        return $userPosts;
    }

    /**
     * 热议帖子
     *
     * @param int $len 帖子数量
     *
     * @return array
     */
    public static function getHotPosts($len = 10)
    {
        $hotPosts = [];
        try {
            $hotPostsModel = self::has('replies', '>=', 20)->with('replies')->limit($len)->select();
            if (empty($hotPostsModel)) {
                return $hotPosts;
            }
            foreach ($hotPostsModel as $item) {
                $hotPosts[] = $item->toArray();
            }
            foreach ($hotPosts as &$post) {
                $post['url'] = url('Topic/detail', "id={$post['id']}");
            }
            unset($post);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        return $hotPosts;
    }

    public function getPostTitle($postId)
    {
        try {
           $data = $this->field('title')->where('id', $postId)->find();
           $post = $data->toArray();
           return $post['title'];
        } catch (\Exception $e) {
            return '数据库异常';
        }
    }

    /**
     * 相对一对多用户
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
        return $this->hasMany('Like');
    }

    /**
     * 一对多关联评论表
     *
     * @return \think\model\relation\HasMany
     */
    public function replies()
    {
        return $this->hasMany('Reply');
    }

}