/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:08:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tracks_website_config`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_website_config`;
CREATE TABLE `tracks_website_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cKey` char(20) NOT NULL DEFAULT '' COMMENT '配置key',
  `cValue` char(200) NOT NULL,
  `is_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启，0开启，1禁止',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '添加人UID',
  `editTime` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_website_config
-- ----------------------------
INSERT INTO `tracks_website_config` VALUES ('1', 'WEB_TRACK_PIC', 'http://tran.valsun.cn', '0', '1405691305', '71', '1405691348', '71', '1');
INSERT INTO `tracks_website_config` VALUES ('2', 'WEB_TRACK_PIC', 'http://track.valsun.cn', '1', '1405692085', '71', '1406528380', '71', '1');
INSERT INTO `tracks_website_config` VALUES ('3', 'WEB_INDEX_TITLE', 'wedo title', '0', '1406446897', '71', '0', '0', '1');
INSERT INTO `tracks_website_config` VALUES ('4', 'WEB_TRACK_PIC', 'wedo11111', '0', '1406446916', '71', '1407207045', '71', '0');
INSERT INTO `tracks_website_config` VALUES ('5', 'WEB_INDEX_DESCRIPTIO', 'wedo description', '0', '1406446934', '71', '0', '0', '0');
INSERT INTO `tracks_website_config` VALUES ('6', 'WEB_TRACK_PIC', 'http://track.valsun.cn', '0', '1406528443', '71', '1406790852', '71', '1');
INSERT INTO `tracks_website_config` VALUES ('7', 'WEB_INDEX_TITLE', 'wedo', '0', '1406531395', '71', '0', '0', '0');
INSERT INTO `tracks_website_config` VALUES ('8', 'WEB_INDEX_DESCRIPTIO', '啊飒飒冯绍峰', '0', '1406771186', '71', '0', '0', '1');
INSERT INTO `tracks_website_config` VALUES ('9', 'WEB_INDEX_DESCRIPTIO', '沙发沙发沙发沙发', '0', '1406771347', '71', '0', '0', '1');
