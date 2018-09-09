<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/4/2
 * Time: 23:05
 */

namespace app\admin\controller;

use app\admin\model\Post as PostModel;


class Post extends Base
{
    public function index()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1 ) * $limit;
            $where = [];
            if (!empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $PostModel = new PostModel();
            $postList = $PostModel->getPostWhere($where, $offset, $limit);
            foreach($postList as $key=>$vo){
                $postList[$key]['operate'] = showOperate($this->makeButton($vo['id']));
            }
            $return['total'] = $PostModel->postTotal($where);  // 总数据
            $return['rows'] = $postList;
            return json($return);
        }
        return $this->fetch();
    }

    public function myPost()
    {
        if ($this->request->isAjax()) {
            $param = $this->request->param();
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1 ) * $limit;
            $where = [];
            if (!empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $PostModel = new PostModel();
            $postList = $PostModel->getMyPostWhere($where, $offset, $limit);
            foreach($postList as $key=>$vo){
                $postList[$key]['operate'] = showOperate($this->makeButton($vo['id']));
            }
            $return['total'] = $PostModel->myPostTotal($where);  // 总数据
            $return['rows'] = $postList;
            return json($return);
        }
        return $this->fetch();
    }

    public function postDel()
    {
        $postId = $this->request->param('id');
        $PostModel = new PostModel();
        $flag = $PostModel->delPost($postId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    public function postBlock()
    {
        $postId = $this->request->param('id');
        $PostModel = new PostModel();
        $flag = $PostModel->blockPost($postId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    private function makeButton($id)
    {
        return [
            '封禁' => [
                'auth' => 'post/postblocked',
                'href' => "javascript:postBlock(" . $id . ")",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-eye-slash'
            ],
            '删除' => [
                'auth' => 'post/postdel',
                'href' => "javascript:postDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],
        ];
    }
}