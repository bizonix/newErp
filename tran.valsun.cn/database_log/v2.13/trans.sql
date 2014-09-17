ALTER TABLE `trans_email_stat` ADD `nodeId` int(10) DEFAULT '0' COMMENT '处理节点ID';
ALTER TABLE `trans_email_stat` ADD `typeId` tinyint(2) DEFAULT '0' COMMENT '类型ID，0mail，1MES';
ALTER TABLE `trans_email_stat` ADD INDEX `addTime` (`addTime`) USING BTREE;
ALTER TABLE `trans_email_stat` ADD INDEX `lastTime` (`lastTime`) USING BTREE;