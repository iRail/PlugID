CREATE TABLE `clients` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL DEFAULT '',
  `client_id` VARCHAR(32) NOT NULL DEFAULT '',
  `client_secret` VARCHAR(32) NOT NULL DEFAULT '',
  `redirect_uri` VARCHAR(250) NOT NULL DEFAULT '',
  `auto_approve` TINYINT(1) NOT NULL DEFAULT '0',
  `autonomous` TINYINT(1) NOT NULL DEFAULT '0',
  `status` ENUM('development','pending','approved','rejected') NOT NULL DEFAULT 'development',
  `suspended` TINYINT(1) NOT NULL DEFAULT '0',
  `notes` TINYTEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `scopes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scope` VARCHAR(64) NOT NULL DEFAULT '',
  `name` VARCHAR(64) NOT NULL DEFAULT '',
  `description` VARCHAR(100) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `scope` (`scope`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` VARCHAR(32) NOT NULL DEFAULT '',
  `redirect_uri` VARCHAR(250) NOT NULL DEFAULT '',
  `type_id` VARCHAR(64) DEFAULT NULL,
  `type` ENUM('user','auto') NOT NULL DEFAULT 'user',
  `code` TEXT,
  `access_token` VARCHAR(50) DEFAULT '',
  `stage` ENUM('request','granted') NOT NULL DEFAULT 'request',
  `first_requested` INT(10) UNSIGNED NOT NULL,
  `last_updated` INT(10) UNSIGNED NOT NULL,
  `limited_access` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Used for user agent flows',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `session_scopes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` INT(11) UNSIGNED NOT NULL,
  `access_token` VARCHAR(50) NOT NULL DEFAULT '',
  `scope` VARCHAR(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `scope` (`scope`),
  KEY `access_token` (`access_token`),
  CONSTRAINT `session_scopes_ibfk_1` FOREIGN KEY (`scope`) REFERENCES `scopes` (`scope`),
  CONSTRAINT `session_scopes_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


ALTER TABLE  `sessions` ADD FOREIGN KEY (`client_id`) REFERENCES  `clients` (
`client_id`
) ON DELETE CASCADE

