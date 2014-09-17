/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-25 13:57:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_sg_dhl_gm_gh`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_sg_dhl_gm_gh`;
CREATE TABLE `trans_freight_sg_dhl_gm_gh` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `groupName` varchar(50) DEFAULT NULL COMMENT '组名',
  `paTranFee` decimal(10,4) DEFAULT '0.0000' COMMENT '包裹运输费，新币/KG',
  `paFee` decimal(10,4) DEFAULT '0.0000' COMMENT '包裹处理费，新币/个',
  `delFee` decimal(10,4) DEFAULT '0.0000' COMMENT '目的地派送费，新币/KG',
  `clsFee` decimal(10,4) DEFAULT '0.0000' COMMENT '清关费，新币/票',
  `countrys` text NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `zgTranFee` decimal(10,4) DEFAULT '0.0000' COMMENT '中港运输费，RMB/KG',
  `airFee` decimal(10,4) DEFAULT '0.0000' COMMENT '空运费，港币/KG',
  `otherFee` decimal(10,4) DEFAULT '0.0000' COMMENT '其它费用,RMB',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='新加坡DHL GM挂号价目表';

-- ----------------------------
-- Records of trans_freight_sg_dhl_gm_gh
-- ----------------------------
INSERT INTO `trans_freight_sg_dhl_gm_gh` VALUES ('1', 'test', '12.4000', '1.0000', '0.1500', '30.0000', 'Australia', '0.96', '1.0000', '9.2000', '0.6000', '1398397378', '1398397691', '71', '71', '0');
