/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-07-15 11:30:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_carrier_process_node`
-- ----------------------------
DROP TABLE IF EXISTS `trans_carrier_process_node`;
CREATE TABLE `trans_carrier_process_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `carrierId` int(10) DEFAULT '0' COMMENT '运输方式ID',
  `nodeName` char(10) DEFAULT '' COMMENT '节点名称',
  `nodeKey` char(50) DEFAULT '' COMMENT '事件触发关键词，多个空格隔开',
  `typeId` tinyint(2) DEFAULT '0' COMMENT '通知类型，0邮件',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人uid',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `carrierId` (`carrierId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;