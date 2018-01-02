
DROP TABLE IF EXISTS `t_item_new`;
CREATE TABLE `t_item_new` (
`f_itemid`                BIGINT NOT NULL AUTO_INCREMENT,
`f_itemtype`              VARCHAR(8) NOT NULL DEFAULT '0',
`f_uid`                   VARCHAR(128) NOT NULL DEFAULT '',
`f_main_type_id`          INT NOT NULL DEFAULT '0',
`f_sub_type_id`           INT NOT NULL DEFAULT '0',
`f_textarea`              VARCHAR(512)  NOT NULL DEFAULT '',
`f_start_date`            VARCHAR(16) NOT NULL DEFAULT '',
`f_finish_date`           VARCHAR(16) NOT NULL DEFAULT '',
`f_latitude`              DOUBLE NOT NULL DEFAULT '0',
`f_longitude`             DOUBLE NOT NULL DEFAULT '0',
`f_location`              VARCHAR(32) NOT NULL DEFAULT '0',
`f_focus`                 VARCHAR(64) NOT NULL DEFAULT '0',
`f_created`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`f_updated`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`f_itemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `t_image`;
CREATE TABLE `t_image` (
`f_imageid`               BIGINT NOT NULL AUTO_INCREMENT,
`f_itemid`                VARCHAR(32) NOT NULL DEFAULT '',
`f_uid`                   VARCHAR(32) NOT NULL DEFAULT '',
`f_imagename`             VARCHAR(48) NOT NULL DEFAULT '',
`f_index`                 INT NOT NULL DEFAULT '0',
`f_created`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`f_updated`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`f_imageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;