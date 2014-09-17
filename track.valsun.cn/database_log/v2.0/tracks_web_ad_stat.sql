/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:08:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tracks_web_ad_stat`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_web_ad_stat`;
CREATE TABLE `tracks_web_ad_stat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `adId` int(10) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `ip` char(15) NOT NULL DEFAULT '127.0.0.1' COMMENT 'IP地址',
  `ipNum` bigint(12) NOT NULL DEFAULT '0' COMMENT 'IP数字位',
  `count` smallint(6) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `lastTime` int(10) NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `ipNum` (`ipNum`) USING BTREE,
  KEY `addTime` (`addTime`) USING BTREE,
  KEY `lastTime` (`lastTime`) USING BTREE,
  KEY `adId` (`adId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_web_ad_stat
-- ----------------------------
INSERT INTO `tracks_web_ad_stat` VALUES ('1', '4', '192.168.16.238', '3232239854', '4', '1407138755', '1407140510', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('2', '8', '192.168.16.238', '3232239854', '5', '1407139036', '1407145120', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('3', '2', '192.168.16.46', '3232239662', '1', '1407141656', '1407141656', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('4', '2', '192.168.16.238', '3232239854', '1', '1407150453', '1407150453', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('5', '2', '192.168.16.238', '3232239854', '1', '1407199626', '1407199626', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('6', '2', '192.168.16.160', '3232239776', '1', '1407200413', '1407200413', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('7', '8', '192.168.16.69', '3232239685', '10', '1407201320', '1407201334', '0');
INSERT INTO `tracks_web_ad_stat` VALUES ('8', '2', '192.168.16.69', '3232239685', '6', '1407201493', '1407201513', '0');
