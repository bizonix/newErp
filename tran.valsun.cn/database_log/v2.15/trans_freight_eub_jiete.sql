/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-25 15:15:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_eub_jiete`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_eub_jiete`;
CREATE TABLE `trans_freight_eub_jiete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `unitprice` decimal(10,4) DEFAULT '0.0000' COMMENT '重量单价按每公斤算',
  `nextweight` decimal(10,2) DEFAULT '0.50' COMMENT '多少KG以内打折',
  `countrys` text NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount1` decimal(10,2) DEFAULT '0.00' COMMENT '折扣2',
  `noWeight` int(10) DEFAULT '60' COMMENT '不足多少克按多少克算，默认60',
  `handlefee` decimal(10,2) DEFAULT '0.00',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COMMENT='EUB深圳价目表';

-- ----------------------------
-- Records of trans_freight_eub_jiete
-- ----------------------------
INSERT INTO `trans_freight_eub_jiete` VALUES ('108', 'EUB', '80.0000', '0.50', 'United States', '0.87', '0.90', '60', '7.00', '0', '1408950846', '0', '71', '0');
