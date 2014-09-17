/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-11 10:31:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_email_account`
-- ----------------------------
DROP TABLE IF EXISTS `trans_email_account`;
CREATE TABLE `trans_email_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `platForm` varchar(30) DEFAULT 'aliexpress' COMMENT '平台名称',
  `platAccount` varchar(30) DEFAULT '' COMMENT '平台帐号',
  `userName` varchar(20) DEFAULT '' COMMENT '客服姓名',
  `userEmail` varchar(100) DEFAULT '' COMMENT '客服邮箱',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人UID',
  `editTime` int(10) DEFAULT '0' COMMENT '编辑时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `platAccount` (`platAccount`) USING BTREE,
  KEY `platForm` (`platForm`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_email_account
-- ----------------------------
INSERT INTO `trans_email_account` VALUES ('4', 'aliexpress', 'acitylife', 'Alina Chen', 'acitylife88@gmail.com', '71', '1397133825', '0', '0', '0');
