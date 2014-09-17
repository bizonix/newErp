/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-06-06 11:06:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_aoyoubao_gh`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_aoyoubao_gh`;
CREATE TABLE `trans_freight_aoyoubao_gh` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT NULL COMMENT '组名',
  `countrys` text COMMENT '国家',
  `price` decimal(10,4) DEFAULT '0.0000' COMMENT '每公斤单价',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `handlefee` decimal(10,2) DEFAULT NULL,
  `addTime` int(10) DEFAULT NULL,
  `editTime` int(10) DEFAULT NULL,
  `add_user_id` int(10) DEFAULT NULL,
  `edit_user_id` int(10) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_aoyoubao_gh
-- ----------------------------
INSERT INTO `trans_freight_aoyoubao_gh` VALUES ('4', 'AU', 'Australia', '63.0000', '0.00', '5.00', '1402023020', null, '71', null, '0');
