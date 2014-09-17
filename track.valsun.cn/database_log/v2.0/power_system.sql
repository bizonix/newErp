/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:08:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `power_system`
-- ----------------------------
DROP TABLE IF EXISTS `power_system`;
CREATE TABLE `power_system` (
  `system_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '接口系统编号',
  `system_name` varchar(20) NOT NULL COMMENT '系统名称',
  `system_principal` varchar(15) NOT NULL COMMENT '系统负责人',
  `system_token` char(32) NOT NULL COMMENT '系统授权码',
  `system_token_grant_date` int(10) DEFAULT NULL COMMENT '接口系统token的授权日期',
  `system_token_effective_date` int(10) unsigned DEFAULT NULL COMMENT '接口系统token的有效天数',
  `system_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除(1表示删除,0表示未删除)',
  PRIMARY KEY (`system_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='外接系统管理表';

-- ----------------------------
-- Records of power_system
-- ----------------------------
INSERT INTO `power_system` VALUES ('1', 'Power', '冯赛明', 'dc1f5f11908695ca1d6d1a747929019d', '1370275200', '3650', '0');
INSERT INTO `power_system` VALUES ('2', 'Picture', '曾祥红', '1ffa3ceedd91ccb34e65339f9b1766c5', '1370275200', '3650', '0');
INSERT INTO `power_system` VALUES ('3', 'Pa', '钟衍台', '9538caa6ae062e30c2728309d89adedb', '1372262400', '3650', '0');
INSERT INTO `power_system` VALUES ('4', 'Erp', '林正祥', '401946505033a8a5eeb7a122a22f4ec6', '1370275200', '3650', '0');
INSERT INTO `power_system` VALUES ('5', 'Opensystem', '任达海', 'f10a4ee75fe2e4d778c606ba27b1c7fe', '1371139200', '3650', '0');
INSERT INTO `power_system` VALUES ('6', 'Oversea', '肖金华', '9973cdbddab4c0fbbe9d733b33ef6aa0', '1373299200', '3650', '0');
INSERT INTO `power_system` VALUES ('7', 'ProductCenter', '朱清庭', '507c44e32a5d14cf25de1d53ddccb0f0', '1373558400', '3650', '0');
INSERT INTO `power_system` VALUES ('8', 'Transportsys', '管拥军', '7b199966daac30778e9c1b6a08605b1f', '1419955200', '3650', '0');
INSERT INTO `power_system` VALUES ('9', 'Notice', '温小彬', '66ddc8ed0b81f080749311f494a39fe1', '1374681600', '3650', '0');
INSERT INTO `power_system` VALUES ('10', 'Warehouse', '涂兴隆', 'b5fa8197fd7a8727484e696ab547d031', '1375977600', '3650', '0');
INSERT INTO `power_system` VALUES ('11', 'Purchase', '肖金华', '6ac238efd0e617e0e0905e27f47f6d7d', '1376841600', '3650', '0');
INSERT INTO `power_system` VALUES ('12', 'Ordermanage', '贺明华', 'eccd25ddf4cddea9c46cf77fb6d78fa4', '1378310400', '3650', '0');
INSERT INTO `power_system` VALUES ('13', 'Finejo', '肖金华', '6aa44230ee8908d7fc9f49c236662fde', '1379001600', '3650', '0');
INSERT INTO `power_system` VALUES ('14', 'Message', '胥朝阳', '12aead0936276c4d8bbe32947b9e94b3', '1381248000', '3650', '0');
INSERT INTO `power_system` VALUES ('15', 'qc', '陈伟', 'ee8a55cdf241cd8c4da869fd53495224', '1381852800', '3650', '0');
INSERT INTO `power_system` VALUES ('16', 'Name', '陈伟', '38c0b769bad579dc18d620e1bda1d7be', '1383235200', '3650', '0');
INSERT INTO `power_system` VALUES ('17', 'Price', '冯赛明', '2eaeb5e062f218686268a44b964376de', '1384876800', '3650', '0');
INSERT INTO `power_system` VALUES ('18', 'resolution', '曾祥红', '25c0e6bd57fda8f1ded1315f5e479e9b', '1388073600', '3650', '0');
INSERT INTO `power_system` VALUES ('19', 'Developer', '冯赛明', '73133d34d11fe879a22e1e1c940e7499', '1389542400', '3650', '0');
INSERT INTO `power_system` VALUES ('20', 'Oversold', '冯赛明', '7a6f0df62580135e51d03244c11b6cf0', '1392652800', '3650', '0');
INSERT INTO `power_system` VALUES ('21', 'Subscription', '周婷', 'eaab14a3b421d8a0422e3de2ef82c4f8', '1396195200', '730', '0');
INSERT INTO `power_system` VALUES ('22', 'Feedback', '任达海', '282b6540be2fe3c48c55f5748a321aeb', '1398009600', '365', '0');
INSERT INTO `power_system` VALUES ('23', 'openTran', '管拥军', '43ac80fb277dc7fde0ae3cd01a11fb64', '1399564800', '365', '0');
INSERT INTO `power_system` VALUES ('24', 'wedoExpress', '管拥军', '4ab79afe173aeed3517274595116f38d', '1399564800', '3650', '0');
INSERT INTO `power_system` VALUES ('25', 'amazonlisting', '刘冠云', '170710ba5d33d81ac495680848fe6997', '1402156800', '3650', '0');
INSERT INTO `power_system` VALUES ('26', 'smtlisting', '张宇鹏', '2d5ddc129f63b623aa7787b37c117785', '1403452800', '3650', '0');
INSERT INTO `power_system` VALUES ('27', 'valsun', '曾祥红', 'f27a4b69900d34567f1db100099beca6', '1405440000', '3650', '0');
