<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/18
 * Time: 22:19
 */

namespace app\index\controller;

use think\Request;
use think\Controller;
use app\index\model\Reply as ReplyModel;

class Reply extends Controller
{
    /**
     * 回复帖子
     *
     * @param \think\Request $request
     *
     * @return \think\response\Json
     */
    public function newReply(Request $request)
    {
        $param = $request->param();
        $replyModel = new ReplyModel();
        $result = $replyModel->addReply($param);
        return $result;
    }
}