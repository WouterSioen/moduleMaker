

CREATE TABLE IF NOT EXISTS `{$underscored_name}_images` (
 `id` int(11) NOT NULL auto_increment,
 `{$underscored_name}_id` int(11) NOT NULL,
 `name` varchar(255) NOT NULL,
 `sequence` int(11) NOT NULL,
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;