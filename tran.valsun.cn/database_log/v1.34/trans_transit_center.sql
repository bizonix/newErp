/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-30 11:26:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_transit_center`
-- ----------------------------
DROP TABLE IF EXISTS `trans_transit_center`;
CREATE TABLE `trans_transit_center` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cn_title` varchar(30) NOT NULL DEFAULT '' COMMENT '转运中心（中文名）',
  `en_title` varchar(30) NOT NULL DEFAULT '' COMMENT '转运中心（英文名）',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '添加人UID',
  `editTime` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_transit_center
-- ----------------------------
INSERT INTO `trans_transit_center` VALUES ('1', '洛杉矶', 'Los Angeles', '1401267430', '71', '1401332765', '71', '0');
INSERT INTO `trans_transit_center` VALUES ('2', '辛辛那提', 'Cincinnati', '1401334093', '71', '1401336095', '71', '0');
