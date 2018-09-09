<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/8/11
 * Time: 18:41
 */

namespace app\index\model;


use think\Model;

class Sign extends Model
{
    protected $autoWriteTimestamp = true;
    protected $dateFormat = 'Y-m-d H:i:s';

    public function setSign($uid)
    {
        try {
            $this->startTrans();
            $signed    = $this->hasSign($uid);
            $data      = $this->getInsertData($uid);
            $now       = strtotime(date('Y-m-d 23:59:59', time()));
            $yesterday = strtotime(date('Y-m-d 0:0:0',time()))-1;
            if ($signed == null) {
                $data['uid'] = $uid;
                unset($data['update_time']);
                $this->save($data);
            } else {
                $signed   = $signed->toArray();
                $signTime = strtotime($signed['update_time']);
                if ($signTime > $yesterday && $signTime <= $now) {
                     return [false, '今天已经签到'];
                } else {
                    $this->where('uid', $uid)->update($data);
                }
            }
            $score = $this->getTodayScores($data['days']);
            if (Experience::addExperience($uid, $score)) {
                $this->commit();
                return [true, '签到成功', $data['days']];
            }
        } catch (\Exception $e) {
            $this->rollback();
            dump($e->getMessage());
            return [false, '数据库发生错误, 请稍后再尝试'];
        }
    }

    protected function getInsertData($uid)
    {
        try {
            $startTime = strtotime(date('Y-m-d 0:0:0',time()-86400))-1;
            $endTime   = strtotime(date('Y-m-d 23:59:59',time()-86400))+1;
            $signInfo = $this->where('uid' , $uid)
                ->where('update_time', '>', $startTime)
                ->where('update_time', '<', $endTime)->find();
            if ($signInfo) {
                $data = $signInfo->toArray();
                $days = $data['days'];
            } else {
                $days = 0;
            }
            if ($days) {
                $days++;
                if ($days >= 120) {
                    $days = 1;
                }
            } else {
                $days = 1;
            }
            return ['days' => $days, 'update_time' => time()];
        } catch (\Exception $e) {
            return '数据库异常';
        }
    }

    protected function hasSign($uid)
    {
        try {
            $result = $this->where('uid', $uid)->find();
            return $result;
        } catch (\Exception $e) {
            return '数据库异常';
        }
    }

    /**
     * 获取用户签到数据
     *
     * @param int $uid  用户ID
     *
     * @return array
     */
    public static function getDays($uid)
    {
        try {
            $signModel = self::get(['uid' => $uid]);
            if ($signModel) {
                $result = $signModel->toArray();
                return $result;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 积分奖励机制
     *
     * @param int $days 签到天数
     *
     * @return int
     */
    protected function getTodayScores($days){
        if ($days >= 30) {
            return 20;
        } elseif ($days >= 15) {
            return 15;
        }else if($days >= 5) {
            return 10;
        } else {
            return 5;
        }
    }

    public function experience()
    {
        return $this->hasOne('Experience');
    }

    public function user()
    {
        return $this->hasOne('User');
    }
}