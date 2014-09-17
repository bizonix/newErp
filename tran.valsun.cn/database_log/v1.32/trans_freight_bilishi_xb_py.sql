/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-23 11:38:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_bilishi_xb_py`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_bilishi_xb_py`;
CREATE TABLE `trans_freight_bilishi_xb_py` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unitPrice` decimal(10,3) DEFAULT NULL COMMENT '单价',
  `countries` text COMMENT '国家列表',
  `groupName` varchar(24) DEFAULT NULL COMMENT '分区名称',
  `handlefee` decimal(10,3) DEFAULT '0.000' COMMENT '手续费',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '默认0不删除，1删除',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='中国邮政挂号福建渠道价目明细表';

-- ----------------------------
-- Records of trans_freight_bilishi_xb_py
-- ----------------------------
