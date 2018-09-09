<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/7/29
 * Time: 14:25
 */

namespace app\index\model;

use think\Model;

class Experience extends Model
{
    /**
     * 自动写入时间
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;
    /**
     * 时间格式
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 管理员ID
     */
    const ADMIN_ID = 1;
    /**
     * 减去积分
     *
     * @param int $uid   用户ID
     * @param int $score 减去积分数
     *
     * @return bool
     */
    public static function minusExperience($uid, $score)
    {
        try {
            $userExperienceModel = self::get(['uid' => $uid]);
            $userExperienceInfo  = isset($userExperienceModel) ? $userExperienceModel->toArray() : 0;
            if ($uid !== self::ADMIN_ID && $score > $userExperienceInfo['experience']) {
                return false;
            }
            $remain = $userExperienceInfo['experience'] - $score;
            $userExperienceModel->experience = $remain;
            $userExperienceModel->save();
            session('user.experience', $remain);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 加积分
     *
     * @param int $uid    用户ID
     * @param int $score  积分
     *
     * @return bool
     */
    public static function addExperience($uid, $score)
    {
        try {
            $userExperienceModel = self::get(['uid' => $uid]);
            $userExperienceInfo  = isset($userExperienceModel) ? $userExperienceModel->toArray() : '';
            $total               = $userExperienceInfo['experience'] + $score;
            $userExperienceModel->experience = $total;
            $userExperienceModel->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}