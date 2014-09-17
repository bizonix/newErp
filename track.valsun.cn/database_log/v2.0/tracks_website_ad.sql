/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:08:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tracks_website_ad`
-- ----------------------------
DROP TABLE IF EXISTS `tracks_website_ad`;
CREATE TABLE `tracks_website_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '广告内容',
  `typeId` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告类型，1文字，2图片，3富媒体',
  `layer` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序，越小的排在前面',
  `is_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用，1禁止',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `add_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '添加人ID',
  `editTime` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `edit_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '修改人UID',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tracks_website_ad
-- ----------------------------
INSERT INTO `tracks_website_ad` VALUES ('1', '测试广告', '爱爱爱1', '2', '0', '1', '1405778644', '71', '1405778664', '71', '1');
INSERT INTO `tracks_website_ad` VALUES ('2', '跟踪号查询结果页面广告', '<img src=\"/upload/image/20140804/20140804190427_71735.jpg\" title=\"Availiable for AD\" alt=\"Availiable for AD\" height=\"128\" width=\"307\" /> <img src=\"/upload/image/20140804/20140804190556_61777.jpg\" title=\"Availiable for AD\" alt=\"Availiable for AD\" class=\"advertisement-margin\" height=\"128\" width=\"307\" /> <img src=\"/upload/image/20140804/20140804190612_32057.jpg\" title=\"Availiable for AD\" alt=\"Availiable for AD\" height=\"128\" width=\"307\" />', '3', '0', '0', '1405930657', '71', '1407202330', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('3', '首页图片广告', '<img src=\"/upload/image/20140804/20140804085155_23976.jpg\" alt=\"\" />', '2', '0', '0', '1406251225', '71', '1407113532', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('4', '运费计算首页第二个图片广告', '<img src=\"/upload/image/20140804/20140804092628_50436.jpg\" alt=\"\" />', '2', '0', '0', '1406529444', '71', '1407115595', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('5', 'test', '<img src=\"/upload/image/20140728/20140728144558_48380.jpg\" alt=\"\" />', '2', '1', '1', '1406529966', '71', '1406529985', '71', '1');
INSERT INTO `tracks_website_ad` VALUES ('6', '首页排行一图片广告', '<li>\n	<img src=\"./public/img/lpi/1-Germany.gif\" width=\"139\" height=\"91\" title=\"Germany (4.12)\" alt=\"Germany (4.12)\" /> <span class=\"rank-number\">1</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/2-Holland.gif\" width=\"139\" height=\"91\" title=\"Holland (4.05)\" alt=\"Holland (4.05)\" /> <span class=\"rank-number\">2</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/3-Belgium.gif\" width=\"139\" height=\"91\" title=\"Belgium (4.04)\" alt=\"Belgium (4.04)\" /> <span class=\"rank-number\">3</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/4-England.gif\" width=\"139\" height=\"91\" title=\"the UK (4.01)\" alt=\"the UK (4.01)\" /> <span class=\"rank-number last-rank-number\">4</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/5-Singapore.gif\" width=\"139\" height=\"91\" title=\"Singapore (4.00)\" alt=\"Singapore (4.00)\" /> <span class=\"rank-number last-rank-number\">5</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/6-Sweden.gif\" width=\"139\" height=\"91\" title=\"Sweden (3.96)\" alt=\"Sweden (3.96)\" /> <span class=\"rank-number last-rank-number\">6</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/7-Norway.gif\" width=\"139\" height=\"91\" title=\"Norway (3.96)\" alt=\"Norway (3.96)\" /> <span class=\"rank-number last-rank-number\">7</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/8-Luxemburg.gif\" width=\"139\" height=\"91\" title=\"Luxemburg (3.95)\" alt=\"Luxemburg (3.95)\" /> <span class=\"rank-number last-rank-number\">8</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/9-America.gif\" width=\"139\" height=\"91\" title=\"USA (3.92)\" alt=\"USA (3.92)\" /> <span class=\"rank-number last-rank-number\">9</span> \n</li>\n<li>\n	<img src=\"./public/img/lpi/10-Japan.gif\" width=\"139\" height=\"91\" title=\" Japan (3.91)\" alt=\" Japan (3.91)\" /> <span class=\"rank-number last-rank-number\">10</span> \n</li>', '2', '0', '0', '1406640166', '71', '1407150629', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('7', '首页排行二图片广告', '<li>\n	<img src=\"./public/img/3pls/1-DHL.gif\" width=\"139\" height=\"91\" title=\"DHL\" alt=\"DHL\" /> <span class=\"rank-number\">1</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/2-kuehne.gif\" width=\"139\" height=\"91\" title=\"Kuehne+Nagel\" alt=\"Kuehne+Nagel\" /> <span class=\"rank-number\">2</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/3-nihon.gif\" width=\"139\" height=\"91\" title=\"Nippon Express\" alt=\"Nippon Express\" /> <span class=\"rank-number\">3</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/4-db-schenker.gif\" width=\"139\" height=\"91\" title=\"Schenker\" alt=\"Schenker\" /> <span class=\"rank-number last-rank-number\">4</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/5-CH-robinson.gif\" width=\"139\" height=\"91\" title=\" CH-Robinson\" alt=\" CH-Robinson\" /> <span class=\"rank-number last-rank-number\">5</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/6-hyundai.gif\" width=\"139\" height=\"91\" title=\"Hyundai Glovis\" alt=\"Hyundai Glovis\" /> <span class=\"rank-number last-rank-number\">6</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/7-CEVA.gif\" width=\"139\" height=\"91\" title=\"CEVA\" alt=\"CEVA\" /> <span class=\"rank-number last-rank-number\">7</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/8-USP.gif\" width=\"139\" height=\"91\" title=\"UPS\" alt=\"UPS\" /> <span class=\"rank-number last-rank-number\">8</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/9-dsv.gif\" width=\"139\" height=\"91\" title=\"DSV\" alt=\"DSV\" /> <span class=\"rank-number last-rank-number\">9</span> \n</li>\n<li>\n	<img src=\"./public/img/3pls/10-zwy.gif\" width=\"139\" height=\"91\" title=\"Sinotrans&amp;CSC\" alt=\"Sinotrans&amp;CSC\" /> <span class=\"rank-number last-rank-number\">10</span> \n</li>', '2', '0', '0', '1406640331', '71', '1407150809', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('8', '运费计算页面第一个图片广告', '<div>\n	<a class=\"li-mid-margin-right\" href=\"#\"> <img src=\"./public/img/1.gif\" height=\"42\" width=\"72\" /> Australia Post<br />\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/2.gif\" height=\"42\" width=\"72\" /> EUB\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/3.gif\" height=\"42\" width=\"72\" /> USPS\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/4.gif\" height=\"42\" width=\"72\" /> UPS<span></span> \n	<div>\n	</div>\n</a> <a class=\"li-mid-margin-left\" href=\"#\"> <img src=\"./public/img/5.gif\" height=\"42\" width=\"72\" /> Swiss Post<br />\n	<div>\n	</div>\n</a> \n</div>\n<div class=\"col2-margin\">\n	<a class=\"li-mid-margin-right\" href=\"#\"> <img src=\"./public/img/6.gif\" height=\"42\" width=\"72\" /> <span>FEDEX</span> \n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/7.gif\" height=\"42\" width=\"72\" /> Russia Post\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/8.gif\" height=\"42\" width=\"72\" /> HK Post\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin\" href=\"#\"> <img src=\"./public/img/9.gif\" height=\"42\" width=\"72\" /> BPOST\n	<div>\n	</div>\n</a> <a class=\"li-mid-margin-left\" href=\"#\"> <img src=\"./public/img/10.gif\" height=\"42\" width=\"72\" /> DHL<span></span> \n	<div>\n	</div>\n</a> \n	<div>\n	</div>\n</div>', '2', '0', '0', '1406727440', '71', '1407141226', '71', '0');
INSERT INTO `tracks_website_ad` VALUES ('9', '12321', '12312', '1', '0', '0', '1406791038', '71', '0', '0', '1');
