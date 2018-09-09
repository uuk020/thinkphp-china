<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/23
 * Time: 23:11
 */

namespace app\admin\model;

use think\Model;

class Role extends Model
{

    /**
     * @param $where
     * @param $limit
     * @param $offset
     * @return false|\PDOStatement|string|\think\Collection
     */
    function getRoleByWhere($where, $limit, $offset)
    {
        try {
            $result = $this->where($where)->limit($offset, $limit)->order('id desc')->select();
            if (false === $result || is_string($result)) {
                return '数据查询有误';
            }
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * 根据查询条件查询总数
     * @param $where
     * @return int|string
     */
    public function getRoleCount($where)
    {
        try {
            $result = $this->where($where)->count();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * 编辑用户
     * @param $param
     * @return array
     */
    public function editRole($param)
    {
        try {
            $result = $this->isUpdate(true)->save($param, ['id' => $param['id']]);
            if (false === $result) {
                return msg(-1, '', $this->getError());
            } else {
                return msg(1, 'role/index', '编辑角色成功');
            }
        } catch (\Exception $e) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' .$e->getMessage());
        }
    }

    /**
     * 新增角色
     * @param $param
     * @return array
     */
    public function insertRole($param)
    {
        try {
            $result = $this->save($param);
            if (false === $result) {
                return msg(-1, '', $this->getError());
            } else {
                return msg(1, url('role/index'), '新增角色成功');
            }
        } catch (\Exception $e) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }

    /**
     * 删除角色
     * @param $id
     * @return array
     */
    public function delRole($id)
    {
        try {
            $this->where('id', $id)->delete();
            return msg(1, url('role/index'), '删除角色成功');
        } catch (\Exception $e) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }

    /**
     * 获取所有的角色信息
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRole()
    {
        try {
            $result = $this->select();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    /**
     * 获取角色的权限节点
     * @param $id
     * @return mixed|string
     */
    public function getRuleById($id)
    {
        try {
            $res = $this->field('rule')->where('id', $id)->find();
            return $res['rule'];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 分配权限
     * @param $param
     * @return array
     */
    public function editAccess($param)
    {
        try {
            $this->save($param, ['id' => $param['id']]);
            return msg(1, url('role/index'), '分配权限成功');
        } catch (\Exception $e) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }

    /**
     * 获取角色信息
     * @param $id
     * @return array|string
     */
    public function getRoleInfo($id)
    {
        try {
            $result = $this->where('id', $id)->find()->toArray();
            // 判断角色所属规则, 超级管理员是*
            if (empty($result['rule'])) {
                $result['action'] = '';
                return $result;
            } else if ('*' === $result['rule']) {
                $where = '';
            } else {
                $where = 'id in( ' . $result['rule']. ')';
            }

            // 获取权限
            $nodeModel = new Node();
            $res = $nodeModel->getActions($where);
            if (is_array($res) && !empty($res)) {
                foreach ($res as $value) {
                    // 判断是不是菜单选项
                    if ('#' !== $value['rule']) {
                        $result['action'][] = $value['rule'];
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

}