<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/4/2
 * Time: 23:04
 */

namespace app\admin\controller;

use app\admin\model\Role as RoleModel;
use app\admin\model\Node as NodeModel;

class Role extends Base
{
    public function index()
    {
        if ($this -> request -> isAjax()) {
            $param = $this->request->param();
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (!empty($param['searchText'])) {
                $where['role_name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            try {
                $RoleModel = new RoleModel;
                $selectResult = objToArray($RoleModel->getRoleByWhere($where, $limit, $offset));
                foreach ($selectResult as $key => $item) {
                    if (1 == $item['id']) {
                        $selectResult[$key]['operate'] = '';
                        continue;
                    }
                    $selectResult[$key]['operate'] = showOperate($this->makeButton($item['id']));
                }
                $roleInfo = [];
                $roleInfo['total'] = $RoleModel->getRoleCount($where);  // 总数据
                $roleInfo['rows'] = $selectResult;
            } catch (\Exception $e) {
                $roleInfo =  [500, $e->getMessage()];
            }
            return json($roleInfo);
        }
        return $this -> fetch();
    }

    public function roleAdd()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $RoleModel = new RoleModel();
            $flag = $RoleModel->insertRole($param);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
        return $this->fetch();
    }

    public function roleEdit()
    {
        $RoleModel = new RoleModel();
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            $flag = $RoleModel->editRole($param);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
        $RoleId =$this->request->param('id');
        $roleInfo = RoleModel::get(['id' => $RoleId]);
        $this->assign('role', $roleInfo);
        return $this->fetch();
    }

    public function roleDel()
    {
        $roleId = $this->request->param('id');
        $RoleModel = new RoleModel();
        $flag = $RoleModel->delRole($roleId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    public function giveAccess()
    {
        $param = $this->request->param();
        $NodeModel = new NodeModel();
        if ('get' == $param['type']) {
            $nodeStr = $NodeModel->getNodeInfo($param['id']);
            return json(msg(1, $nodeStr, 'success'));
        }
        if ('give' == $param['type']) {
            $RoleModel = new RoleModel();
            $giveAccess = [
                'id' => $param['id'],
                'rule' => $param['rule']
            ];
            $flag = $RoleModel->editAccess($giveAccess);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
    }

    private function makeButton($id)
    {
        return [
            '编辑' => [
                'auth' => 'role/roleedit',
                'href' => url('role/roleedit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '删除' => [
                'auth' => 'role/roledel',
                'href' => "javascript:roleDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],
            '分配权限' => [
                'auth' => 'role/giveaccess',
                'href' => "javascript:giveQx(" . $id . ")",
                'btnStyle' => 'info',
                'icon' => 'fa fa-institution'
            ],
        ];
    }
}