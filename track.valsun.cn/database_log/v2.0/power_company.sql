/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:07:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `power_company`
-- ----------------------------
DROP TABLE IF EXISTS `power_company`;
CREATE TABLE `power_company` (
  `company_id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '公司编号',
  `company_name` varchar(100) NOT NULL COMMENT '公司名称',
  `company_principal` varchar(20) DEFAULT NULL COMMENT '负责人',
  `company_address` varchar(100) DEFAULT NULL COMMENT '公司地址',
  `company_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `company_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除(1表示删除,0表示未删除)',
  `company_short_name` varchar(20) DEFAULT '0' COMMENT '公司中文简称',
  `company_pid` int(5) DEFAULT '0' COMMENT '母公司编号',
  `company_header_id` int(5) DEFAULT '0' COMMENT '总公司编号',
  `company_en_name` varchar(20) DEFAULT NULL COMMENT '公司英文名简称',
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of power_company
-- ----------------------------
INSERT INTO `power_company` VALUES ('1', '深圳市赛维网络科技有限公司', '陈文平', '', '', '0', '赛维网络', '0', '0', '');
INSERT INTO `power_company` VALUES ('2', '深圳市芬哲服饰有限公司', '陈文辉', '', '', '0', '芬哲服饰', '1', '0', '');
INSERT INTO `power_company` VALUES ('3', '华成云商技术有限公司', '陈', '', '', '0', '华成云商', '1', '0', '');
INSERT INTO `power_company` VALUES ('4', '深圳市哲果服饰有限公司', '陈', '', '', '0', '哲果服饰', '1', '0', '');
INSERT INTO `power_company` VALUES ('5', '深圳市运德物流有限公司', '陈', '', '', '0', '运德公司', '1', '0', '');
INSERT INTO `power_company` VALUES ('6', '深圳旭日高科光电有限公司', '陈', '', '', '0', 'LED', '1', '0', '');
INSERT INTO `power_company` VALUES ('7', '漳浦芬哲制衣有限公司', '陈', '', '', '0', '芬哲制衣', '1', '0', '');
INSERT INTO `power_company` VALUES ('8', '阿里巴巴', '马云', '', '', '0', '阿里巴巴', '0', '0', 'alibaba');
INSERT INTO `power_company` VALUES ('9', '泉州市邮政局电子商务局', '陈燕生', '泉州市丰泽区泉秀路邮政邮区中心局', '18005062222,15159884', '0', '泉州电子商务局', '0', '0', 'quanzhoudianzishangw');
INSERT INTO `power_company` VALUES ('10', '思科特科技（香港）有限公司', '刘双明', '深圳宝安龙华民治樟坑通博花园BC栋2楼', '13602549695,18926574', '0', '思科特科技', '0', '0', 'sikete');
INSERT INTO `power_company` VALUES ('11', '深圳美华电子商务有限公司', '王耀武', '宝安固戍南昌路上合南昌工业园B2栋南六楼', '13600163859', '0', '深圳美华', '0', '0', 'huamei');
INSERT INTO `power_company` VALUES ('12', '深圳市奥特斯电子科技有限公司', '常火文', '13714680989', '深圳市南山区西丽丽山路民企科技园一栋3楼', '0', '深圳奥特斯电子', '0', '0', 'aotesi');
INSERT INTO `power_company` VALUES ('13', '南京尼考克网络科技有限公司', '李鹏', '江苏省南京市秦淮区光华路四方新村7村13栋物业楼2楼', '15951031660,025-8423', '0', '南京尼考克', '0', '0', 'nikaoke');
INSERT INTO `power_company` VALUES ('14', '深圳艺凡数码科技有限责任公司', '王彬彬', '深圳市龙岗区南联路75号工厂三楼', '18902312701,0755-842', '0', '艺凡数码', '0', '0', 'yifan');
INSERT INTO `power_company` VALUES ('15', '天津倍利加科技', '王文斌', '天津市南开区华苑产业区兰苑路6号', '13116015539,13926872', '0', '倍利加', '0', '0', 'beilijia');
INSERT INTO `power_company` VALUES ('16', '深圳市有棵树科技有限公司', '容可', '深圳市龙岗区平湖华南城1号交易广场6楼A区', '13421346780,0755-895', '0', '有棵树科技', '0', '0', 'youkeshu');
INSERT INTO `power_company` VALUES ('17', '兰亭集势贸易（深圳）有限公司北京分公司', '郭去疾', '深圳市福田区泰然四路泰然工业区204栋西座6F', '010-84170020-8015,13', '0', '兰亭集势贸易', '0', '0', 'lantingjishi');
INSERT INTO `power_company` VALUES ('18', '深圳市浩然盈科通讯科技有限公司', '王燕', '深圳市南山区东滨路荔庭园B栋3单元205', '15692085187,18676755', '0', '浩然盈科通讯', '0', '0', 'haoranyingke');
INSERT INTO `power_company` VALUES ('19', '广州商铭软件有限公司', '杨志君', '广州市越秀区越华路112号珠江国际大厦3003', '15811820907', '0', '商铭', '0', '0', 'shangmin');
INSERT INTO `power_company` VALUES ('20', '深圳市傲基电子商务有限公司', '陆海传', '深圳市龙岗区平湖华南城电子交易中心P09-102', '13632628287,0755-336', '0', '傲基电子', '0', '0', 'aojidianzi');
INSERT INTO `power_company` VALUES ('21', '义乌仕芃贸易有限公司', '王旻昊', '浙江省义乌市西城路472-2', '15268611614,13801968', '0', '仕芃贸易', '0', '0', 'shifanmaoyi');
INSERT INTO `power_company` VALUES ('22', '深圳市润德维科技有限公司', '李红松', '深圳龙岗区坪西南路爽力工业园K栋三楼', '13751141164,13554928', '0', '润德维科技', '0', '0', 'rundewei');
INSERT INTO `power_company` VALUES ('23', '个人分销商', '无', '', '', '0', '个人分销商', '0', '0', 'geren');
INSERT INTO `power_company` VALUES ('24', '漳浦县邮政局', '王德财', '福建省漳浦县绥安镇龙泉路1-4号', '18698301869,15960626', '0', '漳浦邮政', '0', '0', 'youzheng');
INSERT INTO `power_company` VALUES ('25', '深圳市环球金贸电子商务有限公司', '蒋凌辉', '深圳市龙岗区坂田街道坂田社区五和大道黄君山区111号C栋201室', '18025345523,0755-845', '0', '环球金贸', '0', '0', 'huanqiujinmao');
INSERT INTO `power_company` VALUES ('26', '艾瑞尔外贸公司', '金基松', '', '13751187869', '0', '艾瑞尔外贸', '0', '0', 'ruiermao');
INSERT INTO `power_company` VALUES ('27', '上海育姿百货有限公司', '张鸣道', '', '18038099190', '0', '育姿百货', '0', '0', 'yuzi');
INSERT INTO `power_company` VALUES ('28', '深圳市凯飞亚电子有限公司', '赵思敏', '', '15817345148', '0', '凯飞亚电子', '0', '0', 'kaifeiya');
INSERT INTO `power_company` VALUES ('29', '上海贝速贸易有限公司', '管宗斌', '', '13917145001', '0', '贝速贸易', '0', '0', 'gogomg');
INSERT INTO `power_company` VALUES ('30', '深圳市爱淘城网络科技有限公司', '刘雨燕', '', '15989329963', '0', '深圳市爱淘城网络科技有限公司', '0', '0', 'aitaocheng');
INSERT INTO `power_company` VALUES ('31', '上海贝速贸易有限公司22222', '管宗斌', '上海', '13917145001', '1', '上海贝速贸易有限公司22222', '0', '0', 'beisu');
INSERT INTO `power_company` VALUES ('35', '1', '1', '1', '1', '1', '1', '0', '0', '1');
INSERT INTO `power_company` VALUES ('32', '中国邮政', '杨福华', '福建', '13606989696', '0', '【物流系统】中国邮政开放业务', '0', '0', 'chinapost');
INSERT INTO `power_company` VALUES ('33', 'test', 'test', 'test', 'test', '1', 'test', '0', '0', 'test');
INSERT INTO `power_company` VALUES ('34', 'test2', 'test2', 'test2', 'test2', '1', 'test2', '0', '0', 'test2');
INSERT INTO `power_company` VALUES ('36', '深圳市通拓科技有限公司', '廖新辉', '广东省深圳市龙岗区平湖华南城1号交易广场五楼G区', '0755-83998906', '0', '通拓科技', '0', '0', 'tomtop');
INSERT INTO `power_company` VALUES ('37', '福建省邮政公司厦门分公司', '蔡聪明', '福建省厦门市湖滨南路6号电子商务局', '15959288290', '0', '福建省邮政公司厦门分公司', '0', '0', 'fujianyouzhengfengon');
INSERT INTO `power_company` VALUES ('38', '王佳个人分销商', '王佳', '东莞南城', '18666883393', '0', '王佳个人分销商', '0', '0', 'wangjia');
INSERT INTO `power_company` VALUES ('39', '东莞市汇邮电子商务有限公司', '李凯', '东莞市东城区下桥邮政大楼9楼', '13662863517', '0', '东莞市汇邮电子商务有限公司', '0', '0', 'huiyoudianzi');
INSERT INTO `power_company` VALUES ('40', 'dresslinkdistributor', '陈凯丹', 'dresslinkdistributor', '13530294833', '0', 'dresslink', '0', '0', 'dresslink');
INSERT INTO `power_company` VALUES ('41', '辛小芳个人分销商', '辛小芳', '福州市台江区茶亭街五一中路132号民航广场2#1120', '18650708532', '0', '辛小芳个人分销商', '0', '0', 'xinxiaofang trade');
INSERT INTO `power_company` VALUES ('42', '漳州邮政EMS国际部', '蒋婧雯', '福建省漳州市芗城区元光北路46号', '15959692152', '0', '漳州邮政EMS国际部', '0', '0', 'zhangzhouEMS');
INSERT INTO `power_company` VALUES ('43', '深圳易联软件有限公司', '杨志君', '宝安区宝源路宝盛工业区宿舍楼B703', '15811820907', '0', '易联软件', '0', '0', 'yilian');
INSERT INTO `power_company` VALUES ('44', '深圳市缤购科技有限公司', '李超红', '深圳市福田区华发北路桑达雅苑25D', '18601254810', '0', '深圳市缤购科技有限公司', '0', '0', 'bingou');
INSERT INTO `power_company` VALUES ('45', '福建省泉邮信息技术公司', '陈颖颖', '福建省泉州丰泽区西贤路西郊邮政支局四楼泉邮信息技术公司', '13959816868', '0', '福建省泉邮信息技术公司', '0', '0', 'quanyouxinxi');
INSERT INTO `power_company` VALUES ('46', '寻佑兰个人分销商', '寻佑兰', '深圳市龙岗区新生田祖上老3巷3号', '18390098933', '0', '寻佑兰个人分销商', '0', '0', 'xunyoulanfenxiaoshan');
INSERT INTO `power_company` VALUES ('47', '深圳市万方网络信息有限公司', '李健文', '深圳龙岗区坂田雪岗北路16号威宇隆工业园B栋3-4楼', '18906006656', '0', '万方网络', '0', '0', 'wanfangwangluo');
INSERT INTO `power_company` VALUES ('48', '林益池(个人)', '林益池', '江苏省 苏州市 昆山市 周市镇横泾路468号自由都市7号楼1105室', '13773190416', '0', '林益池(个人)', '0', '0', '林益池(个人)');
INSERT INTO `power_company` VALUES ('49', 'soho', '丰满', '南山沙河西路白沙物流园', '15072817261', '0', 'soho', '0', '0', 'soho');
INSERT INTO `power_company` VALUES ('50', '广州市贝易信息科技有限公司', '邓秀丽', '广东省广州市', '13530313902', '0', '广州市贝易信息科技有限公司', '0', '0', 'beiyi');
INSERT INTO `power_company` VALUES ('51', '泉港邮政局', '王成辉', '福建省泉州市泉港区南北五路邮政大楼4楼', '18959728881', '0', '泉港邮政', '0', '0', 'quangangyouzheng');
INSERT INTO `power_company` VALUES ('52', '李爱林(个人)', '李爱林', '----------------', '13444444444', '0', '李爱林(个人)', '0', '0', '李爱林(个人)');
INSERT INTO `power_company` VALUES ('53', '托尼有限公司（香港）', '蔡日航', '广州市海珠区上渡路祥韵雅居C栋801', '13533049324', '0', '托尼有限公司', '0', '0', 'tuoni');
INSERT INTO `power_company` VALUES ('55', '香港睿铭信息科技有限公司', '邱国栋', '广州市海珠区上渡路215号C204', '13719463029', '0', '香港睿铭信息科技有限公司', '0', '0', 'ruiming');
