/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:08:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `power_session`
-- ----------------------------
DROP TABLE IF EXISTS `power_session`;
CREATE TABLE `power_session` (
  `session_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '会话编号',
  `session_name` varchar(15) NOT NULL COMMENT '会话名称(用户登录名)',
  `session_time` char(10) NOT NULL COMMENT '用户登录时间(存储时间戳)',
  `session_system_id` tinyint(3) unsigned NOT NULL COMMENT '登录到的系统编号',
  `session_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除(1表示删除,0表示未删除)',
  `session_client_ip` varchar(30) DEFAULT NULL COMMENT '访问的用户ip地址',
  `session_company_id` int(5) unsigned DEFAULT NULL COMMENT '所属用户的公司编号',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=57715 DEFAULT CHARSET=utf8 COMMENT='会话管理表';

-- ----------------------------
-- Records of power_session
-- ----------------------------
