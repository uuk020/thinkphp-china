-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 2018-08-01 12:57:04
-- 服务器版本： 5.7.21
-- PHP Version: 7.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tp_forum`
--
CREATE DATABASE IF NOT EXISTS `tp_forum` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tp_forum`;

-- --------------------------------------------------------

--
-- 表的结构 `tp_experience`
--

DROP TABLE IF EXISTS `tp_experience`;
CREATE TABLE IF NOT EXISTS `tp_experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `experience` int(11) NOT NULL COMMENT '积分',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `tp_info`
--

DROP TABLE IF EXISTS `tp_info`;
CREATE TABLE IF NOT EXISTS `tp_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '信息ID',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别0-男,1-女',
  `city` char(50) NOT NULL COMMENT '城市',
  `company` char(150) DEFAULT NULL COMMENT '公司',
  `signature` char(100) DEFAULT NULL COMMENT '签名',
  `email_token` varchar(255) DEFAULT NULL COMMENT '邮件验证码',
  `email_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮件验证状态0-未验证,1-验证',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `tp_info`
--

INSERT INTO `tp_info` (`id`, `sex`, `city`, `company`, `signature`, `email_token`, `email_status`, `create_time`, `update_time`, `user_id`) VALUES
(1, 0, '', '', '', '', 1, 1530975687, 1530975687, 1),

-- --------------------------------------------------------

--
-- 表的结构 `tp_like`
--

DROP TABLE IF EXISTS `tp_like`;
CREATE TABLE IF NOT EXISTS `tp_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `reply_uid` int(11) DEFAULT NULL COMMENT '回复者ID',
  `flag` tinyint(1) NOT NULL COMMENT '0-点赞帖子, 1-点赞回复',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `tp_message`
--

DROP TABLE IF EXISTS `tp_message`;
CREATE TABLE IF NOT EXISTS `tp_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `author_id` int(11) NOT NULL COMMENT '被回复者ID',
  `user_id` int(11) NOT NULL COMMENT '回复者ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `object_type` tinyint(1) NOT NULL COMMENT '消息类型0-回复, 1- @某人, 2-点赞',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- 表的结构 `tp_node`
--

DROP TABLE IF EXISTS `tp_node`;
CREATE TABLE IF NOT EXISTS `tp_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_name` varchar(155) NOT NULL DEFAULT '' COMMENT '节点名称',
  `rule` varchar(155) NOT NULL COMMENT '权限规则',
  `is_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是菜单项 1不是 2是',
  `type_id` int(11) NOT NULL COMMENT '父级节点id',
  `style` varchar(155) DEFAULT '' COMMENT '菜单样式',
  `condition` varchar(155) DEFAULT NULL COMMENT '附加条件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tp_node`
--

INSERT INTO `tp_node` (`id`, `node_name`, `rule`, `is_menu`, `type_id`, `style`, `condition`) VALUES
(1, '官网', '#', 2, 0, 'fa fa-bar-chart', NULL),
(2, '官网数据', 'index/count', 2, 1, '', NULL),
(3, '用户管理', '#', 2, 0, 'fa fa-users', NULL),
(4, '用户列表', 'user/index', 2, 3, '', NULL),
(5, '用户新增', 'user/useradd', 1, 3, '', NULL),
(6, '用户删除', 'user/userdel', 1, 3, '', NULL),
(7, '用户编辑', 'user/useredit', 1, 3, '', NULL),
(8, '用户封禁', 'user/userblocked', 1, 3, '', NULL),
(9, '角色管理', '#', 2, 0, 'fa fa-user-secret', NULL),
(10, '角色列表', 'role/index', 2, 9, '', NULL),
(11, '角色增加', 'role/roleadd', 1, 9, '', NULL),
(12, '角色编辑', 'role/roleedit', 1, 9, '', NULL),
(13, '角色删除', 'role/roledel', 1, 9, '', NULL),
(14, '分配权限', 'role/giveaccess', 1, 9, '', NULL),
(15, '节点管理', '#', 2, 0, 'fa fa-sitemap', NULL),
(16, '节点列表', 'node/index', 2, 15, '', NULL),
(17, '节点编辑', 'node/nodeedit', 1, 15, '', NULL),
(18, '节点增加', 'node/nodeadd', 1, 15, '', NULL),
(19, '删除节点', 'node/nodedel', 1, 15, '', NULL),
(20, '帖子管理', '#', 2, 0, 'fa fa-file-word-o', NULL),
(21, '所有帖子', 'post/index', 2, 20, '', NULL),
(22, '自己帖子', 'post/mypost', 2, 20, '', NULL),
(23, '帖子封禁', 'post/postblocked', 1, 20, '', NULL),
(24, '帖子删除', 'post/postdel', 1, 20, '', NULL);
-- (25, '回复管理', '#', 2, 0, 'fa fa-comments', NULL),
-- (26, '回复列表', 'reply/index', 2, 25, '', NUll),
-- (27, '回复删除', 'reply/delete', 1, 25, '', NUll)

-- --------------------------------------------------------

--
-- 表的结构 `tp_post`
--

DROP TABLE IF EXISTS `tp_post`;
CREATE TABLE IF NOT EXISTS `tp_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '帖子ID',
  `title` char(50) NOT NULL COMMENT '帖子标题',
  `content` mediumtext NOT NULL COMMENT '帖子内容',
  `user_id` int(11) NOT NULL COMMENT '作者ID',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT '封禁标识',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `tp_reply`
--

DROP TABLE IF EXISTS `tp_reply`;
CREATE TABLE IF NOT EXISTS `tp_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `user_id` int(11) NOT NULL COMMENT '回复者ID',
  `reply_id` int(11) NOT NULL COMMENT '被回复者ID',
  `content` varchar(255) NOT NULL COMMENT '回复内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- 表的结构 `tp_role`
--

DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE IF NOT EXISTS `tp_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `role_name` varchar(155) NOT NULL COMMENT '角色名称',
  `rule` varchar(255) DEFAULT '' COMMENT '权限节点数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tp_role`
--

INSERT INTO `tp_role` (`id`, `role_name`, `rule`) VALUES
(1, '超级管理员', '*'),
(2, '系统维护员', '1,2,3,4,5,6,7,18,19,20,21'),
(3, '普通用户', '18,20,20');

-- --------------------------------------------------------

--
-- 表的结构 `tp_user`
--

DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE IF NOT EXISTS `tp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `nickname` varchar(30) NOT NULL COMMENT '昵称',
  `email` varchar(60) NOT NULL COMMENT '邮箱',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `role_id` int(11) NOT NULL DEFAULT '3' COMMENT '角色ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT '封禁账号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tp_user`
--

INSERT INTO `tp_user` (`id`, `username`, `nickname`, `email`, `password`, `avatar`, `role_id`, `create_time`, `update_time`, `delete_time`, `blocked`) VALUES
(1, 'admin', '管理员', 'wythe.huangw@gmail.com', '$2y$12$Qj.hxhLspqpO98xGF5UDOO65y1acgTt9fY8/E3GDRwMvAQxi8KSzK', 'http://www.tpproject.com/static/images/avatar.jpg', 1, 1530975687, 1530975687, NULL, 0),

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
