<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/28
 * Time: 0:09
 */

namespace app\admin\controller;

use app\admin\model\User as UserModel;
use app\admin\model\Role as RoleModel;

class User extends Base
{
    public function index()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (!empty($param['searchText'])) {
                $where = ['like', '%' . $param['searchText'] . '%'];
            }
            try {
                $userModel = new UserModel();
                $userList = objToArray($userModel->getUserList($where, $offset, $limit));
                foreach ($userList as $key => $item) {
                    if (1 == $item['id']) {
                        $userList[$key]['operate'] = '';
                        continue;
                    }
                    $userList[$key]['operate'] = showOperate($this->makeButton($item['id']));
                }
                $userInfo = [];
                $userInfo['total'] = $userModel->getAllUser('');
                $userInfo['rows'] = $userList;
            } catch (\Exception $e) {
                $userInfo =  [500, $e->getMessage()];
            }
            return json($userInfo);
        }
        return $this->fetch();
    }

    public function userEdit()
    {
        $userModel = new UserModel();
        if ($this->request->isAjax()) {
            $request = $this->request->post();
            if (empty($request['password'])) {
                unset($request['password']);
            }
            $flag = $userModel->editUser($request);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
        $id = $this->request->param('id');
        $RoleModel = new RoleModel();
        $this->assign([
            'user' => $userModel->getOneUser($id),
            'role' => $RoleModel->getRole(),
        ]);
        return $this->fetch();
    }

    public function userAdd()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->post();
            $userModel = new UserModel();
            $flag = $userModel->insertUser($param);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
        $RoleModel = new RoleModel();
        $roleName = $RoleModel->getRole();
        $this->assign([
            'role' => $roleName,
        ]);
        return $this->fetch();
    }

    public function userDel()
    {
        $userId = $this->request->param('id');
        $userModel = new UserModel();
        $flag = $userModel->delUser($userId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    public function userBlocked()
    {
        $userId = $this->request->param('id');
        $userModel = new UserModel();
        $flag = $userModel->blockedUser($userId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    private function makeButton($id)
    {
        return [
            '编辑' => [
                'auth' => 'user/useredit',
                'href' => url('user/userEdit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '封禁' => [
                'auth' => 'user/userblocked',
                'href' => "javascript:userBlocked(". $id .")",
                'btnStyle' => 'warning',
                'icon' => 'fa fa-lock'
            ],
            '删除' => [
                'auth' => 'user/userdel',
                'href' => "javascript:userDel(" .$id .")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }

}