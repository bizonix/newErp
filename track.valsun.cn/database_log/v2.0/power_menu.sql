/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_track

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-08-05 11:07:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `power_menu`
-- ----------------------------
DROP TABLE IF EXISTS `power_menu`;
CREATE TABLE `power_menu` (
  `menu_id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单编号',
  `menu_name` varchar(20) NOT NULL COMMENT '菜单名称',
  `menu_url` varchar(100) NOT NULL COMMENT '菜单对应的url路径地址',
  `menu_system_id` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '菜单所属系统编号对应表power_system',
  `menu_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被删除(1表示删除,0表示未删除)',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='菜单管理表';

-- ----------------------------
-- Records of power_menu
-- ----------------------------
INSERT INTO `power_menu` VALUES ('1', '分系统用户管理', 'user.php', '1', '0');
INSERT INTO `power_menu` VALUES ('2', '菜单管理', 'menu.php', '1', '0');
INSERT INTO `power_menu` VALUES ('3', '部门管理', 'dept.php', '1', '0');
INSERT INTO `power_menu` VALUES ('4', '接口系统管理', 'system.php', '1', '0');
INSERT INTO `power_menu` VALUES ('5', 'Action管理', 'action.php', '1', '0');
INSERT INTO `power_menu` VALUES ('6', 'SESSION管理', 'session.php', '1', '0');
INSERT INTO `power_menu` VALUES ('7', 'ActionGroup管理', 'actionGroup.php', '1', '0');
INSERT INTO `power_menu` VALUES ('8', '岗位权限管理', 'jobpower.php', '1', '0');
INSERT INTO `power_menu` VALUES ('9', '岗位管理', 'job.php', '1', '0');
INSERT INTO `power_menu` VALUES ('10', '水印管理', 'water.php', '2', '0');
INSERT INTO `power_menu` VALUES ('11', '图片管理', 'pictures.php', '2', '0');
INSERT INTO `power_menu` VALUES ('12', 'test', '', '1', '1');
INSERT INTO `power_menu` VALUES ('13', '用户管理', 'http://123.com', '2', '1');
INSERT INTO `power_menu` VALUES ('14', '范本', 'tp.php', '3', '0');
INSERT INTO `power_menu` VALUES ('15', '标题管理', 'spuListingTitle.php', '3', '0');
INSERT INTO `power_menu` VALUES ('16', '刊登', 'listing.php', '3', '0');
INSERT INTO `power_menu` VALUES ('17', 'test', 'stockIndex.php', '1', '1');
INSERT INTO `power_menu` VALUES ('18', '公司管理', 'company.php', '1', '0');
INSERT INTO `power_menu` VALUES ('19', 'test', 'manageUser.php', '1', '1');
INSERT INTO `power_menu` VALUES ('20', '发货管理菜单', 'saleOrderIndex.php', '6', '0');
INSERT INTO `power_menu` VALUES ('21', '库存管理菜单', 'good.php', '6', '0');
INSERT INTO `power_menu` VALUES ('22', 'test', 'stockIndex.php', '1', '1');
INSERT INTO `power_menu` VALUES ('23', 'test', 'manageUser.php', '1', '1');
INSERT INTO `power_menu` VALUES ('24', '备货单管理菜单', 'stockInvoice.php', '6', '0');
INSERT INTO `power_menu` VALUES ('25', '系统设置菜单', 'manageUser.php', '6', '0');
INSERT INTO `power_menu` VALUES ('26', '站点账号', 'accountJoinSite.php', '3', '0');
INSERT INTO `power_menu` VALUES ('27', '类目映射', 'erpCategoryToEbayStoreCategory.php', '3', '0');
INSERT INTO `power_menu` VALUES ('28', '店铺模板', 'templetList.php', '3', '0');
INSERT INTO `power_menu` VALUES ('29', '货币汇率维护', 'currency.php', '3', '0');
INSERT INTO `power_menu` VALUES ('30', '速卖通入口', '../aliex/index.php', '3', '1');
INSERT INTO `power_menu` VALUES ('31', '货品资料', 'index.php?mod=goods', '11', '0');
INSERT INTO `power_menu` VALUES ('32', '统一用户管理', 'globalUser.php', '1', '0');
INSERT INTO `power_menu` VALUES ('33', '账户管理', 'accountEdit.php', '3', '0');
INSERT INTO `power_menu` VALUES ('34', '运输方式管理', '', '8', '0');
INSERT INTO `power_menu` VALUES ('35', '产品信息', 'index.php?mod=goods&act=getGoodsList', '7', '0');
INSERT INTO `power_menu` VALUES ('36', 'SPU管理', 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList', '7', '0');
INSERT INTO `power_menu` VALUES ('37', '包材管理', 'index.php?mod=packingMaterials&act=getPmList', '7', '0');
INSERT INTO `power_menu` VALUES ('38', '类别管理', 'index.php?mod=category&act=getCategoryList', '7', '0');
INSERT INTO `power_menu` VALUES ('39', '系统设置', 'index.php?mod=user&act=index', '7', '0');
INSERT INTO `power_menu` VALUES ('40', '检测领取', 'index.php?mod=Iqc&act=iqcList', '15', '0');
INSERT INTO `power_menu` VALUES ('41', 'QC检测', 'index.php?mod=IqcDetect&act=iqcScan', '15', '0');
INSERT INTO `power_menu` VALUES ('42', 'QC库', 'index.php?mod=DefectiveProducts&act=getDefectiveProductsList', '15', '0');
INSERT INTO `power_menu` VALUES ('43', 'QC检测标准', 'index.php?mod=NowSampleStandard&act=nowSampleType', '15', '0');
INSERT INTO `power_menu` VALUES ('44', '系统管理', 'index.php?mod=user&act=index', '15', '0');
INSERT INTO `power_menu` VALUES ('45', '名称管理', 'index.php?mod=nameSystem&act=nameSystemList', '16', '0');
INSERT INTO `power_menu` VALUES ('46', '系统名称管理', 'index.php?mod=systemManage&act=systemManageList', '16', '0');
INSERT INTO `power_menu` VALUES ('47', '名称分类管理', 'index.php?mod=nameTypeManage&act=nameTypeManageList', '16', '0');
INSERT INTO `power_menu` VALUES ('48', '产品制作管理', 'index.php?mod=products&act=getProductsComfirmList', '7', '0');
INSERT INTO `power_menu` VALUES ('49', '定价搜索', '/index.php?mod=PricingSearch&act=index', '17', '0');
INSERT INTO `power_menu` VALUES ('50', '费用设置', '/index.php?mod=ExchangeLoss&act=index', '17', '0');
INSERT INTO `power_menu` VALUES ('51', 'Listing', '', '4', '1');
INSERT INTO `power_menu` VALUES ('52', '分销商管理', '', '4', '1');
INSERT INTO `power_menu` VALUES ('53', '系统设置', '/index.php?mod=Warehouse&act=index', '17', '0');
INSERT INTO `power_menu` VALUES ('54', '分销商管理', '/index.php?mod=costDistributor&act=costDistributorList', '17', '0');
INSERT INTO `power_menu` VALUES ('55', 'Listing', '/index.php?mod=listing&act=listingList', '17', '0');
INSERT INTO `power_menu` VALUES ('56', 'API申请审核', '/index.php?mod=apiApplyAudit&act=index', '19', '0');
INSERT INTO `power_menu` VALUES ('58', '开发者管理', '/index.php?mod=backgroundManage&act=index', '19', '0');
INSERT INTO `power_menu` VALUES ('60', 'Listing管理', '/index.php?mod=Listing&act=index', '20', '0');
INSERT INTO `power_menu` VALUES ('61', 'Listing变更记录', '/index.php?mod=Listing&act=updateLog', '20', '0');
INSERT INTO `power_menu` VALUES ('62', '国家简码管理', '/index.php?mod=CountryCode&act=index', '19', '0');
INSERT INTO `power_menu` VALUES ('63', 'Excel导入', 'http://pc.valsun.cn/index.php?mod=excelImport&act=welcomeExcelImport', '7', '0');
INSERT INTO `power_menu` VALUES ('64', '报表导出', 'http://pc.valsun.cn/index.php?mod=excelOutPut&act=welcomeExcelOutPut', '7', '0');
INSERT INTO `power_menu` VALUES ('65', '待刊登列表', 'awaitPublished.php', '3', '0');
INSERT INTO `power_menu` VALUES ('66', '刊登结果', 'ebay_queue.php', '3', '0');
INSERT INTO `power_menu` VALUES ('57', 'API管理', '/index.php?mod=apiManage&act=index', '19', '0');
INSERT INTO `power_menu` VALUES ('59', '系统管理', '/index.php?mod=admin&act=index', '19', '0');
INSERT INTO `power_menu` VALUES ('67', '用户系统开放配置', '/index.php?mod=userOpenSystemConfig&act=index', '19', '0');
