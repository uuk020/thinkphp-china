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

/**
 * 设置配置文件
 *
 * @param string $file       配置文件
 * @param array  $userConfig 用户设置
 *
 * @return bool
 */
function setConfig($file, $userConfig)
{
    if (is_array($userConfig)) {
        $key = array_keys($userConfig);
        $value = array_values($userConfig);
        for ($i = 0; $i < count($key); $i++) {
            $keys[$i] = '/\'' . $key[$i] . '\'(.*?),/';
            $replace =  "'". $key[$i]. "'". " => " . "'".$value[$i] ."',";
        }
        $configFile = file_get_contents($file);
        $configFile = preg_replace($keys, $replace, $configFile);
        file_put_contents($file, $configFile);
        return true;
    } else {
        return false;
    }
}

function checkEnv()
{
    $needEnv = [
        'OS' => ['系统', '不限制', 'Linux', PHP_OS, 'check'],
        'PHP' => ['PHP版本', '5.6以上', '7.0以上', PHP_VERSION, 'check'],
        'MySQL' => ['MySQL版本', '5.5', '5.7', '未知', 'check'],
        'GD' => ['GD库', '2.0', '2.0以上', '未知', 'check'],
        'upload' => ['上传大小', '5M以上', '8M', '未知', 'check'],
        'disk' => ['磁盘空间', '100M', '200M以上', '未知', 'check']
    ];
}

function checkMySQLVersion()
{
    $dsn = 'mysql:dbname=mysql;host=127.0.0.1';
    $username = config('database.username');
    $password = config('database.password');
    try {
        $dbh = new PDO($dsn, $username, $password);
        $version = $dbh->getAttribute(PDO::ATTR_SERVER_VERSION);
        $mySqlVersion = substr($version, 0, 3);
        return $mySqlVersion;
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}