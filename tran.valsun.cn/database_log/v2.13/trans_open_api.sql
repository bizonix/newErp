/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-07-15 11:31:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_open_api`
-- ----------------------------
DROP TABLE IF EXISTS `trans_open_api`;
CREATE TABLE `trans_open_api` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `apiName` char(50) DEFAULT '' COMMENT 'api名称',
  `apiValue` text COMMENT '授权内容',
  `apiUid` int(10) DEFAULT '0' COMMENT 'API使用人UID',
  `apiMaxCount` int(10) DEFAULT '0' COMMENT 'API一天最大调用次数，0不限制',
  `apiToken` char(32) DEFAULT '' COMMENT 'api调用token',
  `apiTokenExpire` int(10) DEFAULT '0' COMMENT 'apitoken 有效期，0不限',
  `is_enable` tinyint(1) DEFAULT '0' COMMENT '是否禁用，1经用',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `apiName` (`apiName`) USING BTREE,
  KEY `apiUid` (`apiUid`) USING BTREE,
  KEY `apiToken` (`apiToken`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
