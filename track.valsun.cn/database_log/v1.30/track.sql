ALTER TABLE  `tracks_access_statistics` DROP INDEX  `ip`;
ALTER TABLE  `tracks_access_statistics` DROP INDEX  `id`;
ALTER TABLE  `tracks_access_statistics` ADD `ipNum` bigint(12) DEFAULT '0' COMMENT 'IP数值';	
UPDATE `tracks_access_statistics` SET ipNum = INET_ATON(ip);
ALTER TABLE  `tracks_access_statistics` CHANGE  `ip`  `ip` CHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '127.0.0.1' COMMENT 'IP地址';
ALTER TABLE  `tracks_access_statistics` ADD INDEX `ipNum` (`ipNum`) USING BTREE;
OPTIMIZE TABLE `tracks_access_statistics`;
