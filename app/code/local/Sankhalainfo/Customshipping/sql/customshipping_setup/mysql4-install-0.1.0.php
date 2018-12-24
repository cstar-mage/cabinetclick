<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('customshipping')};
CREATE TABLE {$this->getTable('customshipping')} (
  `customshipping_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `radius` varchar(255) NOT NULL default '',
  `postcode` text NOT NULL default '',
  `price` decimal(10,2) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`customshipping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$this->getConnection()->addColumn($this->getTable('sales/quote'), 'shipping_extra_person', "smallint(6) default NULL");

$installer->endSetup(); 
