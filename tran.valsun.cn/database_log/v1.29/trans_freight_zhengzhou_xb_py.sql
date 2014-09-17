/*
Navicat MySQL Data Transfer

Source Server         : 198host
Source Server Version : 50529
Source Host           : 192.168.200.198:3306
Source Database       : valsun_tran

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2014-04-29 20:11:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `trans_freight_zhengzhou_xb_py`
-- ----------------------------
DROP TABLE IF EXISTS `trans_freight_zhengzhou_xb_py`;
CREATE TABLE `trans_freight_zhengzhou_xb_py` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unitPrice` decimal(10,4) DEFAULT NULL COMMENT '单价',
  `countries` text COMMENT '国家列表',
  `name` varchar(50) DEFAULT NULL,
  `handlefee` decimal(10,2) DEFAULT '0.00' COMMENT '手续费',
  `discount` decimal(10,2) DEFAULT NULL COMMENT '折扣',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除，1删除',
  `addTime` int(10) DEFAULT '0' COMMENT '添加时间',
  `editTime` int(10) DEFAULT '0' COMMENT '修改时间',
  `add_user_id` int(10) DEFAULT '0' COMMENT '添加人ID',
  `edit_user_id` int(10) DEFAULT '0' COMMENT '修改人ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trans_freight_zhengzhou_xb_py
-- ----------------------------
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('11', '105.0000', 'Andorra,Albania,Algeria,Afghanistan,Angola,Argentina,Egypt,United Arab Emirates,Ethiopia,Ascension Island,Papua New Guinea,Botswana,Bhutan,Iceland,Burkina Faso,Bahrain,Burundi,French Polynesia,Benin,Panama,Brazil,Equatorial Guinea,DEMOCRATIC REPUBLIC OF TIMOR-LESTE,Togo,Ecuador,The Commonwealth of eritrea,Vatican City State,Cape Verde Islands,Fiji,the Faroe Islands,Cuba,Republic of,Gambia,Guam,Congo, Republic of the,Congo, Democratic Republic of the,Colombia,Georgia,Montenegro,Zimbabwe,Djibouti,Kiribati,Ghana,Canary Islands,Guinea,Guinea-Bissau,Gabon Republic,Cambodia,Cook Islands,Comoros,Cameroon,Kenya,Cote d Ivoire Ivory Coast,Kuwait,Liberia,Lebanon,Libya,Reunion,Lesotho,Laos,Rwanda,Liechtenstein,Burma,Madagascar,Maldives,Moldova,Bangladesh,Micronesia,\nPeru,Mali,Morocco,Mauritius,Mauritania,Malawi,\nTheNorthernMarianaIslands,Monaco,Macedonia,Mozambique,Marshall Islands,Mexico,Mayotte,Niue,Nepal,South Africa,Nauru,Namibia,South Georgia and the South Sandwich Islands,Niger,Nigeria,Palau,Pitcairn Islands Group,Norfolk Island,Sudan,Christmas Island,Sao Tome and Principe ST,Serbia,Saint Helena,Saint Kitts-Nevis,Sierra Leone,Solomon Islands,Suriname,Somalia,San Marino,American Samoa,Senegal,Seychelles,Swaziland,Tonga,Tokelau,Tunisia,Tanzania,Tuvalu,Uganda,BruneiDarussalam,Wallis and Futuna,Vanuatu,Venezuela,New Caledonia,Western Sahara,Western Samoa,Jordan,Iran,Iraq,Yemen,Gibraltar,Zambia,Chad,Central African Republic,Svalbard and Jan Mayen,French Southern Territories,Brasil,Brunei Darussalam,Brasilien', '第五组', '0.00', '0.85', '1', '0', '1393928420', '0', '153');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('12', '120.0000', 'Anguilla,Aruba,Antigua and Barbuda,Barbados,Puerto Rico,Bahamas,Paraguay,Bolivia,Belize,Bermuda,Dominica,FalklandIslandsMalvinas,French Guiana,Guadeloupe,Greenland,Grenada,Costa Rica,Guyana,Haiti,Honduras,Cayman Islands,Virgin Islands,Martinique,Montserrat,Nicaragua,El Salvador,SaintLucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Turks and Caicos Islands,Trinidad and Tobago,Guatemala,Uruguay,Jamaica,British Virgin Islands U.S.,Chile,Zaire,Netherlands Antilles,Dominican Republic,Niederlande,Pays-Bas', '第六组', '0.00', '0.85', '1', '0', '1393928370', '0', '153');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('14', '62.0000', 'Japan,', '第一组', '0.00', '0.85', '1', '0', '1393928360', '0', '153');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('15', '68.0000', 'Austria,Bulgaria,Korea, South,Croatia,Croatia,Republic of,Malaysia,Slovakia,Thailand,Singapore,Hungary,India,Indonesia,Croatia, Republic of,Slovakia Slovak Republic,Croatia local name: Hrvatska,Österreich,Slowakei,Ungarn,Autriche,Slovaquie,Hongrie,Croatie,Kroatien', '第二组', '0.00', '0.85', '1', '0', '1393928340', '0', '153');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('16', '75.0000', 'Australia,Ireland,Poland,Belgium,Germany,Denmark,Finland,Netherlands,NetherlandsHolland,Holland,Czech Republic,Norway,Portugal,Sweden,Switzerland,Greece,Italy,United Kingdom,Israel,Guernsey,Belgique,	Deutschland,Griechenland,Belgien,Schweiz,Suisse,Dänemark,Finnland,Tschechische Republik,Sweden,Polen,Schweden,Finlande,Irlanda,Svizzera,République tchèque,Allemagne,Suiza,Países Bajos,Grèce,Suède,Alemania,Repubblica Ceca,Italien,Dinamarca,Deutschland,Italia,Norwegen,Großbritannien', '第三组', '0.00', '0.85', '1', '0', '1393928351', '0', '153');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('17', '102.5000', 'Russian Federation,Russische Föderation,Russia', '第四组', '0.00', '0.85', '0', '0', '1394028014', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('18', '83.0000', 'Croatia,Croatia Republic of,Hungary,Croatia local name: Hrvatska,Österreich,Ungarn,Croatie,Kroatien,Hungary,Australia,Germany,Netherlands,NetherlandsHolland,Holland,Norway,Sweden,United Kingdom,Israel,', '第二组', '0.00', '0.85', '0', '0', '1394030210', '0', '315');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('20', '123.0000', '123,123,123', '测试分组', '12.00', '0.00', '1', '1384842588', '1384842899', '71', '71');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('21', '85.0000', 'Oman,Azerbaijan Republic,Estonia,Belarus,Bosnia and Herzegovina,Pakistan,Korea, North,France,Philippines,Kazakhstan,Kyrgyzstan,Canada,Qatar,Romania,Luxembourg,,Lithuania,Latvia,Malta,Mongolia,United States,USA,Sri Lanka,Slovenia,Cyprus,Saudi Arabia,Turkey,Tajikistan,Turkmenistan,Ukraine,Uzbekistan,Spain,Syria,New Zealand,Armenia,Vietnam,Palestine,APO/FPO,France métropolitaine,España,Slowenien,Luxemburg,Spanien,Espagne,Estland,Frankreich,Russie,Zypern,Lettland,Kasachstan,Rumänien,Saudi Arabien,Україна,Украина,Republic of Belarus', '第四区', '0.00', '0.85', '0', '1394009932', '0', '315', '0');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('22', '75.0000', 'Ireland,Poland,Belgium,Denmark,Finland,Czech Republic,Portugal,Switzerland,Greece,Italy,Guernsey,Belgique,	Deutschland,Griechenland,Belgien,Schweiz,Suisse,Dänemark,Finnland,Tschechische Republik,Polen,Schweden,Finlande,Irlanda,Svizzera,République tchèque,Allemagne,Suiza,Países Bajos,Grèce,Suède,Alemania,Repubblica Ceca,Italien,Dinamarca,Deutschland,Italia,Norwegen,Großbritannien', '第三区', '0.00', '0.85', '0', '1394009981', '1394027899', '315', '71');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('23', '68.0000', 'Austria,Bulgaria,Korea, South,Malaysia,Slovakia,Thailand,Singapore,India,Indonesia,Slovakia Slovak Republic,Slowakei,Autriche,Slovaquie,', '第二区', '0.00', '0.85', '0', '1394010097', '0', '315', '0');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('24', '62.0000', 'Japan,', '第一区', '0.00', '0.85', '0', '1394010135', '0', '315', '0');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('25', '105.0000', 'Andorra,Albania,Algeria,Afghanistan,Angola,Argentina,Egypt,United Arab Emirates,Ethiopia,Ascension Island,Papua New Guinea,Botswana,Bhutan,Iceland,Burkina Faso,Bahrain,Burundi,French Polynesia,Benin,Panama,Brazil,Equatorial Guinea,DEMOCRATIC REPUBLIC OF TIMOR-LESTE,Togo,Ecuador,The Commonwealth of eritrea,Vatican City State,Cape Verde Islands,Fiji,the Faroe Islands,Cuba,Republic of,Gambia,Guam,Congo, Republic of the,Congo, Democratic Republic of the,Colombia,Georgia,Montenegro,Zimbabwe,Djibouti,Kiribati,Ghana,Canary Islands,Guinea,Guinea-Bissau,Gabon Republic,Cambodia,Cook Islands,Comoros,Cameroon,Kenya,Cote d Ivoire Ivory Coast,Kuwait,Liberia,Lebanon,Libya,Reunion,Lesotho,Laos,Rwanda,Liechtenstein,Burma,Madagascar,Maldives,Moldova,Bangladesh,Micronesia,\nPeru,Mali,Morocco,Mauritius,Mauritania,Malawi,\nTheNorthernMarianaIslands,Monaco,Macedonia,Mozambique,Marshall Islands,Mexico,Mayotte,Niue,Nepal,South Africa,Nauru,Namibia,South Georgia and the South Sandwich Islands,Niger,Nigeria,Palau,Pitcairn Islands Group,Norfolk Island,Sudan,Christmas Island,Sao Tome and Principe ST,Serbia,Saint Helena,Saint Kitts-Nevis,Sierra Leone,Solomon Islands,Suriname,Somalia,San Marino,American Samoa,Senegal,Seychelles,Swaziland,Tonga,Tokelau,Tunisia,Tanzania,Tuvalu,Uganda,BruneiDarussalam,Wallis and Futuna,Vanuatu,Venezuela,New Caledonia,Western Sahara,Western Samoa,Jordan,Iran,Iraq,Yemen,Gibraltar,Zambia,Chad,Central African Republic,Svalbard and Jan Mayen,French Southern Territories,Brasil,Brunei Darussalam,Brasilien,Îles du Cap Vert', '第五区', '0.00', '0.85', '0', '1394010181', '0', '315', '0');
INSERT INTO `trans_freight_zhengzhou_xb_py` VALUES ('26', '120.0000', 'Anguilla,Aruba,Antigua and Barbuda,Barbados,Puerto Rico,Bahamas,Paraguay,Bolivia,Belize,Bermuda,Dominica,FalklandIslandsMalvinas,French Guiana,Guadeloupe,Greenland,Grenada,Costa Rica,Guyana,Haiti,Honduras,Cayman Islands,Virgin Islands,Martinique,Montserrat,Nicaragua,El Salvador,SaintLucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Turks and Caicos Islands,Trinidad and Tobago,Guatemala,Uruguay,Jamaica,British Virgin Islands U.S.,Chile,Zaire,Netherlands Antilles,Dominican Republic,Niederlande,Pays-Bas,Virgin Islands U.S.,Saint Lucia,', '第六区', '0.00', '0.85', '0', '1394010223', '0', '315', '0');
