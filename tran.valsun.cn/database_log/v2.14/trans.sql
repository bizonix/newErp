ALTER TABLE `trans_carrier` ADD `carrierAbb` char(10) DEFAULT '' COMMENT '运输方式简码';
ALTER TABLE `trans_carrier` ADD INDEX `carrierAbb` (`carrierAbb`) USING BTREE;
ALTER TABLE `trans_carrier` ADD `carrierIndex` char(1) DEFAULT '' COMMENT '字母索引';
ALTER TABLE `trans_carrier` ADD INDEX `carrierIndex` (`carrierIndex`) USING BTREE;
ALTER TABLE `trans_carrier` ADD `carrierLogo` VARCHAR(100) DEFAULT '' COMMENT '运输方式LOGO';