<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/7/22
 * Time: 18:07
 */

namespace app\index\controller;

use think\Request;
use think\Controller;
use app\index\model\Message as MessageModel;


class Message extends Controller
{
    private $msgModel;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->msgModel = new MessageModel();
    }

    /**
     * 统计消息数目
     *
     * @return \think\response\Json
     */
    public function count()
    {
        $userId = session('user.id');
        return $this->msgModel->getNotifyCount($userId);
    }


    /**
     * 已读消息
     *
     * @return \think\response\Json
     */
    public function read()
    {
        $userId = session('user.id');
        return $this->msgModel->readMessage($userId);
    }


    /**
     * 删除消息
     *
     * @return \think\response\Json
     */
    public function remove()
    {
        $userId = session('user.id');
        $toId = $this->request->post();
        return $this->msgModel->removeMessage($userId, $toId);
    }
}