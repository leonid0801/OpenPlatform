
DROP TABLE IF EXISTS `t_client`;
CREATE TABLE `t_client` (
`f_itemid`             BIGINT NOT NULL AUTO_INCREMENT,
`f_openid`         VARCHAR(128) NOT NULL DEFAULT '',
`f_session_key`    VARCHAR(128) NOT NULL DEFAULT '',
`f_expires_in`     INT NOT NULL DEFAULT '0',
`f_client_session` VARCHAR(16) NOT NULL DEFAULT '',
`f_valid`          TINYINT NOT NULL DEFAULT '0',
`f_created`        DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`f_updated`        DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `t_item`;
CREATE TABLE `t_item` (
`f_itemid`                BIGINT NOT NULL AUTO_INCREMENT,
`f_uid`                   VARCHAR(128) NOT NULL DEFAULT '',
`f_main_type_id`          INT NOT NULL DEFAULT '0',
`f_sub_type_id`           INT NOT NULL DEFAULT '0',
`f_textarea`              VARCHAR(1024)  NOT NULL DEFAULT '',
`f_start_date`            VARCHAR(16) NOT NULL DEFAULT '',
`f_finish_date`           VARCHAR(16) NOT NULL DEFAULT '',
`f_latitude`              DOUBLE NOT NULL DEFAULT '0',
`f_longitude`             DOUBLE NOT NULL DEFAULT '0',
`f_location`              VARCHAR(32) NOT NULL DEFAULT '0',
`f_url`                   VARCHAR(64) NOT NULL DEFAULT '0',
`f_created`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`f_updated`               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`f_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
`f_uid`                     BIGINT NOT NULL AUTO_INCREMENT,
`f_nickname`                VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_gender`                  VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_language`                VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_avatar_url`              VARCHAR(128)  NOT NULL DEFAULT ''COMMENT 'from wx',
`f_city`                    VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_country`                 VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_province`                VARCHAR(32)  NOT NULL DEFAULT '' COMMENT 'from wx',
`f_openid`                  VARCHAR(128) NOT NULL DEFAULT '' UNIQUE COMMENT 'from wx',
`f_session_key`             VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'from wx',
`f_expires_in`              INT NOT NULL DEFAULT  '0'        COMMENT 'from wx',
`f_client_session`          VARCHAR(16) NOT NULL DEFAULT '',
`f_tel_num`                 VARCHAR(32)  NOT NULL DEFAULT '',
`f_wx`                      VARCHAR(32)  NOT NULL DEFAULT '',
`f_weibo`                   VARCHAR(32)  NOT NULL DEFAULT '',
`f_birthday`                VARCHAR(16) NOT NULL DEFAULT '',
`f_stature`                 VARCHAR(16) NOT NULL DEFAULT '',
`f_hometown`                VARCHAR(16) NOT NULL DEFAULT '',
`f_portrait`                VARCHAR(64)  NOT NULL DEFAULT '',
`f_valid`                   TINYINT NOT NULL DEFAULT '0',
`f_created`                 DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`f_updated`                 DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`f_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;



