ALTER TABLE `trans_email_stat` ADD `nodeId` int(10) DEFAULT '0' COMMENT '����ڵ�ID';
ALTER TABLE `trans_email_stat` ADD `typeId` tinyint(2) DEFAULT '0' COMMENT '����ID��0mail��1MES';
ALTER TABLE `trans_email_stat` ADD INDEX `addTime` (`addTime`) USING BTREE;
ALTER TABLE `trans_email_stat` ADD INDEX `lastTime` (`lastTime`) USING BTREE;