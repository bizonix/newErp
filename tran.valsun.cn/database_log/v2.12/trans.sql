ALTER TABLE `trans_track_numbers` ADD `channelId` int(10) DEFAULT '0' COMMENT '����ID';
ALTER TABLE `trans_track_numbers` ADD INDEX `channelId` (`channelId`) USING BTREE;