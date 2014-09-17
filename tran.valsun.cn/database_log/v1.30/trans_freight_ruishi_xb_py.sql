/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-05-14 16:49:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_ruishi_xb_py`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_ruishi_xb_py`;
CREATE TABLE `trans_freight_ruishi_xb_py` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unitPrice` decimal(10,4) DEFAULT '0.0000' COMMENT '单价(HKD)',
  `countries` text COMMENT '国家列表',
  `groupName` varchar(20) DEFAULT NULL,
  `handlefee` decimal(10,4) DEFAULT '0.0000' COMMENT '挂号费(HKD)',
  `zgTranFee` decimal(10,4) DEFAULT '0.0000' COMMENT '中港运输费(HKD)',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `level` varchar(20) DEFAULT NULL COMMENT '级别',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_ruishi_xb_py
-- ----------------------------
INSERT INTO `trans_freight_ruishi_xb_py` VALUES ('27', '96.0000', 'United Kingdom', 'UK', '0.0000', '1.5000', '0.00', 'Priority', '0', '1400051862', '1400052830', '71', '71');
