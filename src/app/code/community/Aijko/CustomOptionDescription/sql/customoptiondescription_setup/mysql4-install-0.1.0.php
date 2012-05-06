<?php
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('catalog_product_option_description')};
CREATE TABLE {$this->getTable('catalog_product_option_description')} (
    `option_description_id` int(10) unsigned NOT NULL auto_increment,
    `option_id` int(10) unsigned NOT NULL default '0',
    `store_id` smallint(5) unsigned NOT NULL default '0',
    `description` VARCHAR(500) NOT NULL default '',
    PRIMARY KEY (`option_description_id`),
    KEY `CATALOG_PRODUCT_OPTION_DESCRIPTION_OPTION` (`option_id`),
    KEY `CATALOG_PRODUCT_OPTION_DESCRIPTION_STORE` (`store_id`),
    CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_DESCRIPTION_OPTION` FOREIGN KEY (`option_id`) REFERENCES {$this->getTable('catalog_product_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_DESCRIPTION_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

");

$installer->endSetup();