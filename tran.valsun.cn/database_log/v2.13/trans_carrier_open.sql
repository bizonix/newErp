/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-07-15 11:30:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_carrier_open`
-- ----------------------------
DROP TABLE IF EXISTS `trans_carrier_open`;
CREATE TABLE `trans_carrier_open` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `carrierAdd` tinyint(2) DEFAULT '0' COMMENT '发货地址',
  `carrierId` int(10) DEFAULT '0' COMMENT '运输方式ID',
  `carrierAbb` char(20) DEFAULT '' COMMENT '运输方式简称',
  `carrierIndex` char(1) DEFAULT '' COMMENT '运输方式字母索引',
  `carrierDiscount` decimal(10,4) DEFAULT '0.0000' COMMENT '运输方式原价上的折扣',
  `carrierAging` char(50) DEFAULT '' COMMENT '时效描述',
  `carrierNote` varchar(200) DEFAULT '' COMMENT '运输方式备注',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `carrierId` (`carrierId`) USING BTREE,
  KEY `carrierAbb` (`carrierAbb`) USING BTREE,
  KEY `carrierAdd` (`carrierAdd`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
