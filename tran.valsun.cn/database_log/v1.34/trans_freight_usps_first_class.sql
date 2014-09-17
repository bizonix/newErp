/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-30 11:27:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_usps_first_class`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_usps_first_class`;
CREATE TABLE `trans_freight_usps_first_class` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zone` mediumint(6) DEFAULT NULL COMMENT '分区',
  `minWeight` decimal(10,4) DEFAULT '0.0000' COMMENT '最小重量',
  `maxWeight` decimal(10,4) DEFAULT '0.0000' COMMENT '最大重量',
  `cost` decimal(10,4) DEFAULT '0.0000' COMMENT '运费',
  `discount` decimal(10,4) DEFAULT '0.0000' COMMENT '折扣',
  `handlefee` decimal(10,4) DEFAULT '0.0000' COMMENT '住宅运送费',
  `fuelCost` decimal(10,4) DEFAULT '0.0000' COMMENT '燃油附加费',
  `zgTranFee` decimal(10,4) DEFAULT '0.0000' COMMENT '中港运输费(RMB/KG)',
  `airFee` decimal(10,4) DEFAULT '0.0000' COMMENT '空运费（HKD/KG)',
  `clsFee` decimal(10,4) DEFAULT '0.0000' COMMENT '目的地清关费（USD/票）',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `zone` (`zone`)
) ENGINE=InnoDB AUTO_INCREMENT=1202 DEFAULT CHARSET=utf8 COMMENT='海外仓UPS运费计算表';

-- ----------------------------
-- Records of trans_freight_usps_first_class
-- ----------------------------
INSERT INTO `trans_freight_usps_first_class` VALUES ('1201', '1', '0.0000', '0.0280', '1.6900', '0.0000', '0.0000', '0.0000', '1.0000', '29.7000', '0.1400', '71', '1401357628', '71', '1401358435', '0');
