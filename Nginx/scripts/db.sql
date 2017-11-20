
DROP TABLE IF EXISTS `t_client`;
CREATE TABLE `t_client` (
`f_id`             BIGINT NOT NULL AUTO_INCREMENT,
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
`f_id`                    BIGINT NOT NULL AUTO_INCREMENT,
`f_openid`                VARCHAR(128) NOT NULL DEFAULT '',
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
