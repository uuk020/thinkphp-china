/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.199.101_3306
 Source Server Type    : MySQL
 Source Server Version : 50561
 Source Host           : 192.168.199.101:3306
 Source Schema         : tp_forum

 Target Server Type    : MySQL
 Target Server Version : 50561
 File Encoding         : 65001

 Date: 25/10/2018 22:07:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tp_collection
-- ----------------------------
DROP TABLE IF EXISTS `tp_collection`;
CREATE TABLE `tp_collection`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for tp_experience
-- ----------------------------
DROP TABLE IF EXISTS `tp_experience`;
CREATE TABLE `tp_experience`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `experience` int(11) NOT NULL COMMENT '积分',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_experience
-- ----------------------------
INSERT INTO `tp_experience` VALUES (1, 1, 1000000, 1533909362, 1535037565);

-- ----------------------------
-- Table structure for tp_info
-- ----------------------------
DROP TABLE IF EXISTS `tp_info`;
CREATE TABLE `tp_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '信息ID',
  `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别0-男,1-女',
  `city` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '城市',
  `company` char(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '公司',
  `signature` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '签名',
  `email_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮件验证码',
  `email_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '邮件验证状态0-未验证,1-验证',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tp_info
-- ----------------------------
INSERT INTO `tp_info` VALUES (1, 0, '深圳', '', '', '', 1, 1530975687, 1536593697, 1);

-- ----------------------------
-- Table structure for tp_like
-- ----------------------------
DROP TABLE IF EXISTS `tp_like`;
CREATE TABLE `tp_like`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `reply_uid` int(11) NULL DEFAULT NULL COMMENT '回复者ID',
  `flag` tinyint(1) NOT NULL COMMENT '0-点赞帖子, 1-点赞回复',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for tp_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_message`;
CREATE TABLE `tp_message`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `author_id` int(11) NOT NULL COMMENT '被回复者ID',
  `user_id` int(11) NOT NULL COMMENT '回复者ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `object_type` tinyint(1) NOT NULL COMMENT '消息类型0-回复, 1- @某人, 2-点赞',
  `post_title` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `reply_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tp_node
-- ----------------------------
DROP TABLE IF EXISTS `tp_node`;
CREATE TABLE `tp_node`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '节点名称',
  `rule` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限规则',
  `is_menu` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是菜单项 1不是 2是',
  `type_id` int(11) NOT NULL COMMENT '父级节点id',
  `style` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '菜单样式',
  `condition` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '附加条件',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 25 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tp_node
-- ----------------------------
INSERT INTO `tp_node` VALUES (1, '官网', '#', 2, 0, 'fa fa-bar-chart', NULL);
INSERT INTO `tp_node` VALUES (2, '官网数据', 'index/count', 2, 1, '', NULL);
INSERT INTO `tp_node` VALUES (3, '用户管理', '#', 2, 0, 'fa fa-users', NULL);
INSERT INTO `tp_node` VALUES (4, '用户列表', 'user/index', 2, 3, '', NULL);
INSERT INTO `tp_node` VALUES (5, '用户新增', 'user/useradd', 1, 3, '', NULL);
INSERT INTO `tp_node` VALUES (6, '用户删除', 'user/userdel', 1, 3, '', NULL);
INSERT INTO `tp_node` VALUES (7, '用户编辑', 'user/useredit', 1, 3, '', NULL);
INSERT INTO `tp_node` VALUES (8, '用户封禁', 'user/userblocked', 1, 3, '', NULL);
INSERT INTO `tp_node` VALUES (9, '角色管理', '#', 2, 0, 'fa fa-user-secret', NULL);
INSERT INTO `tp_node` VALUES (10, '角色列表', 'role/index', 2, 9, '', NULL);
INSERT INTO `tp_node` VALUES (11, '角色增加', 'role/roleadd', 1, 9, '', NULL);
INSERT INTO `tp_node` VALUES (12, '角色编辑', 'role/roleedit', 1, 9, '', NULL);
INSERT INTO `tp_node` VALUES (13, '角色删除', 'role/roledel', 1, 9, '', NULL);
INSERT INTO `tp_node` VALUES (14, '分配权限', 'role/giveaccess', 1, 9, '', NULL);
INSERT INTO `tp_node` VALUES (15, '节点管理', '#', 2, 0, 'fa fa-sitemap', NULL);
INSERT INTO `tp_node` VALUES (16, '节点列表', 'node/index', 2, 15, '', NULL);
INSERT INTO `tp_node` VALUES (17, '节点编辑', 'node/nodeedit', 1, 15, '', NULL);
INSERT INTO `tp_node` VALUES (18, '节点增加', 'node/nodeadd', 1, 15, '', NULL);
INSERT INTO `tp_node` VALUES (19, '删除节点', 'node/nodedel', 1, 15, '', NULL);
INSERT INTO `tp_node` VALUES (20, '帖子管理', '#', 2, 0, 'fa fa-file-word-o', NULL);
INSERT INTO `tp_node` VALUES (21, '所有帖子', 'post/index', 2, 20, '', NULL);
INSERT INTO `tp_node` VALUES (22, '自己帖子', 'post/mypost', 2, 20, '', NULL);
INSERT INTO `tp_node` VALUES (23, '帖子封禁', 'post/postblocked', 1, 20, '', NULL);
INSERT INTO `tp_node` VALUES (24, '帖子删除', 'post/postdel', 1, 20, '', NULL);

-- ----------------------------
-- Table structure for tp_post
-- ----------------------------
DROP TABLE IF EXISTS `tp_post`;
CREATE TABLE `tp_post`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '帖子ID',
  `title` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '帖子标题',
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '帖子内容',
  `user_id` int(11) NOT NULL COMMENT '作者ID',
  `category_id` int(11) NOT NULL COMMENT '分类ID',
  `blocked` tinyint(1) NOT NULL DEFAULT 0 COMMENT '封禁标识',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_post
-- ----------------------------
INSERT INTO `tp_post` VALUES (1, '欢迎安装并且使用ThinkPhpForum论坛', '闲暇之时使用thinkphp和fly社区前端模板开发了一个论坛用来整理技能知识, 基本上满足一个论坛所需功能.欢迎使用和提出issue! ', 1, 1, 0, 1529938969, 1529939045, NULL);
-- ----------------------------
-- Table structure for tp_reply
-- ----------------------------
DROP TABLE IF EXISTS `tp_reply`;
CREATE TABLE `tp_reply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `post_id` int(11) NOT NULL COMMENT '帖子ID',
  `user_id` int(11) NOT NULL COMMENT '回复者ID',
  `reply_id` int(11) NOT NULL COMMENT '被回复者ID',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '回复内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tp_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `role_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色名称',
  `rule` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '权限节点数据',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES (1, '超级管理员', '*');
INSERT INTO `tp_role` VALUES (2, '系统维护员', '1,2,3,4,5,6,7,18,19,20,21');
INSERT INTO `tp_role` VALUES (3, '普通用户', '18,20,20');

-- ----------------------------
-- Table structure for tp_sign
-- ----------------------------
DROP TABLE IF EXISTS `tp_sign`;
CREATE TABLE `tp_sign`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `days` tinyint(2) NOT NULL DEFAULT 0 COMMENT '用户连续签到天数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '签到表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tp_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '昵称',
  `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '头像',
  `role_id` int(11) NOT NULL DEFAULT 3 COMMENT '角色ID',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `blocked` tinyint(1) NOT NULL DEFAULT 0 COMMENT '封禁账号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (1, 'admin', '管理员', 'wythe.huangw@gmail.com', '$2y$12$5Fd1mBasBuYA4c8WCp6UXeE/hd/0VQyzL/C4YOPYDZtlCOFtuNDE6', 'http://tp5.forum.test/uploads/20180912/2c9fd27688329b0943232c77c71cd2d5.jpg', 1, 1530975687, 1536767546, NULL, 0);

SET FOREIGN_KEY_CHECKS = 1;