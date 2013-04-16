

CREATE TABLE IF NOT EXISTS `{$underscored_name}_categories` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;