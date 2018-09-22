<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/5
 * Time: 21:41
 */

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Message extends Model
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $deleteTime = 'delete_time';


    /**
     * 获取消息信息
     *
     * @param integer $userId 用户ID
     *
     * @return array|string
     */
    public static function messageInfo($userId)
    {
        try {
            $msgData = self::all(['author_id' => $userId]);
            $msgView = [];
            foreach ($msgData as $datum) {
                $msgView[] = $datum->toArray();
            }
            foreach ($msgView as &$msg) {
                $msg['commenter'] = User::where('id', $msg['user_id'])->value('nickname');
                $msg['post_url']  = url('index/topic/detail', "id={$msg['post_id']}", true, true);
            }
            unset($msg);
        } catch (\Exception $e) {
            return '内部程序发生错误, 请联系管理员';
        }
        return $msgView;
    }

    /**
     * 获取消息总数
     *
     * @param $userId
     *
     * @return \think\response\Json
     */
    public function getNotifyCount($userId)
    {
        try {
            $messageCount = self::where('author_id', $userId)->where('is_read' ,0)->count();
            return json(['count' => $messageCount, 'status' => 0]);
        } catch (\Exception $e) {
            return json(['error' => $e->getMessage(), 'status' => 1, 'msg'=>'请求失败']);
        }
    }

    /**
     * 已读消息
     * @param $userId
     *
     * @return \think\response\Json
     */
    public function readMessage($userId)
    {
        try {
            self::where('author_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);
            return json(['status' => 0, 'msg' => '标记已读']);
        } catch (\Exception $exception) {
            return json(['error' => $exception->getMessage(), 'status' => 1, 'msg' => '请求失败']);
        }
    }

    /**
     * 删除消息
     *
     * @param $userId
     * @param $toId
     *
     * @return \think\response\Json
     */
    public function removeMessage($userId, $toId)
    {
        try {
            if (array_key_exists('all', $toId)) {
                self::destroy([
                    'author_id' => $userId
                ]);
            } else {
                self::destroy([
                    'id'        => $toId['id'],
                    'author_id' => $userId,
                ]);
            }
            return json(['status' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            return json(['error' => $exception->getMessage(), 'status' => 1, 'msg' => '请求失败']);
        }
    }
}