ALTER TABLE `trans_track_number` ADD INDEX `scancarrier` (`carrierId`,`scanTime`) USING BTREE;
ALTER TABLE `trans_track_number` ADD INDEX `platAccount` (`platAccount`) USING BTREE;
ALTER TABLE `trans_track_number` ADD INDEX `delscan` (`is_delete`,`scanTime`) USING BTREE;