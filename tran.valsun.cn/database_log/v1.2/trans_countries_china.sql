/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-02-25 15:20:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_countries_china`
-- ----------------------------
DROP TABLE IF EXISTS `trans_countries_china`;
CREATE TABLE `trans_countries_china` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `countryName` varchar(30) DEFAULT NULL COMMENT '名称',
  `pid` int(10) DEFAULT '0' COMMENT '上级ID',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8 COMMENT='中国国家表';

-- ----------------------------
-- Records of trans_countries_china
-- ----------------------------
INSERT INTO `trans_countries_china` VALUES ('234', '深圳市', '0', '1392188396', '1392188462', '71', '71', '0');
INSERT INTO `trans_countries_china` VALUES ('235', '广东', '0', '1392188405', '0', '71', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('236', '上海', '0', '1392188412', '0', '71', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('237', '江苏', '0', '1392188422', '0', '71', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('238', '浙江', '0', '1392188428', '0', '71', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('239', '北海', '0', '1393204824', '1393204854', '11', '11', '1');
INSERT INTO `trans_countries_china` VALUES ('240', '北京', '0', '1393204898', '0', '11', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('241', '重庆', '0', '1393205266', '0', '11', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('242', '天津', '0', '1393223603', '0', '11', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('243', '辽宁', '0', '1393223667', '0', '11', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('244', '山东', '0', '1393223679', '0', '11', '0', '0');
INSERT INTO `trans_countries_china` VALUES ('245', '河北', '0', '1393223695', '0', '11', '0', '0');
