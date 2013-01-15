CREATE TABLE IF NOT EXISTS `awesome_module` (
 `id` int(11) NOT NULL auto_increment,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `price` int(11) NOT NULL,
 `email` varchar(255),
 `visible` ENUM('Y','N') NOT NULL DEFAULT 'Y',
 `publish_on` datetime,
 `image` varchar(255) NOT NULL,
 `radiobutton` ENUM('1','2','3'),
 `accept_terms` ENUM(''),
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;