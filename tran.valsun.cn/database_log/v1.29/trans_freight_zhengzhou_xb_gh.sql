/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-29 20:11:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_zhengzhou_xb_gh`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_zhengzhou_xb_gh`;
CREATE TABLE `trans_freight_zhengzhou_xb_gh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unitPrice` decimal(10,3) DEFAULT NULL COMMENT '单价',
  `countries` text COMMENT '国家列表',
  `groupName` varchar(24) DEFAULT NULL COMMENT '分区名称',
  `handlefee` decimal(10,3) DEFAULT '0.000' COMMENT '手续费',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '默认0不删除，1删除',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='中国邮政挂号福建渠道价目明细表';

-- ----------------------------
-- Records of trans_freight_zhengzhou_xb_gh
-- ----------------------------
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('9', '90.500', 'Oman,Azerbaijan Republic,Estonia,Belarus,Bosnia and Herzegovina,Pakistan,Korea, North,France,Philippines,Kazakhstan,Kyrgyzstan,Canada,Qatar,Romania,Luxembourg,Lithuania,Latvia,Malta,Mongolia,United States,Sri Lanka,Slovenia,Cyprus,Saudi Arabia,Tajikistan,Turkmenistan,Ukraine,Uzbekistan,Spain,Syria,United Kingdom,Armenia,Vietnam,Palestine,USA,APO/FPO,Guernsey,España,France métropolitaine,Saudi Arabien,Украина,Україна,England', '第五区', '8.000', '1383136127', '1394013433', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('10', '105.000', 'South Africa,', '第六区', '8.000', '1383136127', '1394013401', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('11', '110.000', 'Argentina,Brazil,Mexico,Brasil,', '第七区', '8.000', '1383136127', '1394013392', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('12', '62.000', 'Japan,', '第一区', '8.000', '1383136127', '1394013380', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('13', '71.500', 'Korea, South,Malaysia,Thailand,Singapore,India,Indonesia,South Korea,', '第二区', '8.000', '1383136127', '1394013370', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('14', '81.000', 'Austria,Australia,Ireland,Bulgaria,Poland,Belgium,Germany,Denmark,Finland,Netherlands,Czech Republic,Croatia,Croatia,Republic of,Norway,Portugal,Sweden,Switzerland,Slovakia,Greece,Hungary,Italy,Israel,Croatia, Republic of,NetherlandsHolland,Holland,Belgique,Slovakia Slovak Republic,Croatia local name: Hrvatska,Deutschland,Schweiz,Allemagne,Germania,République tchèque,Alemania,Autriche,Österreich,Kroatien', '第三区', '8.000', '1383136127', '1394013349', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('15', '85.000', 'Turkey,New Zealand,', '第四区', '8.000', '1383136127', '1394013321', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('16', '120.000', 'Afghanistan,United Arab Emirates,Bhutan,Bahrain,DEMOCRATIC REPUBLIC OF TIMOR-LESTE,Cambodia,Kuwait,Lebanon,Laos,Burma,Maldives,Bangladesh,Peru,Nepal,Brunei Darussalam,Jordan,Iran,Iraq,Yemen,Chile,', '第八区', '8.000', '1383136127', '1394013313', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('17', '147.500', 'Andorra,Albania,Iceland,Vatican City State,the Faroe Islands,Georgia,Montenegro,Liechtenstein,Moldova,Monaco,Macedonia,Serbia,San Marino,Gibraltar,', '第九区', '8.000', '1383136127', '1394013290', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('18', '176.000', 'Algeria,Angola,Egypt,Anguilla,Aruba,Ethiopia,Ascension Island,Antigua and Barbuda,Barbados,Papua New Guinea,Botswana,Puerto Rico,Bahamas,Burkina Faso,Burundi,Paraguay,French Polynesia,Bolivia,Belize,Bermuda,Benin,Panama,EquatorialGuinea,Togo,Dominica,Ecuador,\nThe Commonwealth of eritrea,Cape Verde Islands,Fiji,Falkland IslandsMalvinas,French Guiana,Cuba, Republic of,Gambia,Guam,Guadeloupe,Congo, Republic of the,Congo, Democratic Republic of the,Colombia,Greenland,Grenada,Costa Rica,Guyana,Haiti,Honduras,Zimbabwe,Djibouti,Kiribati,Ghana,Canary Islands,Guinea,Guinea-Bissau,Gabon Republic,Cook Islands,Comoros,Cameroon,Cayman Islands,Kenya,Cote d Ivoire Ivory Coast,Liberia,Libya,Reunion,Lesotho,Rwanda,Madagascar,Micronesia,Mali,Morocco,Mauritius,Mauritania,Malawi,TheNorthernMarianaIslands,Mozambique,Marshall Islands,Virgin Islands,Martinique,Montserrat,Mayotte,Niue,Nicaragua,Nauru,Namibia,South Georgia and the South Sandwich Islands,Niger,Nigeria,Palau,Pitcairn Islands Group,\nNorfolk Island,Sudan,Christmas Island,Sao Tome and Principe ST,El Salvador,Saint Helena,Saint Kitts-Nevis,Sierra Leone,Solomon Islands,Suriname,Saint Lucia,Somalia,American Samoa,Senegal,Saint Pierre and Miquelon,Seychelles,Swaziland,Saint Vincent and the Grenadines,Tonga,Tokelau,Turks and Caicos Islands,Trinidad and Tobago,Tunisia,Tanzania,Tuvalu,Guatemala,Uganda,Uruguay,Wallis and Futuna,Vanuatu,Venezuela,New Caledonia,Western Sahara,Western Samoa,Jamaica,British Virgin Islands U.S.,Zambia,Chad,Central African Republic,Zaire,Svalbard and Jan Mayen,French Southern Territories,Dominican Republic,Îles du Cap Vert,Virgin Islands U.S.,Saint Lucia,Netherlands Antilles', '第十区', '8.000', '1383136127', '1394013273', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('19', '96.300', 'Russian Federation,Russische Föderation', '第十一区', '8.000', '1383136127', '1394013203', '0', '0.85', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_gh` VALUES ('20', '12.400', 'aaa', '测试分组', '12.000', '1384849246', '0', '1', '0.90', '71', '0');
