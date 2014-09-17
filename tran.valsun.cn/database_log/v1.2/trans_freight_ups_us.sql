/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-02-25 14:54:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_ups_us`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_ups_us`;
CREATE TABLE `trans_freight_ups_us` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `min_weight` decimal(10,1) DEFAULT '0.0' COMMENT '最小重量（KG）',
  `max_weight` decimal(10,1) DEFAULT '0.0' COMMENT '最大重量（KG)',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `fuelcosts` decimal(10,4) DEFAULT '0.0000' COMMENT '燃油费率',
  `vat` decimal(10,2) DEFAULT '0.00' COMMENT '增值税',
  `type` tinyint(1) DEFAULT '1' COMMENT '运输类型，1(EXPEDITED),2(EXPRESS SAVER)',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '编辑时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人ID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_ups_us
-- ----------------------------
INSERT INTO `trans_freight_ups_us` VALUES ('1', '0.0', '0.5', '48.20', '0.1850', '0.06', '1', '1389692242', '1389692691', '71', '71', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('2', '0.5', '1.0', '63.20', '0.1850', '0.06', '1', '1389693093', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('3', '1.0', '1.5', '78.59', '0.1850', '0.06', '1', '1389693120', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('4', '1.5', '2.0', '93.60', '0.1850', '0.06', '1', '1389693149', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('5', '2.0', '2.5', '103.79', '0.1850', '0.06', '1', '1389693192', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('6', '2.5', '3.0', '119.40', '0.1850', '0.06', '1', '1389693212', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('7', '3.0', '3.5', '123.99', '0.1850', '0.06', '1', '1389693235', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('8', '3.5', '4.0', '137.40', '0.1850', '0.06', '1', '1389693341', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('9', '4.0', '4.5', '151.08', '0.1850', '0.06', '1', '1389693367', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('10', '4.5', '5.0', '164.20', '0.1850', '0.06', '1', '1389693392', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('11', '5.0', '5.5', '177.53', '0.1850', '0.06', '1', '1389693412', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('12', '5.5', '6.0', '191.00', '0.1850', '0.06', '1', '1389693435', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('13', '6.0', '6.5', '204.76', '0.1850', '0.06', '1', '1389693487', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('14', '6.5', '7.0', '218.40', '0.1850', '0.06', '1', '1389693506', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('15', '7.0', '7.5', '231.57', '0.1850', '0.06', '1', '1389693534', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('16', '7.5', '8.0', '245.20', '0.1850', '0.06', '1', '1389693553', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('17', '8.0', '8.5', '258.77', '0.1850', '0.06', '1', '1389693575', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('18', '8.5', '9.0', '272.40', '0.1850', '0.06', '1', '1389693595', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('19', '9.0', '9.5', '286.05', '0.1850', '0.06', '1', '1389693614', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('20', '9.5', '10.0', '299.40', '0.1850', '0.06', '1', '1389693642', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('21', '10.0', '10.5', '313.92', '0.1850', '0.06', '1', '1389693690', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('22', '10.5', '11.0', '324.40', '0.1850', '0.06', '1', '1389693709', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('23', '11.0', '11.5', '338.45', '0.1850', '0.06', '1', '1389693726', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('24', '11.5', '12.0', '349.20', '0.1850', '0.06', '1', '1389693745', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('25', '12.0', '12.5', '363.53', '0.1850', '0.06', '1', '1389693786', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('26', '12.5', '13.0', '374.40', '0.1850', '0.06', '1', '1389693803', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('27', '13.0', '13.5', '388.25', '0.1850', '0.06', '1', '1389693827', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('28', '13.5', '14.0', '399.20', '0.1850', '0.06', '1', '1389693850', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('29', '14.0', '14.5', '414.02', '0.1850', '0.06', '1', '1389693867', '1389693991', '71', '71', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('30', '14.5', '15.0', '424.60', '0.1850', '0.06', '1', '1389702148', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('31', '15.0', '15.5', '438.63', '0.1850', '0.06', '1', '1389702169', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('32', '15.5', '16.0', '449.60', '0.1850', '0.06', '1', '1389702198', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('33', '16.0', '16.5', '463.21', '0.1850', '0.06', '1', '1389702229', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('34', '16.5', '17.0', '474.40', '0.1850', '0.06', '1', '1389702518', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('35', '17.0', '17.5', '482.36', '0.1850', '0.06', '1', '1389702539', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('36', '17.5', '18.0', '493.00', '0.1850', '0.06', '1', '1389702559', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('37', '18.0', '18.5', '493.17', '0.1850', '0.06', '1', '1389702579', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('38', '18.5', '19.0', '504.20', '0.1850', '0.06', '1', '1389702609', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('39', '19.0', '19.5', '517.03', '0.1850', '0.06', '1', '1389702632', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('40', '19.5', '20.0', '527.80', '0.1850', '0.06', '1', '1389702681', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('41', '21.0', '44.0', '28.60', '0.1850', '0.06', '1', '1389702743', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('42', '45.0', '70.0', '29.28', '0.1850', '0.06', '1', '1389702762', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('43', '71.0', '99.0', '30.16', '0.1850', '0.06', '1', '1389702787', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('44', '100.0', '299.0', '29.90', '0.1850', '0.06', '1', '1389702807', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('45', '300.0', '499.0', '29.12', '0.1850', '0.06', '1', '1389702824', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('46', '500.0', '999.0', '28.60', '0.1850', '0.06', '1', '1389702884', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('47', '1000.0', '9999.0', '28.08', '0.1850', '0.06', '1', '1389702933', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('48', '0.0', '0.5', '71.50', '0.1850', '0.06', '2', '1389692242', '1389692691', '71', '71', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('49', '0.5', '1.0', '93.75', '0.1850', '0.06', '2', '1389693093', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('50', '1.0', '1.5', '116.50', '0.1850', '0.06', '2', '1389693120', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('51', '1.5', '2.0', '138.75', '0.1850', '0.06', '2', '1389693149', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('52', '2.0', '2.5', '161.25', '0.1850', '0.06', '2', '1389693192', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('53', '2.5', '3.0', '185.50', '0.1850', '0.06', '2', '1389693212', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('54', '3.0', '3.5', '208.00', '0.1850', '0.06', '2', '1389693235', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('55', '3.5', '4.0', '230.50', '0.1850', '0.06', '2', '1389693341', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('56', '4.0', '4.5', '253.25', '0.1850', '0.06', '2', '1389693367', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('57', '4.5', '5.0', '220.20', '0.1850', '0.06', '2', '1389693392', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('58', '5.0', '5.5', '237.20', '0.1850', '0.06', '2', '1389693412', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('59', '5.5', '6.0', '255.20', '0.1850', '0.06', '2', '1389693435', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('60', '6.0', '6.5', '273.20', '0.1850', '0.06', '2', '1389693487', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('61', '6.5', '7.0', '291.40', '0.1850', '0.06', '2', '1389693506', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('62', '7.0', '7.5', '309.20', '0.1850', '0.06', '2', '1389693534', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('63', '7.5', '8.0', '327.40', '0.1850', '0.06', '2', '1389693553', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('64', '8.0', '8.5', '345.40', '0.1850', '0.06', '2', '1389693575', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('65', '8.5', '9.0', '363.60', '0.1850', '0.06', '2', '1389693595', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('66', '9.0', '9.5', '381.40', '0.1850', '0.06', '2', '1389693614', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('67', '9.5', '10.0', '399.20', '0.1850', '0.06', '2', '1389693642', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('68', '10.0', '10.5', '516.50', '0.1850', '0.06', '2', '1389693690', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('69', '10.5', '11.0', '533.75', '0.1850', '0.06', '2', '1389693709', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('70', '11.0', '11.5', '551.00', '0.1850', '0.06', '2', '1389693726', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('71', '11.5', '12.0', '568.50', '0.1850', '0.06', '2', '1389693745', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('72', '12.0', '12.5', '585.50', '0.1850', '0.06', '2', '1389693786', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('73', '12.5', '13.0', '603.00', '0.1850', '0.06', '2', '1389693803', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('74', '13.0', '13.5', '620.50', '0.1850', '0.06', '2', '1389693827', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('75', '13.5', '14.0', '638.00', '0.1850', '0.06', '2', '1389693850', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('76', '14.0', '14.5', '655.25', '0.1850', '0.06', '2', '1389693867', '1389693991', '71', '71', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('77', '14.5', '15.0', '672.00', '0.1850', '0.06', '2', '1389702148', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('78', '15.0', '15.5', '689.75', '0.1850', '0.06', '2', '1389702169', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('79', '15.5', '16.0', '707.00', '0.1850', '0.06', '2', '1389702198', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('80', '16.0', '16.5', '724.50', '0.1850', '0.06', '2', '1389702229', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('81', '16.5', '17.0', '742.00', '0.1850', '0.06', '2', '1389702518', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('82', '17.0', '17.5', '759.50', '0.1850', '0.06', '2', '1389702539', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('83', '17.5', '18.0', '776.25', '0.1850', '0.06', '2', '1389702559', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('84', '18.0', '18.5', '793.50', '0.1850', '0.06', '2', '1389702579', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('85', '18.5', '19.0', '811.25', '0.1850', '0.06', '2', '1389702609', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('86', '19.0', '19.5', '828.50', '0.1850', '0.06', '2', '1389702632', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('87', '19.5', '20.0', '845.75', '0.1850', '0.06', '2', '1389702681', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('88', '21.0', '44.0', '44.28', '0.1850', '0.06', '2', '1389702743', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('89', '45.0', '70.0', '45.24', '0.1850', '0.06', '2', '1389702762', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('90', '71.0', '99.0', '45.57', '0.1850', '0.06', '2', '1389702787', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('91', '100.0', '299.0', '43.09', '0.1850', '0.06', '2', '1389702807', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('92', '300.0', '499.0', '39.99', '0.1850', '0.06', '2', '1389702824', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('93', '500.0', '999.0', '37.51', '0.1850', '0.06', '2', '1389702884', '0', '71', '0', '0');
INSERT INTO `trans_freight_ups_us` VALUES ('94', '1000.0', '9999.0', '35.96', '0.1850', '0.06', '2', '1389702933', '0', '71', '0', '0');
