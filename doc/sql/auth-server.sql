CREATE TABLE IF NOT EXISTS `auth_codes` (
  `client_id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL,
  `expires` int(11) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `client_id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` varchar(40) NOT NULL,
  `expires` int(11) NOT NULL,
  UNIQUE KEY `unique` (`client_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `client_secret` varchar(32) NOT NULL,
  `redirect_uri` varchar(250) NOT NULL,
  `status` enum('rejected','approved') NOT NULL DEFAULT 'approved',
  PRIMARY KEY (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user_tokens` (
  `user_id` int(11) NOT NULL,
  `service_type` enum('foursquare','facebook','twitter','google','viking') NOT NULL,
  `ext_user_id` varchar(32) NOT NULL,
  `access_token` varchar(40) NOT NULL,
  `refresh_token` varchar(40) DEFAULT NULL,
  UNIQUE KEY `unique1` (`service_type`,`user_id`),
  UNIQUE KEY `unique2` (`service_type`,`user_id`,`ext_user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `auth_codes`
  ADD CONSTRAINT `auth_codes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_codes_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;


ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;


ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
