/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-11 10:32:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_smtp_account`
-- ----------------------------
DROP TABLE IF EXISTS `trans_smtp_account`;
CREATE TABLE `trans_smtp_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `platForm` varchar(30) DEFAULT 'aliexpress' COMMENT '平台名称',
  `platAccount` varchar(30) DEFAULT '' COMMENT '平台帐号',
  `smtpHost` varchar(200) DEFAULT '' COMMENT 'SMTP服务器网址',
  `smtpPort` varchar(20) DEFAULT '' COMMENT 'SMTP服务器端口',
  `smtpUser` varchar(100) DEFAULT '' COMMENT 'SMTP用户名',
  `smtpPwd` varchar(100) DEFAULT '' COMMENT 'SMTP密码',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人UID',
  `editTime` int(10) DEFAULT '0' COMMENT '编辑时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `platAccount` (`platAccount`) USING BTREE,
  KEY `platForm` (`platForm`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_smtp_account
-- ----------------------------
INSERT INTO `trans_smtp_account` VALUES ('1', 'aliexpress', 'aaa', 'aaa', 'aa', 'aa', 'aaa', '71', '1397138469', '71', '1397138490', '1');
INSERT INTO `trans_smtp_account` VALUES ('2', 'aliexpress', 'aa', 'aaa', 'aaa', 'aaa', 'aa', '71', '1397138482', '0', '0', '0');
