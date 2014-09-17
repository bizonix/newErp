/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-07-15 11:31:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_open_api_stat`
-- ----------------------------
DROP TABLE IF EXISTS `trans_open_api_stat`;
CREATE TABLE `trans_open_api_stat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `apiId` int(10) DEFAULT '0' COMMENT 'api ID',
  `apiUid` int(10) DEFAULT '0' COMMENT 'API使用人UID',
  `apiCount` int(10) DEFAULT '0' COMMENT 'API当天调用次数',
  `firstTime` int(10) DEFAULT '0' COMMENT 'api当日首次调用时间',
  `lastTime` int(10) DEFAULT '0' COMMENT '最近一次调用时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `apiUid` (`apiUid`) USING BTREE,
  KEY `firstTime` (`firstTime`) USING BTREE,
  KEY `apiId` (`apiId`) USING BTREE,
  KEY `lastTime` (`lastTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;