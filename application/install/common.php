<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/9/24
 * Time: 17:10
 */
/**
 *
 * @return bool
 */
function hasInstallFile()
{
    if (file_exists(ROOT_PATH . 'forum.lock')) {
        return true;
    }
    return false;
}