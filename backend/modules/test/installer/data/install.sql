CREATE TABLE IF NOT EXISTS `test` (
 `id` int(11) NOT NULL auto_increment,
 `category_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `test_categories` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;