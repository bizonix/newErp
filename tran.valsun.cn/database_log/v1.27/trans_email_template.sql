/*
Navicat MySQL Data Transfer

Source Server         : testdb.valsun.cn
Source Server Version : 50529
Source Host           : testdb.valsun.cn:3306
Source Database       : trans

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-11 10:31:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_email_template`
-- ----------------------------
DROP TABLE IF EXISTS `trans_email_template`;
CREATE TABLE `trans_email_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platForm` varchar(20) DEFAULT 'aliexpress' COMMENT '平台名称',
  `tempName` varchar(20) DEFAULT '' COMMENT '邮件模版名称',
  `title` varchar(100) DEFAULT '' COMMENT '邮件标题模版',
  `content` text COMMENT '邮件内容模版',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人UID',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '编辑人UID',
  `editTime` int(10) DEFAULT '0' COMMENT '编辑时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  PRIMARY KEY (`id`),
  KEY `platForm` (`platForm`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_email_template
-- ----------------------------
INSERT INTO `trans_email_template` VALUES ('1', 'aliexpress', '速卖通跟踪邮件模版', 'Your Order <weod:recordId> has been shipped', 'Your Order <wedo:recordId> has been shipped\n\nDear <wedo:userId>\nIt is a pleasant to inform you that your order <wedo:recordId> has been shipped on <wedo:markTime>\nThe Tracking number is : <wedo:trackNum>,\nyou can track the sending process on the web: <wedo:trackUrl>\n\n1. If you are satisfied with the items you have received, please click ‘ Confirm Order Received ’and give me 5 star feedback.\n2. If you have not received your items, or you are not satisfied with your items, pls contact me at once, we will help you solve the problem.\n3. If you would like to extend your Purchase Protection you can contact  me directly , we will extend the delivery time for you.\nIf you have any further questions, please do not hesitate to contact me by the email: <wedo:userEmail>, have a nice day!\n\nSincerely,\n<wedo:userName>\n<wedo:sendTime>\nThis is a system message, please do not reply directly.', '71', '1397121263', '71', '1397129785', '0');
