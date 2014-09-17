/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-02-25 15:24:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_sto_shenzhen`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_sto_shenzhen`;
CREATE TABLE `trans_freight_sto_shenzhen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `areaId` int(10) DEFAULT '0' COMMENT '中国区域ID',
  `firstWeight` decimal(10,2) DEFAULT '0.00' COMMENT '首重，单位KG',
  `price` decimal(10,4) DEFAULT '0.0000' COMMENT '首重单价',
  `nextPrice` decimal(10,4) DEFAULT '0.0000' COMMENT '每公斤续重多少钱',
  `noPrice` int(10) DEFAULT '0' COMMENT '多少公斤以上不算首重价格',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `handlefee` decimal(10,2) DEFAULT NULL,
  `addTime` int(10) DEFAULT NULL,
  `editTime` int(10) DEFAULT NULL,
  `add_user_id` int(10) DEFAULT NULL,
  `edit_user_id` int(10) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `areaId` (`areaId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_sto_shenzhen
-- ----------------------------
INSERT INTO `trans_freight_sto_shenzhen` VALUES ('1', '234', '1.00', '7.0000', '1.0000', '2', '0.00', '0.00', '1392884552', '1392969460', '71', '71', '0');
INSERT INTO `trans_freight_sto_shenzhen` VALUES ('2', '235', '1.00', '7.0000', '1.0000', '0', '0.00', '0.00', '1392886584', '1392985064', '71', '71', '0');
INSERT INTO `trans_freight_sto_shenzhen` VALUES ('3', '237', '1.00', '7.0000', '4.0000', '1', '0.90', '1.00', '1392966698', null, '11', null, '1');
