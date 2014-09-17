/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-02-25 15:28:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_address_carrier_relation`
-- ----------------------------
DROP TABLE IF EXISTS `trans_address_carrier_relation`;
CREATE TABLE `trans_address_carrier_relation` (
  `addressId` int(11) NOT NULL COMMENT '发货地ID',
  `carrierId` int(11) NOT NULL COMMENT '运输方式ID',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认0不删除，1删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发货地址对应运输方式关系对应表';

-- ----------------------------
-- Records of trans_address_carrier_relation
-- ----------------------------
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '2', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '3', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '4', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '5', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '6', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '7', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '8', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '9', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '10', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '29', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '32', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '33', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '34', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '35', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '36', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '37', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('2', '38', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '39', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '40', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '41', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('2', '46', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('2', '47', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '52', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '53', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '58', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '59', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '60', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '61', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('1', '1', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '49', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '50', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '51', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '54', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '55', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '57', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '56', '0');
INSERT INTO `trans_address_carrier_relation` VALUES ('5', '48', '0');
