<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
DROP TABLE IF EXISTS {$this->getTable('wtc_hdfc')};
create table wtc_hdfc(id int not null auto_increment,error_text varchar(255),paymentid varchar(255),trackid varchar(255),error varchar(255),result varchar(255),postdate varchar(255),tranid varchar(255),auth varchar(255),avr varchar(255),ref varchar(255),amt varchar(255),udf1 varchar(255),udf2 varchar(255),udf3 varchar(255),udf4 varchar(255),udf5 varchar(255), primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 