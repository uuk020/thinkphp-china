<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/3/23
 * Time: 22:46
 */

/**
 * 生成操作按钮
 * @param array $operate
 * @return string
 */
function showOperate($operate = [])
{
    if (empty($operate)) {
        return '';
    }

    $option = '';
    foreach ($operate as $key => $vo) {
        if (authCheck($vo['auth'])) {
            $option .= ' <a href="' . $vo['href'] . '"><button type="button" class="btn btn-' . $vo['btnStyle'] . ' btn-sm">' .
                '<i class="' . $vo['icon'] . '"></i> ' . $key . '</button></a>';
        }
    }

    return $option;
}

/**
 * @param $code
 * @param $data
 * @param $msg
 * @return array
 */
function msg($code, $data, $msg)
{
    return compact('code', 'data', 'msg');
}

/**
 * 权限检测
 * @param string $role 角色
 * @return bool  通过验证返回true;失败返回false
 */
function authCheck($role)
{
    $control = explode('/', $role)[0];
    // 排除登录页面和主页
    if (in_array($control, ['login', 'index'])) {
        return true;
    }
    // 链接是否符合session('action')规则
    if (in_array($role, session('action'))) {
        return true;
    }
    return false;
}

function prepareMenu($param)
{
    $param = objToArray($param);
    $parent = []; //父类
    $child = [];  //子类
    foreach ($param as $key => $vo) {

        if ($vo['type_id'] == 0) {
            $vo['href'] = '#';
            $parent[] = $vo;
        } else {
            $vo['href'] = url($vo['rule']); //跳转地址
            $child[] = $vo;
        }
    }

    foreach ($parent as $key => $vo) {
        foreach ($child as $k => $v) {

            if ($v['type_id'] == $vo['id']) {
                $parent[$key]['child'][] = $v;
            }
        }
    }
    unset($child);

    return $parent;
}

/**
 * @param $obj
 * @return mixed
 */
function objToArray($obj)
{
    return json_decode(json_encode($obj), true);
}

/**
 * 整理出tree数据 ---  layui tree
 * @param array $pInfo
 * @param bool  $spread
 * @return array
 */
function getTree($pInfo, $spread = true)
{

    $res = [];
    $tree = [];
    //整理数组
    foreach ($pInfo as $key => $vo) {

        if ($spread) {
            $vo['spread'] = true;  //默认展开
        }
        $res[$vo['id']] = $vo;
        $res[$vo['id']]['children'] = [];
    }
    unset($pInfo);

    //查找子孙
    foreach ($res as $key => $vo) {
        if (0 != $vo['pid']) {
            $res[$vo['pid']]['children'][] = &$res[$key];
        }
    }

    //过滤杂质
    foreach ($res as $key => $vo) {
        if (0 == $vo['pid']) {
            $tree[] = $vo;
        }
    }
    unset($res);

    return $tree;
}