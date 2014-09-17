/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-23 16:01:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_track_carrier_country`
-- ----------------------------
DROP TABLE IF EXISTS `trans_track_carrier_country`;
CREATE TABLE `trans_track_carrier_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `carrierId` int(10) NOT NULL DEFAULT '0' COMMENT '运输方式ID',
  `countryName` varchar(20) NOT NULL DEFAULT '' COMMENT '国家名称',
  `countryId` int(10) NOT NULL DEFAULT '0' COMMENT '国家ID',
  `trackName` varchar(20) NOT NULL COMMENT '跟踪系统运输方式名称',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '添加人UID',
  `editTime` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `edit_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '编辑人UID',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `carrierId` (`carrierId`) USING BTREE,
  KEY `countryName` (`countryName`) USING BTREE,
  KEY `countryId` (`countryId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_track_carrier_country
-- ----------------------------
