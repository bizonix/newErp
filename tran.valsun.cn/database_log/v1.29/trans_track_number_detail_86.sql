/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-29 20:11:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_track_number_detail_86`
-- ----------------------------
DROP TABLE IF EXISTS `trans_track_number_detail_86`;
CREATE TABLE `trans_track_number_detail_86` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trackNumber` varchar(30) DEFAULT NULL COMMENT '跟踪号',
  `postion` varchar(100) DEFAULT NULL COMMENT '处理地点',
  `event` varchar(200) DEFAULT NULL COMMENT '事件',
  `trackTime` int(10) DEFAULT '0' COMMENT '跟踪时间',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `trackNumber` (`trackNumber`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_track_number_detail_86
-- ----------------------------
