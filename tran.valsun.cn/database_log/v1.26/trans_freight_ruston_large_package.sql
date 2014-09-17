/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-03-25 15:17:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_ruston_large_package`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_ruston_large_package`;
CREATE TABLE `trans_freight_ruston_large_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(30) DEFAULT NULL COMMENT '组名',
  `countrys` text COMMENT '国家',
  `price` decimal(10,4) DEFAULT '0.0000' COMMENT '首重单价',
  `nextPrice` decimal(10,4) DEFAULT '0.0000' COMMENT '每公斤续重多少钱',
  `minWeight` decimal(10,2) DEFAULT '1.80' COMMENT '大包最小公斤值限制',
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
-- Records of trans_freight_ruston_large_package
-- ----------------------------
INSERT INTO `trans_freight_ruston_large_package` VALUES ('3', '俄罗斯大包', 'Russia,Russian Federation,Russische Föderation', '85.0000', '25.0000', '1.80', '0.00', '0.00', '1395730259', '1395730373', '71', '71', '0');
