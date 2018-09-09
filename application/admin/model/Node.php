<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/23
 * Time: 23:10
 */

namespace app\admin\model;

use think\Model;

class Node extends Model
{
    /**
     * 获取节点数据
     * @param $id int 节点id
     * @return string
     */
    public function getNodeInfo($id)
    {
        try {
            $result = $this->field('id, node_name, type_id')->select();
            $str = '';
            $roleModel = new Role();
            $rules = $roleModel->getRuleById($id);
            if (!empty($rules)) {
                $rules = explode(',', $rules);
            }
            foreach ($result as $item) {
                $str .= '{ "id": "' . $item['id'] . '", "pId":"' . $item['type_id'] . '", "name":"' . $item['node_name'].'"';
                if (!empty($rules) && in_array($item['id'], $rules)) {
                    $str .= ' ,"checked":1';
                }
                $str .= '},';
            }
            return '[' . rtrim($str, ',') . ']';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 根据节点数据获取对应的菜单
     * @param string $nodeStr 节点数据
     * @return array|string
     */
    public function getMenus($nodeStr = '')
    {
        if (empty($nodeStr)) {
            return [];
        }
        // 超级管理员没有节点数组 * 号表示
        $where = '*' == $nodeStr ? 'is_menu = 2' : 'is_menu = 2 and id in(' . $nodeStr . ')';
        try {
            $result = $this->where($where)->select();
            $menus = prepareMenu($result);
            return $menus;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 根据条件获取访问权限节点数据
     * @param $where string 查询条件
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getActions($where)
    {
        try {
            $result = $this->field('rule')->where($where)->select();
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取节点数据
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNodeList()
    {
        try {
            $result = $this->field('id, node_name name, rule, is_menu, type_id pid, style')->select();
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 插入节点
     * @param $param
     * @return array
     */
    public function insertNode($param)
    {
        try {
            $this->save($param);
            return msg(1, '', '添加节点成功');
        } catch (\Exception $e) {
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑节点
     * @param $param
     * @return array
     */
    public function editNode($param)
    {
        try {
            $result = $this->isUpdate(true)->save($param, ['id' => $param['id']]);
            if (false !== $result) {
                return msg(1, '', '编辑节点成功');
            } else {
                return msg(-1, '', '编辑节点失败');
            }
        } catch (\Exception $e) {
            return msg(-2, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }

    /**
     * 删除节点
     * @param $id
     * @return array
     */
    public function delNode($id)
    {
        try {
            $this->where('id', $id)->delete();
            return msg(1, '', '删除节点成功');
        } catch (\Exception $e) {
            return msg(-1, '', '数据库出错, 请联系管理员. 报错信息:' . $e->getMessage());
        }
    }
}