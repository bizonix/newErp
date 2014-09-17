/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-03-22 13:42:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_track_wedo_number`
-- ----------------------------
DROP TABLE IF EXISTS `trans_track_wedo_number`;
CREATE TABLE `trans_track_wedo_number` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `orderSn` varchar(50) DEFAULT NULL COMMENT '关联订单号',
  `trackNumber` varchar(30) DEFAULT NULL COMMENT '运德物流跟踪号',
  `carrierId` int(10) DEFAULT '61' COMMENT '运输方式ID',
  `channelId` int(10) DEFAULT '85' COMMENT '渠道ID',
  `toCountry` varchar(30) DEFAULT NULL COMMENT '目的地国家',
  `scanTime` int(10) DEFAULT '0' COMMENT '跟踪号首次扫描时间',
  `platAccount` varchar(30) DEFAULT '' COMMENT '平台帐号',
  `platForm` varchar(20) DEFAULT '' COMMENT '平台名称',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `carrierId` (`carrierId`) USING BTREE,
  KEY `orderSn` (`orderSn`) USING BTREE,
  KEY `scanTime` (`scanTime`) USING BTREE,
  KEY `channelId` (`channelId`) USING BTREE,
  KEY `is_delete` (`is_delete`),
  KEY `trackNumber` (`trackNumber`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=800022 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_track_wedo_number
-- ----------------------------
INSERT INTO `trans_track_wedo_number` VALUES ('800019', 'HO86777\\&acute;HO86777HO86777', 'WDG20800019CN', '61', '85', 'Australia', '1394640000', 'candholltom', '', '1395454341', '0', '71', '0', '0');
INSERT INTO `trans_track_wedo_number` VALUES ('800020', 'FA10770', 'WDG20800020CN', '61', '85', 'United Kingdom', '1394640000', '0795epjelaine', '', '1395454341', '0', '71', '0', '0');
INSERT INTO `trans_track_wedo_number` VALUES ('800021', 'YO8559', 'WDG20800021CN', '61', '85', 'Belarus', '1394640000', 'olesya_by', '', '1395454342', '0', '71', '0', '0');

-- ----------------------------
-- Table structure for `trans_track_wedo_sn`
-- ----------------------------
DROP TABLE IF EXISTS `trans_track_wedo_sn`;
CREATE TABLE `trans_track_wedo_sn` (
  `gid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `wedo_sn` char(4) DEFAULT NULL COMMENT '运德跟踪号唯一识别码',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) DEFAULT NULL,
  `editTime` int(10) DEFAULT '0' COMMENT '编辑时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`gid`),
  UNIQUE KEY `gid` (`gid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_track_wedo_sn
-- ----------------------------
INSERT INTO `trans_track_wedo_sn` VALUES ('1', 'G3', '1395389132', '71', '0', '0', '0');
INSERT INTO `trans_track_wedo_sn` VALUES ('71', 'G2', '1395389107', '71', '0', '0', '0');
INSERT INTO `trans_track_wedo_sn` VALUES ('664', 'G1', '1395372415', '71', '1395389120', '71', '0');

-- ----------------------------
-- Table structure for `trans_user_competence`
-- ----------------------------
DROP TABLE IF EXISTS `trans_user_competence`;
CREATE TABLE `trans_user_competence` (
  `gid` int(10) NOT NULL DEFAULT '0' COMMENT '统一用户ID',
  `competence` text COMMENT '权限',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`gid`),
  UNIQUE KEY `gid` (`gid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_user_competence
-- ----------------------------

-- ----------------------------
-- Table structure for `trans_user_competences`
-- ----------------------------
DROP TABLE IF EXISTS `trans_user_competences`;
CREATE TABLE `trans_user_competences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL COMMENT '权限描述',
  `item` varchar(30) DEFAULT NULL COMMENT '键名',
  `content` varchar(30) DEFAULT NULL COMMENT '权限内容',
  `pid` int(10) DEFAULT '0' COMMENT '上级ID，0顶级',
  `level` tinyint(4) DEFAULT '1' COMMENT '权限级别',
  `path` varchar(50) DEFAULT NULL COMMENT '权限路径',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_user_competences
-- ----------------------------
INSERT INTO `trans_user_competences` VALUES ('1', '内容查看权限', 'view', 'all', '0', '1', '1', '1395217969', '71', '1395305840', '71', '0');
INSERT INTO `trans_user_competences` VALUES ('2', '中国邮政挂号', 'carrierId', '2', '1', '2', '1-2', '1395219478', '71', '1395321020', '71', '0');
INSERT INTO `trans_user_competences` VALUES ('3', '漳州渠道', 'channelId', '19', '2', '3', '1-2-3', '1395286442', '71', '1395321873', '71', '0');
INSERT INTO `trans_user_competences` VALUES ('5', '操作控制权限', 'action', 'all', '0', '1', '5', '1395297181', '71', '0', '0', '1');
INSERT INTO `trans_user_competences` VALUES ('6', '删除', 'delete', 'true', '5', '2', '5-6', '1395297278', '71', '1395297313', '71', '1');
