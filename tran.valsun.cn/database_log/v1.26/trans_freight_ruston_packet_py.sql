/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-03-29 10:28:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_ruston_packet_py`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_ruston_packet_py`;
CREATE TABLE `trans_freight_ruston_packet_py` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT NULL COMMENT '组名',
  `countrys` text COMMENT '国家',
  `price` decimal(10,4) DEFAULT '0.0000' COMMENT '首重单价',
  `nextPrice` decimal(10,4) DEFAULT '0.0000' COMMENT '每公斤续重多少钱',
  `maxWeight` decimal(10,2) DEFAULT '1.80' COMMENT '小包最大公斤值限制',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `handlefee` decimal(10,2) DEFAULT NULL,
  `addTime` int(10) DEFAULT NULL,
  `editTime` int(10) DEFAULT NULL,
  `add_user_id` int(10) DEFAULT NULL,
  `edit_user_id` int(10) DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_ruston_packet_py
-- ----------------------------
INSERT INTO `trans_freight_ruston_packet_py` VALUES ('3', '俄罗斯小包', 'Russia,Russian Federation,Russische Föderation', '78.0000', '0.0000', '1.80', '0.00', '6.00', '1395729830', '1395730363', '71', '71', '0');
