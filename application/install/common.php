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
        $keys = array_keys($userConfig);
        $values = array_values($userConfig);
        $replace = [];
        $PatternKey = [];
        for ($i = 0; $i < count($keys); $i++) {
            $PatternKey[$i] = '/\'' . $keys[$i] . '\'(.*?),/';
            $replace[$i] =  "'". $keys[$i]. "'". " => " . "'".$values[$i] ."',";
        }
        $configFile = file_get_contents($file);
        $configFile = preg_replace($PatternKey, $replace, $configFile);
        file_put_contents($file, $configFile);
        return true;
    } else {
        return false;
    }
}

/**
 *  检测文件夹或文件权限
 *
 * @return array
 */
function checkDirOrFile()
{
    $item = [
        ['dir', '可写', 'check', APP_PATH],
        ['dir', '可写', 'check', EXTEND_PATH],
        ['dir', '可写', 'check', RUNTIME_PATH],
        ['dir', '可写', 'check', ROOT_PATH . 'public/static'],
        ['dir', '可写', 'check', ROOT_PATH . 'public/uploads'],
    ];
    foreach ($item as &$value) {
        if ('dir' == $value[0]) {
            if (!is_writable($value[3])) {
                if (is_dir($value[3])) {
                    $value[1] = '可读';
                    $value[2] = 'times text-warning';
                    session('error', true);
                } else {
                    $value[1] = '非目录';
                    $value[2] = 'times text-warning';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($value[3]) && is_file($value[3])) {
                if (!is_writable($value[3])) {
                    $value[1] = '不可写';
                    $value[2] = 'times text-warning';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($value[3]))) {
                    $value[1] = '非目录且不可写';
                    $value[2] = 'times text-warning';
                    session('error', true);
                }
            }
        }
    }
    unset($value);
    return $item;
}

/**
 * 检测PHP模块,类和函数支持
 *
 * @return array
 */
function checkFunc()
{
    $item = [
        ['pdo', '支持', 'check', 'class'],
        ['pdo_mysql', '支持', 'check', 'module'],
        ['curl', '支持', 'check', 'module'],
        ['fileinfo', '支持', 'check', 'module'],
        ['file_get_contents', '支持', 'check', 'function'],
        ['mb_strlen', '支持', 'check', 'function']
    ];
    foreach ($item as &$value) {
        if (('class' == $value[3] && !class_exists($value[0]))
            || ('module' == $value[3] && !extension_loaded($value[0]))
            || ('function' == $value[3] && !function_exists($value[0]))) {
            $value[1] = '不支持';
            $value[2] = 'times text-warning';
        }
    }
    return $item;
}

/**
 *  检测安装环境
 *
 * @return array
 */
function checkEnv()
{
    $needEnv = [
        'OS' => ['系统', '不限制', 'Linux', PHP_OS, 'check'],
        'PHP' => ['PHP版本', '5.6', '7.0+', PHP_VERSION, 'check'],
        'MySQL' => ['MySQL版本', '5.5', '5.7', '未知', 'check'],
        'GD' => ['GD库', '2.0', '2.0以上', '未知', 'check'],
        'upload' => ['上传大小', '2M以上', '8M', '未知', 'check'],
        'disk' => ['磁盘空间', '100M', '200M以上', '未知', 'check']
    ];
    // 检测PHP版本
    if ($needEnv['PHP'][3] < $needEnv['PHP'][2]) {
        $needEnv['PHP'][4] = 'times text-waring';
    }
    // 检测MySQL版本
    $mysqlVersion = checkMySQLVersion();
    if (preg_match('/^\d\.\d+$/', $mysqlVersion)) {
        $needEnv['MySQL'][3] = $mysqlVersion;
        if ($mysqlVersion < $needEnv['MySQL'][1]) {
            $needEnv['MySQL'][4] = 'times text-warning';
            session('error', true);
        }
    } else {
        $items['MySQL'][3] = $mysqlVersion;
        $needEnv['MySQL'][4] = 'times text-warning';
        session('error', true);
    }
    unset($mysqlVersion);
    // 检测GD库
    $gdVersion = gdVersion();
    if ($gdVersion) {
         $needEnv['GD'][3] = $gdVersion;
         if ($gdVersion < $needEnv['GD'][1]) {
             $needEnv['MySQL'][4] = 'times text-warning';
             session('error', true);
         }
    } else {
        $needEnv['GD'][3] = '未安装';
        $needEnv['GD'][4] = 'times text-warning';
        session('error', true);
    }
    unset($gdVersion);
    // 检测上传大小
    $uploadSize = checkUploadSize();
    if ($uploadSize) {
        $needEnv['upload'][3] = $uploadSize;
        if ((int)$uploadSize < (int)$needEnv['upload'][1]) {
            $needEnv['upload'][4] = 'times text-warning';
            session('error', true);
        }
    } else {
        $needEnv['upload'][3] = '无法获取上传大小';
        $needEnv['upload'][4] = 'times text-warning';
        session('error', true);
    }

    // 检测磁盘大小
    $diskSize = checkDiskSize();
    if ($diskSize) {
        $needEnv['disk'][3] = $diskSize . 'M';
        if ($diskSize < 100) {
            $needEnv['disk'][4] = 'times text-warning';
            session('error', true);
        }
    } else {
        $needEnv['disk'][3] = '无法检测磁盘大小';
        $needEnv['disk'][4] = 'times text-warning';
        session('error', true);
    }
    return $needEnv;
}

/**
 * 获取mysql版本
 *
 * @return bool|string
 */
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

/**
 * 检测GD库
 *
 * @return bool
 */
function gdVersion()
{
    if (function_exists('gd_info')) {
        $gdInfo = gd_info();
        return $gdInfo['GD Version'];
    }
    return false;
}

/**
 * 获取上传大小
 *
 * @return bool|string
 */
function checkUploadSize()
{
    if (ini_get('file_uploads')) {
        return ini_get('upload_max_filesize');
    } else {
        return false;
    }
}

/**
 * 检测磁盘大小
 *
 * @return bool|float
 */
function checkDiskSize()
{
    if (function_exists('disk_free_space')) {
        $diskSize = floor(disk_free_space(APP_PATH) / (1024 * 1024));
        return $diskSize;
    } else {
        return false;
    }
}
