# RSS headline SQL structure

DROP TABLE IF EXISTS `feeds`;
DROP TABLE IF EXISTS `rss`;
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) TYPE=INNODB;

CREATE TABLE `rss` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `url` text NOT NULL,
    `title` varchar(255) NOT NULL,
    `link` varchar(255) NOT NULL,
    `last_update` timestamp default now(),
    `last_click` timestamp default now(),
    `clicks` int(10) unsigned NOT NULL default 0,
    `category` int(10) unsigned,
    PRIMARY KEY (`id`),
    FOREIGN KEY (category) REFERENCES categories(id)
) TYPE=INNODB;

CREATE TABLE `feeds` (
    `id` int(10) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL,
    `link` text NOT NULL,
    `timestamp` timestamp default now(),
    `rss_parent` int(10) unsigned,
    PRIMARY KEY (`id`),
    FOREIGN KEY (rss_parent) REFERENCES rss(id)
) TYPE=INNODB;
