/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-11 10:31:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_email_stat`
-- ----------------------------
DROP TABLE IF EXISTS `trans_email_stat`;
CREATE TABLE `trans_email_stat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trackNumber` varchar(30) DEFAULT '' COMMENT '跟踪号',
  `content` text COMMENT '邮件内容',
  `platAccount` varchar(30) DEFAULT '' COMMENT '平台帐号',
  `is_success` tinyint(1) DEFAULT '1' COMMENT '是否成功，0失败',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `lastTime` int(10) DEFAULT '0' COMMENT '最后更新时间',
  `retryCount` smallint(4) DEFAULT '0' COMMENT '重试次数',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `trackNumber` (`trackNumber`) USING BTREE,
  KEY `platAccount` (`platAccount`) USING BTREE,
  KEY `is_success` (`is_success`) USING BTREE,
  KEY `is_delete` (`is_delete`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_email_stat
-- ----------------------------
