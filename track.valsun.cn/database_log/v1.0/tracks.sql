/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : tracks

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-01-24 10:36:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tracks_access_ban`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_access_ban`;
CREATE TABLE `tracks_access_ban` (
  `ip` varchar(16) NOT NULL DEFAULT '0' COMMENT 'IP地址',
  `expires` int(10) NOT NULL DEFAULT '0' COMMENT '过期禁止时间，0永久',
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip` (`ip`) USING BTREE,
  KEY `expires` (`expires`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_access_ban
-- ----------------------------

-- ----------------------------
-- Table structure for `tracks_access_noban`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_access_noban`;
CREATE TABLE `tracks_access_noban` (
  `ip` varchar(16) NOT NULL DEFAULT '0' COMMENT 'IP地址',
  `expires` int(10) NOT NULL DEFAULT '0' COMMENT '访问过期时间，0不过期',
  PRIMARY KEY (`ip`),
  UNIQUE KEY `ip` (`ip`) USING BTREE,
  KEY `expires` (`expires`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_access_noban
-- ----------------------------

-- ----------------------------
-- Table structure for `tracks_access_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_access_statistics`;
CREATE TABLE `tracks_access_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `ip` varchar(16) NOT NULL DEFAULT '127.0.0.1' COMMENT 'IP地址',
  `count` int(10) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `expires` int(10) NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE,
  KEY `count` (`count`) USING BTREE,
  KEY `expires` (`expires`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_access_statistics
-- ----------------------------
INSERT INTO `tracks_access_statistics` VALUES ('2', '192.168.16.238', '6', '1390527103');
INSERT INTO `tracks_access_statistics` VALUES ('3', '192.168.16.113', '10', '1390443013');
INSERT INTO `tracks_access_statistics` VALUES ('4', '192.168.13.36', '1', '1390555883');
INSERT INTO `tracks_access_statistics` VALUES ('5', '192.168.16.200', '7', '1390369763');
INSERT INTO `tracks_access_statistics` VALUES ('6', '192.168.16.131', '3', '1390550948');

-- ----------------------------
-- Table structure for `tracks_track_number_detail_61`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_track_number_detail_61`;
CREATE TABLE `tracks_track_number_detail_61` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trackNumber` varchar(30) DEFAULT NULL COMMENT '跟踪号',
  `postion` varchar(100) DEFAULT NULL COMMENT '处理地点',
  `event` varchar(200) DEFAULT NULL COMMENT '事件',
  `trackTime` int(10) DEFAULT '0' COMMENT '跟踪时间',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `trackNumber` (`trackNumber`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_track_number_detail_61
-- ----------------------------
INSERT INTO `tracks_track_number_detail_61` VALUES ('87', '6032756', '51810302', 'Collection', '1381244560', '1390308406');
INSERT INTO `tracks_track_number_detail_61` VALUES ('88', '6032756', '51805300', 'Opening', '1381410331', '1390308406');
INSERT INTO `tracks_track_number_detail_61` VALUES ('89', '6032756', '51805300', 'Departure from outward office of exchange', '1381511127', '1390308406');
INSERT INTO `tracks_track_number_detail_61` VALUES ('90', '6032756', 'GB', 'Arrival at inward office of exchange', '1382356694', '1390308406');
INSERT INTO `tracks_track_number_detail_61` VALUES ('91', '6032756', 'GB', 'Dispatching', '1382615696', '1390308406');
INSERT INTO `tracks_track_number_detail_61` VALUES ('92', '7882302', '51810302', 'Collection', '1388940086', '1390353589');
INSERT INTO `tracks_track_number_detail_61` VALUES ('93', '7882302', '51805300', 'Opening', '1389120702', '1390353589');
INSERT INTO `tracks_track_number_detail_61` VALUES ('94', '7882302', '51805300', 'Departure from outward office of exchange', '1389219704', '1390353590');
INSERT INTO `tracks_track_number_detail_61` VALUES ('95', '7882302', 'AU', 'Arrival at inward office of exchange', '1390076628', '1390353590');
INSERT INTO `tracks_track_number_detail_61` VALUES ('96', '7094136', '51810302', 'Collection', '1385816101', '1390356449');
INSERT INTO `tracks_track_number_detail_61` VALUES ('97', '7094136', '51805300', 'Opening', '1386139754', '1390356449');
INSERT INTO `tracks_track_number_detail_61` VALUES ('98', '7094136', '51805300', 'Departure from outward office of exchange', '1386365263', '1390356449');
INSERT INTO `tracks_track_number_detail_61` VALUES ('99', '7094136', 'US', 'Arrival at inward office of exchange', '1387028909', '1390356449');
INSERT INTO `tracks_track_number_detail_61` VALUES ('100', '7094136', 'US', 'Dispatching', '1387202807', '1390356449');
INSERT INTO `tracks_track_number_detail_61` VALUES ('101', '7030705', '51810302', 'Collection', '1385542046', '1390356619');
INSERT INTO `tracks_track_number_detail_61` VALUES ('102', '7030705', '51805300', 'Opening', '1385939076', '1390356619');
INSERT INTO `tracks_track_number_detail_61` VALUES ('103', '7030705', '51805300', 'Departure from outward office of exchange', '1386127747', '1390356619');
INSERT INTO `tracks_track_number_detail_61` VALUES ('104', '7030705', 'LK', 'Arrival at inward office of exchange', '1386792902', '1390356619');
INSERT INTO `tracks_track_number_detail_61` VALUES ('105', '7030705', 'LK', 'Dispatching', '1386971677', '1390356619');
INSERT INTO `tracks_track_number_detail_61` VALUES ('106', '7023806', '51810302', 'Collection', '1385612317', '1390356719');
INSERT INTO `tracks_track_number_detail_61` VALUES ('107', '7023806', '51805300', 'Opening', '1385892264', '1390356719');
INSERT INTO `tracks_track_number_detail_61` VALUES ('108', '7023806', '51805300', 'Departure from outward office of exchange', '1386115755', '1390356719');
INSERT INTO `tracks_track_number_detail_61` VALUES ('109', '7023806', 'AU', 'Arrival at inward office of exchange', '1386808052', '1390356719');
INSERT INTO `tracks_track_number_detail_61` VALUES ('110', '7023806', 'AU', 'Dispatching', '1386961749', '1390356719');
INSERT INTO `tracks_track_number_detail_61` VALUES ('111', '7019975', '51810302', 'Collection', '1385441644', '1390356813');
INSERT INTO `tracks_track_number_detail_61` VALUES ('112', '7019975', '51805300', 'Opening', '1385819197', '1390356813');
INSERT INTO `tracks_track_number_detail_61` VALUES ('113', '7019975', '51805300', 'Departure from outward office of exchange', '1385965210', '1390356813');
INSERT INTO `tracks_track_number_detail_61` VALUES ('114', '7019975', 'IE', 'Arrival at inward office of exchange', '1386654260', '1390356813');
INSERT INTO `tracks_track_number_detail_61` VALUES ('115', '7019975', 'IE', 'Dispatching', '1386850898', '1390356813');
INSERT INTO `tracks_track_number_detail_61` VALUES ('116', '7006778', '51810302', 'Collection', '1385382293', '1390357128');
INSERT INTO `tracks_track_number_detail_61` VALUES ('117', '7006778', '51805300', 'Opening', '1385758867', '1390357128');
INSERT INTO `tracks_track_number_detail_61` VALUES ('118', '7006778', '51805300', 'Departure from outward office of exchange', '1385922767', '1390357128');
INSERT INTO `tracks_track_number_detail_61` VALUES ('119', '7006778', 'FR', 'Arrival at inward office of exchange', '1386648891', '1390357128');
INSERT INTO `tracks_track_number_detail_61` VALUES ('120', '7006778', 'FR', 'Dispatching', '1386808740', '1390357128');
INSERT INTO `tracks_track_number_detail_61` VALUES ('121', '7003591', '51810302', 'Collection', '1385750530', '1390357176');
INSERT INTO `tracks_track_number_detail_61` VALUES ('122', '7003591', '51805300', 'Opening', '1386136410', '1390357176');
INSERT INTO `tracks_track_number_detail_61` VALUES ('123', '7003591', '51805300', 'Departure from outward office of exchange', '1386264113', '1390357176');
INSERT INTO `tracks_track_number_detail_61` VALUES ('124', '7003591', 'IL', 'Arrival at inward office of exchange', '1386960966', '1390357176');
INSERT INTO `tracks_track_number_detail_61` VALUES ('125', '7003591', 'IL', 'Dispatching', '1387159534', '1390357176');
INSERT INTO `tracks_track_number_detail_61` VALUES ('126', '6999492', '51810302', 'Collection', '1385399877', '1390357215');
INSERT INTO `tracks_track_number_detail_61` VALUES ('127', '6999492', '51805300', 'Opening', '1385770705', '1390357215');
INSERT INTO `tracks_track_number_detail_61` VALUES ('128', '6999492', '51805300', 'Departure from outward office of exchange', '1385910158', '1390357215');
INSERT INTO `tracks_track_number_detail_61` VALUES ('129', '6999492', 'NL', 'Arrival at inward office of exchange', '1386595900', '1390357215');
INSERT INTO `tracks_track_number_detail_61` VALUES ('130', '6999492', 'NL', 'Dispatching', '1386750671', '1390357215');
INSERT INTO `tracks_track_number_detail_61` VALUES ('131', '6980388', '51810302', 'Collection', '1385217382', '1390358113');
INSERT INTO `tracks_track_number_detail_61` VALUES ('132', '6980388', '51805300', 'Opening', '1385445367', '1390358113');
INSERT INTO `tracks_track_number_detail_61` VALUES ('133', '6980388', '51805300', 'Departure from outward office of exchange', '1385527203', '1390358113');
INSERT INTO `tracks_track_number_detail_61` VALUES ('134', '6980388', 'US', 'Arrival at inward office of exchange', '1386341550', '1390358113');
INSERT INTO `tracks_track_number_detail_61` VALUES ('135', '6980388', 'US', 'Dispatching', '1386606962', '1390358113');
INSERT INTO `tracks_track_number_detail_61` VALUES ('136', '6980391', '51810302', 'Collection', '1385255098', '1390360554');
INSERT INTO `tracks_track_number_detail_61` VALUES ('137', '6980391', '51805300', 'Opening', '1385455764', '1390360554');
INSERT INTO `tracks_track_number_detail_61` VALUES ('138', '6980391', '51805300', 'Departure from outward office of exchange', '1385521745', '1390360555');
INSERT INTO `tracks_track_number_detail_61` VALUES ('139', '6980391', 'US', 'Arrival at inward office of exchange', '1386404025', '1390360555');
INSERT INTO `tracks_track_number_detail_61` VALUES ('140', '6980391', 'US', 'Dispatching', '1386655722', '1390360555');
INSERT INTO `tracks_track_number_detail_61` VALUES ('141', '7691527', '51810302', 'Collection', '1388107552', '1390371456');
INSERT INTO `tracks_track_number_detail_61` VALUES ('142', '7691527', '51805300', 'Opening', '1388259355', '1390371456');
INSERT INTO `tracks_track_number_detail_61` VALUES ('143', '7691527', '51805300', 'Departure from outward office of exchange', '1388378098', '1390371456');
INSERT INTO `tracks_track_number_detail_61` VALUES ('144', '7691527', 'US', 'Arrival at inward office of exchange', '1389184920', '1390371456');
INSERT INTO `tracks_track_number_detail_61` VALUES ('145', '7691527', 'US', 'Dispatching', '1389500180', '1390371456');
INSERT INTO `tracks_track_number_detail_61` VALUES ('146', '7691524', '51810302', 'Collection', '1388061057', '1390383625');
INSERT INTO `tracks_track_number_detail_61` VALUES ('147', '7691524', '51805300', 'Opening', '1388226507', '1390383625');
INSERT INTO `tracks_track_number_detail_61` VALUES ('148', '7691524', '51805300', 'Departure from outward office of exchange', '1388307643', '1390383625');
INSERT INTO `tracks_track_number_detail_61` VALUES ('149', '7691524', 'US', 'Arrival at inward office of exchange', '1389252867', '1390383625');
INSERT INTO `tracks_track_number_detail_61` VALUES ('150', '7691524', 'US', 'Dispatching', '1389466881', '1390383625');
INSERT INTO `tracks_track_number_detail_61` VALUES ('151', '7691521', '51810302', 'Collection', '1388129558', '1390383645');
INSERT INTO `tracks_track_number_detail_61` VALUES ('152', '7691521', '51805300', 'Opening', '1388267176', '1390383645');
INSERT INTO `tracks_track_number_detail_61` VALUES ('153', '7691521', '51805300', 'Departure from outward office of exchange', '1388379021', '1390383645');
INSERT INTO `tracks_track_number_detail_61` VALUES ('154', '7691521', 'US', 'Arrival at inward office of exchange', '1389196213', '1390383645');
INSERT INTO `tracks_track_number_detail_61` VALUES ('155', '7691521', 'US', 'Dispatching', '1389498692', '1390383645');
