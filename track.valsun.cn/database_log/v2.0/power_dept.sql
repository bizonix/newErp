/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:07:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `power_dept`
-- ----------------------------
DROP TABLE IF EXISTS `power_dept`;
CREATE TABLE `power_dept` (
  `dept_id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门编号',
  `dept_name` varchar(20) NOT NULL COMMENT '部门名称',
  `dept_principal` varchar(15) NOT NULL COMMENT '部门负责人',
  `dept_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除(1表示删除,0表示未删除)',
  `dept_company_id` int(5) unsigned NOT NULL DEFAULT '1' COMMENT '所属公司',
  PRIMARY KEY (`dept_id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COMMENT='部门管理表';

-- ----------------------------
-- Records of power_dept
-- ----------------------------
INSERT INTO `power_dept` VALUES ('1', '总裁办公室', '陈文平', '0', '1');
INSERT INTO `power_dept` VALUES ('2', 'eBay销售一部', '陈小霞', '0', '1');
INSERT INTO `power_dept` VALUES ('3', '系统技术部', '林正祥', '0', '1');
INSERT INTO `power_dept` VALUES ('4', '办公室', '陈文辉', '0', '2');
INSERT INTO `power_dept` VALUES ('5', 'B2B销售部', '罗莉', '0', '1');
INSERT INTO `power_dept` VALUES ('6', '采购二部', '潘旭东', '0', '1');
INSERT INTO `power_dept` VALUES ('7', '产品部', '陈波', '0', '1');
INSERT INTO `power_dept` VALUES ('8', '天猫项目', '罗莹', '0', '1');
INSERT INTO `power_dept` VALUES ('9', '物料部', '陈前', '0', '5');
INSERT INTO `power_dept` VALUES ('10', '人力资源部', '龚丽娜', '0', '1');
INSERT INTO `power_dept` VALUES ('11', '总经办', '陈', '0', '3');
INSERT INTO `power_dept` VALUES ('12', '天猫销售部', '罗莹', '0', '2');
INSERT INTO `power_dept` VALUES ('13', '财务部', '陈晓兰', '0', '1');
INSERT INTO `power_dept` VALUES ('14', '采购部', '陈汉宗', '0', '2');
INSERT INTO `power_dept` VALUES ('15', '设计开放部', '陈汉宗', '0', '1');
INSERT INTO `power_dept` VALUES ('16', 'eBay客服一部', '陈君玉', '0', '1');
INSERT INTO `power_dept` VALUES ('17', '商城技术部', '张振祥', '0', '1');
INSERT INTO `power_dept` VALUES ('18', 'eBay销售二部', '陈月葵', '0', '1');
INSERT INTO `power_dept` VALUES ('19', '采购一部', '郑凤娇', '0', '1');
INSERT INTO `power_dept` VALUES ('20', '服装厂', '待定', '0', '2');
INSERT INTO `power_dept` VALUES ('21', '国内销售部', '包凤鸣', '0', '1');
INSERT INTO `power_dept` VALUES ('22', '独立商城部', '王绪成', '0', '1');
INSERT INTO `power_dept` VALUES ('24', '设计开发部', '陈汉宗', '0', '2');
INSERT INTO `power_dept` VALUES ('23', '海外仓', '待定', '0', '1');
INSERT INTO `power_dept` VALUES ('25', '信息安全部', '李高飞', '1', '1');
INSERT INTO `power_dept` VALUES ('26', '信息管理部', '李高飞', '1', '1');
INSERT INTO `power_dept` VALUES ('27', '后勤部', '待定', '1', '1');
INSERT INTO `power_dept` VALUES ('28', '物料部', '陈', '0', '4');
INSERT INTO `power_dept` VALUES ('29', '综合技术部', '陈', '0', '1');
INSERT INTO `power_dept` VALUES ('30', '天猫销售部', '罗莹', '0', '1');
INSERT INTO `power_dept` VALUES ('31', 'B2B销售部', '陈', '0', '4');
INSERT INTO `power_dept` VALUES ('32', '办公室', '陈', '0', '4');
INSERT INTO `power_dept` VALUES ('33', '天猫销售部', '陈', '0', '4');
INSERT INTO `power_dept` VALUES ('34', '财务部', '陈义杉', '0', '2');
INSERT INTO `power_dept` VALUES ('35', '销售四部', '李美琴', '0', '2');
INSERT INTO `power_dept` VALUES ('36', '销售三部', '陈', '0', '2');
INSERT INTO `power_dept` VALUES ('37', '独立商城部', '待定', '0', '2');
INSERT INTO `power_dept` VALUES ('38', '销售二部', '待定', '0', '2');
INSERT INTO `power_dept` VALUES ('39', '销售一部', '待定', '0', '2');
INSERT INTO `power_dept` VALUES ('40', 'LED', '刘鹏飞', '0', '6');
INSERT INTO `power_dept` VALUES ('41', '生产部', '暂无', '0', '7');
INSERT INTO `power_dept` VALUES ('42', '打板部', '林加华', '0', '7');
INSERT INTO `power_dept` VALUES ('43', '采购部', '陈汉宗', '0', '7');
INSERT INTO `power_dept` VALUES ('44', '总经办', '马云', '0', '8');
INSERT INTO `power_dept` VALUES ('46', '总经办', '陈燕生', '0', '9');
INSERT INTO `power_dept` VALUES ('47', '总经办', '刘双明', '0', '10');
INSERT INTO `power_dept` VALUES ('48', '总经办', '王耀武', '0', '11');
INSERT INTO `power_dept` VALUES ('49', '总经办', '常火文', '0', '12');
INSERT INTO `power_dept` VALUES ('50', '总经办', '李鹏', '0', '13');
INSERT INTO `power_dept` VALUES ('51', '总经办', '王彬彬', '0', '14');
INSERT INTO `power_dept` VALUES ('52', '总经办', '王文斌', '0', '15');
INSERT INTO `power_dept` VALUES ('53', '总经办', '容可', '0', '16');
INSERT INTO `power_dept` VALUES ('54', '总经办', '郭去疾', '0', '17');
INSERT INTO `power_dept` VALUES ('55', '总经理', '王燕', '0', '18');
INSERT INTO `power_dept` VALUES ('56', '总经办', '杨志君', '0', '19');
INSERT INTO `power_dept` VALUES ('57', '总经办', '陆海传', '0', '20');
INSERT INTO `power_dept` VALUES ('58', '总经办', '王旻昊', '0', '21');
INSERT INTO `power_dept` VALUES ('59', '总经办', '李红松', '0', '22');
INSERT INTO `power_dept` VALUES ('60', '总经办', '无', '0', '23');
INSERT INTO `power_dept` VALUES ('61', '总经办', '王德财', '0', '24');
INSERT INTO `power_dept` VALUES ('62', '总经办', '蒋凌辉', '0', '25');
INSERT INTO `power_dept` VALUES ('63', '总经办', '金基松', '0', '26');
INSERT INTO `power_dept` VALUES ('45', '总经办', '陈', '0', '7');
INSERT INTO `power_dept` VALUES ('64', '总经办', '张鸣道', '0', '27');
INSERT INTO `power_dept` VALUES ('65', '总经办', '赵思敏', '0', '28');
INSERT INTO `power_dept` VALUES ('66', '速卖通客服部', '韩庆新', '1', '1');
INSERT INTO `power_dept` VALUES ('67', '速卖通销售二部', '陈智兴', '1', '1');
INSERT INTO `power_dept` VALUES ('68', '速卖通销售一部', '仝召燕', '1', '1');
INSERT INTO `power_dept` VALUES ('95', 'eBay客服二部', '陈月葵', '0', '1');
INSERT INTO `power_dept` VALUES ('69', '速卖通客服部', '韩庆新', '0', '1');
INSERT INTO `power_dept` VALUES ('70', '后勤部', '待定', '0', '1');
INSERT INTO `power_dept` VALUES ('71', '速卖通销售一部', '仝召燕', '0', '1');
INSERT INTO `power_dept` VALUES ('72', '速卖通销售二部', '陈智兴', '0', '1');
INSERT INTO `power_dept` VALUES ('73', '亚马逊销售一部', '李美琴', '1', '1');
INSERT INTO `power_dept` VALUES ('74', '海外销售部&亚马逊销售一部', '李美琴', '0', '1');
INSERT INTO `power_dept` VALUES ('75', '总经办', '管宗斌', '0', '29');
INSERT INTO `power_dept` VALUES ('76', '总经办', '刘雨燕', '0', '30');
INSERT INTO `power_dept` VALUES ('77', '总经办', '管宗斌', '0', '31');
INSERT INTO `power_dept` VALUES ('78', '开放业务', '开放业务', '0', '32');
INSERT INTO `power_dept` VALUES ('79', '开放业务', '开放业务', '1', '32');
INSERT INTO `power_dept` VALUES ('80', '开放业务', '开放业务', '1', '32');
INSERT INTO `power_dept` VALUES ('81', '总经办', '廖新辉', '0', '36');
INSERT INTO `power_dept` VALUES ('82', '总经办', '蔡聪明', '0', '37');
INSERT INTO `power_dept` VALUES ('83', '总经办', '王佳', '0', '38');
INSERT INTO `power_dept` VALUES ('84', '总经办', '李凯', '0', '39');
INSERT INTO `power_dept` VALUES ('85', '物流部', '陈前', '0', '5');
INSERT INTO `power_dept` VALUES ('86', '总经办', '陈凯丹', '0', '40');
INSERT INTO `power_dept` VALUES ('87', '总经办', '辛小芳', '0', '41');
INSERT INTO `power_dept` VALUES ('88', '总经办', '蒋婧雯', '0', '42');
INSERT INTO `power_dept` VALUES ('89', '总经办', '李超红', '0', '44');
INSERT INTO `power_dept` VALUES ('90', '总经办', '陈颖颖', '0', '45');
INSERT INTO `power_dept` VALUES ('91', '总经办', '寻佑兰', '0', '46');
INSERT INTO `power_dept` VALUES ('92', '总经办', '李健文', '0', '47');
INSERT INTO `power_dept` VALUES ('93', '总办', '林益池', '0', '48');
INSERT INTO `power_dept` VALUES ('94', '总经办', '丰满', '0', '49');
INSERT INTO `power_dept` VALUES ('96', '总经办', '邓秀丽', '0', '50');
INSERT INTO `power_dept` VALUES ('97', '总经办', '王成辉', '0', '51');
INSERT INTO `power_dept` VALUES ('98', 'boss', '崔炯丽', '0', '52');
INSERT INTO `power_dept` VALUES ('99', '总经办', '蔡日航', '0', '53');
INSERT INTO `power_dept` VALUES ('101', '总经办', '邱国栋', '0', '55');
