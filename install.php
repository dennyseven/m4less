<?php 
$db = mysql_connect('localhost', 'marine_OCadmin', 'Temp457Stuff%');
if (!$db) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db('marine_OCmain', $db);
$rslt = mysql_query("CREATE TABLE IF NOT EXISTS `seo_data` (
                      `title` varchar(255) COLLATE utf8_bin NOT NULL,
                      `meta_keywords` varchar(255) COLLATE utf8_bin NOT NULL,
                      `meta_description` varchar(255) COLLATE utf8_bin NOT NULL,
                      `type` varchar(32) COLLATE utf8_bin NOT NULL,
                      `id` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'It will be id for products, category, manufacturer, information and route for general urls',
                      `language_id` int(11) NOT NULL,
                      `url_alias_id` int(11) NOT NULL,
                      UNIQUE KEY `type` (`type`,`id`,`language_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin");

$rslt = mysql_query("CREATE TABLE IF NOT EXISTS `seo_pattern` (
                      `pattern_id` int(11) NOT NULL AUTO_INCREMENT,
                      `product_url_keyword` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_title` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_meta_keywords` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_meta_description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_tags` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                      `product_image_name` text NOT NULL,
                      `category_url_keyword` text NOT NULL,
                      `category_title` text NOT NULL,
                      `category_keyword` text NOT NULL,
                      `category_meta_description` text NOT NULL,
                      `manufacturer_url_keyword` text NOT NULL,
                      `information_page_url_keyword` text NOT NULL,
                      `information_pages_title` text NOT NULL,
                      `yahoo_id` int(11) NOT NULL,
                      PRIMARY KEY (`pattern_id`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

mysql_close($db);
?>