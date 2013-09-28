# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.27)
# Database: advanced_user_manager
# Generation Time: 2013-09-28 21:34:48 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message`;

CREATE TABLE `message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT '',
  `message` text,
  `read` tinyint(1) DEFAULT '0',
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `sent_from_id` int(11) unsigned DEFAULT NULL,
  `sent_to_id` int(11) unsigned DEFAULT NULL,
  `show_to_sender` tinyint(4) DEFAULT '1',
  `show_to_receiver` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sent_from_id` (`sent_from_id`),
  KEY `sent_to_id` (`sent_to_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sent_from_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`sent_to_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table notifications_centre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications_centre`;

CREATE TABLE `notifications_centre` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `json_object_data` text,
  `created_at` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `pretty_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;

INSERT INTO `permission` (`id`, `description`, `pretty_name`)
VALUES
	(1,'accessAdminPanel','Access Admin Panel'),
	(2,'viewMembers','View Members'),
	(3,'editMembers','Edit Members'),
	(4,'createMembers','Create Members'),
	(5,'deleteMembers','Delete Members'),
	(6,'bannedMember','Banned Account'),
	(7,'accessSettingsPanel','Access Settings Panel');

/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table private_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `private_pages`;

CREATE TABLE `private_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `URL` varchar(350) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `private_pages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `private_pages` WRITE;
/*!40000 ALTER TABLE `private_pages` DISABLE KEYS */;

INSERT INTO `private_pages` (`id`, `user_id`, `URL`)
VALUES
	(1,1,'member/hello.php');

/*!40000 ALTER TABLE `private_pages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(42) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;

INSERT INTO `role` (`role_id`, `role_name`)
VALUES
	(1,'Administrator'),
	(3,'Banned'),
	(2,'Member');

/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role_permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role_permission`;

CREATE TABLE `role_permission` (
  `role_id` int(11) unsigned NOT NULL DEFAULT '0',
  `permission_id` int(11) unsigned DEFAULT NULL,
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;

INSERT INTO `role_permission` (`role_id`, `permission_id`)
VALUES
	(1,1),
	(1,2),
	(1,3),
	(1,4),
	(1,5),
	(1,7),
	(3,6);

/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `version` varchar(11) DEFAULT NULL,
  `meta_author` varchar(128) DEFAULT NULL,
  `meta_description` varchar(256) DEFAULT NULL,
  `banned_email_extensions` varchar(256) DEFAULT NULL,
  `default_group` int(11) unsigned DEFAULT NULL,
  `allow_registration` tinyint(1) DEFAULT '1',
  `email` varchar(128) DEFAULT 'noreply@phpcodemonkey.com',
  `stripe_secret_key` varchar(128) DEFAULT NULL,
  `stripe_ publishable_key` varchar(128) DEFAULT NULL,
  `pm_disabled` tinyint(1) DEFAULT '0',
  `support_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `default_group` (`default_group`),
  CONSTRAINT `setting_ibfk_1` FOREIGN KEY (`default_group`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;

INSERT INTO `setting` (`id`, `name`, `url`, `version`, `meta_author`, `meta_description`, `banned_email_extensions`, `default_group`, `allow_registration`, `email`, `stripe_secret_key`, `stripe_ publishable_key`, `pm_disabled`, `support_enabled`)
VALUES
	(1,'Advanced User Manager','http://localhost/advanced-user-manager/','1.0.4','John Crossley','This is a meta description, nothing too fancy just this.','fake.com example.com googlemail.com',2,1,'hello@phpcodemonkey.com','sk_test_htCmN7XkocIQ7Z45G6CI3agY','pk_test_h5kd3AIMCHF9IczvdGbsRh5S',1,0);

/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table template
# ------------------------------------------------------------

DROP TABLE IF EXISTS `template`;

CREATE TABLE `template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `subject` varchar(128) DEFAULT NULL,
  `data` text,
  `fields` varchar(256) DEFAULT NULL,
  `default_data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `template` WRITE;
/*!40000 ALTER TABLE `template` DISABLE KEYS */;

INSERT INTO `template` (`id`, `name`, `subject`, `data`, `fields`, `default_data`)
VALUES
	(1,'Forgot Password Request','Forgot Password Request','Hello {{username}},\r\n\r\nSo you forgot your password? Not to worry! Please use the link below and we\'ll send you a new password.\r\n\r\n<a href=\"{{new_password_url}}\">{{new_password_url}}</a>\r\n\r\nJust click on the link to begin the process of obtaining a new password. If you did not make this request then do not worry! Nothing will be changed.\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team.','a:2:{i:0;s:8:\"username\";i:1;s:16:\"new_password_url\";}',''),
	(2,'New Personal Message','New Personal Message','Hello {{username}},\r\n\r\nGood news! You have received a personal message from <strong>{{sender}}</strong> over at <strong>{{system_name}}</strong>. To see the message in context then head over to your account and check it out.\r\n\r\n<blockquote>\r\n{{title}}\r\n{{message}}\r\n</blockquote>\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team\r\n','a:4:{i:0;s:8:\"username\";i:1;s:6:\"sender\";i:2;s:5:\"title\";i:3;s:7:\"message\";}',''),
	(3,'New Random Password','New random password','Hello {{username}},\r\n\r\nNew password <strong>{{password}}</strong>\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team','a:2:{i:0;s:8:\"username\";i:1;s:8:\"password\";}',''),
	(4,'New User (From Admin Panel)','Your new account','Welcome {{username}},\r\n\r\nGood news! {{creator}} over at {{system_name}} has just made you a new account! No action is required on your part WOO! Here is some information you may need.\r\n\r\nUsername: {{username}}\r\nPassword: {{password}}\r\nEmail: {{user_email}}\r\nGroup: {{user_group}}\r\n\r\nSo head over to <a href=\"{{url}}\">{{url}}</a> and login.\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team','a:5:{i:0;s:7:\"creator\";i:1;s:8:\"username\";i:2;s:8:\"password\";i:3;s:10:\"user_email\";i:4;s:10:\"user_group\";}',''),
	(5,'Registration Complete','Registration complete','Hello {{username}},\r\n\r\nJust to let you know that your registration is now complete and you may login to your account.\r\n\r\n<a href=\"{{login_url}}\">{{login_url}}</a>\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team','a:2:{i:0;s:8:\"username\";i:1;s:9:\"login_url\";}',''),
	(6,'User Information Updated','Heads up! your information has been updated','Hello {{username}},\r\n\r\nJust to let you know that some of your data has been updated by one of {{system_name}}\'s administrators. Below is a list of what has been changed.\r\n\r\n<strong>Username</strong>: {{username}}\r\n<strong>Full name</strong>: {{fullname}}\r\n<strong>Email</strong>: {{user_email}}\r\n<strong>Password</strong>: {{password}}\r\n<strong>Group</strong>: {{user_group}}\r\n<strong>Location</strong>: {{location}}\r\n<hr>\r\n<strong>Bio</strong>: {{bio}}\r\n\r\n<strong>Account visibility</strong>: {{account_private}}\r\n\r\n{{status_change_message}}\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team','a:9:{i:0;s:8:\"username\";i:1;s:8:\"fullname\";i:2;s:10:\"user_email\";i:3;s:8:\"password\";i:4;s:10:\"user_group\";i:5;s:3:\"bio\";i:6;s:8:\"location\";i:7;s:15:\"account_private\";i:8;s:21:\"status_change_message\";}',''),
	(7,'Welcome Email','New user account - Action required','Hello {{username}} and welcome to {{system_name}},\r\n\r\nJust to let you know that you need to verify this is your email to complete your registration. Simply click on the link below to complete.\r\n\r\n<a href=\"{{validate_url}}\">Verify your account</a>\r\n\r\nKind Regards,\r\n\r\n{{system_name}} team','a:2:{i:0;s:8:\"username\";i:1;s:12:\"validate_url\";}','');

/*!40000 ALTER TABLE `template` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(52) DEFAULT NULL,
  `lastname` varchar(52) DEFAULT NULL,
  `username` varchar(42) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `bio` text,
  `location` varchar(128) DEFAULT NULL,
  `password` varchar(82) NOT NULL DEFAULT '',
  `salt` varchar(52) DEFAULT NULL,
  `private` tinyint(1) DEFAULT '0',
  `notify_me_personal_message` tinyint(1) DEFAULT '1',
  `receive_personal_messages` tinyint(1) DEFAULT '1',
  `banned_from_sending_personal_messages` tinyint(1) DEFAULT '0',
  `redirect_to` varchar(128) DEFAULT 'member/',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(42) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `firstname`, `lastname`, `username`, `email`, `bio`, `location`, `password`, `salt`, `private`, `notify_me_personal_message`, `receive_personal_messages`, `banned_from_sending_personal_messages`, `redirect_to`, `created_at`, `updated_at`, `last_login`, `verified`, `hash`)
VALUES
	(1,'John','Crossley','admin','hello@phpcodemonkey.com','Hello my name is John Crossley and I\'m a 24 year old student / web app developer from Manchester, England. I specialise in writing beauiful php, HTML, CSS and JavaScript oh and a little bit of Ruby. If your looking for someone to write awesome code for you, your company or even your dog then then I\'m your guy!','Manchester, England','0643c76c544072ea70d1f869d1b45cb45c3771a3','439313a160fb70949d073a266455d6d4a38c7c72',0,0,1,0,'admin/index.php','2013-05-26 21:45:48','2013-09-21 17:45:52','2013-09-21 17:45:52',1,'e22f9d25178d9c9bd629048c08afb69e'),
	(2,'Carl','Evison','carlospinkz','newb2ninja@gmail.com','Hello, my name is Carl and welcome to my profile. - edit.',NULL,'ce430ae1364e1a94a4de34cfdf190338f2462851','d558a61bf29b8d47fdd70962d5aff57f968e7a8d',0,1,1,0,'member/','2013-08-12 19:51:46','2013-09-18 22:56:45','2013-09-18 22:55:22',1,NULL);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;

INSERT INTO `user_role` (`user_id`, `role_id`)
VALUES
	(1,1),
	(2,2);

/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
