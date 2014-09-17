/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-06-06 16:43:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_track_numbers`
-- ----------------------------
DROP TABLE IF EXISTS `trans_track_numbers`;
CREATE TABLE `trans_track_numbers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrierId` int(10) DEFAULT '0' COMMENT '运输方式ID',
  `trackNumber` varchar(50) DEFAULT NULL COMMENT '跟踪号',
  `orderId` int(12) DEFAULT '0' COMMENT '订单号',
  `countrys` varchar(30) DEFAULT NULL COMMENT '跟踪号所属国家',
  `assignTime` int(10) DEFAULT '0' COMMENT '跟踪号分配时间',
  `addTime` int(10) DEFAULT '0' COMMENT '跟踪号添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '跟踪号修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人UID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `carrierId` (`carrierId`) USING BTREE,
  KEY `trackNumber` (`trackNumber`) USING BTREE,
  KEY `orderId` (`orderId`) USING BTREE,
  KEY `addTime` (`addTime`) USING BTREE,
  KEY `assignTime` (`assignTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_track_numbers
-- ----------------------------
