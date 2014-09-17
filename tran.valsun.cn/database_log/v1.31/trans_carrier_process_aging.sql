/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-17 11:16:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_carrier_process_aging`
-- ----------------------------
DROP TABLE IF EXISTS `trans_carrier_process_aging`;
CREATE TABLE `trans_carrier_process_aging` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `nodeId` int(10) DEFAULT '0' COMMENT '节点ID',
  `aging` int(10) DEFAULT '0' COMMENT '时效',
  `country` varchar(100) DEFAULT NULL COMMENT '国家名',
  `cid` int(10) DEFAULT '0' COMMENT '国家ID',
  `is_auto` tinyint(1) DEFAULT '0' COMMENT '自动更新，0自动',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `nodeId` (`nodeId`) USING BTREE,
  KEY `country` (`country`) USING BTREE,
  KEY `cid` (`cid`) USING BTREE,
  KEY `aging` (`aging`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_carrier_process_aging
-- ----------------------------
