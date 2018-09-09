<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/4/2
 * Time: 23:05
 */

namespace app\admin\controller;

use app\admin\model\Node as NodeModel;


class Node extends Base
{
    /**
     * 加载节点列表
     * @return \think\response\Json 加载成功json数据
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $NodeModel = new NodeModel();
            $nodeStr = $NodeModel -> getNodeList();
//          nodestr一开始是数据集的数组, 转换数组形式, 整理出tree数据的数组
            $nodeStr = getTree(objToArray($nodeStr), false);
            return json(msg(1, $nodeStr, '数据加载完成'));
        }
        return $this->fetch();
    }

    /**
     * 增加节点
     * @return \think\response\Json 增加成功或者失败返回的JSON数据
     */
    public function nodeAdd()
    {
        $param = $this->request -> post();
        $NodeModel = new NodeModel();
        $flag = $NodeModel -> insertNode($param);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    /**
     * 编辑节点
     * @return \think\response\Json 编辑成功或者失败返回的JSON数据
     */
    public function nodeEdit()
    {
        $param = $this->request -> post();
        $NodeModel = new NodeModel();
        $flag = $NodeModel->editNode($param);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    /**
     * 删除节点
     * @return \think\response\Json 删除成功或者失败返回的JSON数据
     */
    public function nodeDel()
    {
        $nodeId = $this->request->param('id');
        $nodeModel = new NodeModel();
        $flag = $nodeModel -> delNode($nodeId);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }
}