<?php
	set_time_limit(0);	 
	require_once 'app/Mage.php';
	if (!Mage::isInstalled()) {
		echo "Application is not installed yet, please complete install wizard first.";
		exit;
	}
	Mage::app('admin')->setUseSessionInUrl(false);
	
    $installer = Mage::getResourceModel('catalog/setup','catalog_setup');
    $installer->startSetup();

      //Add group to entity & all attribute sets
    $installer->addAttribute(
          'catalog_category',
          'specs',
          array(
              'label' => 'Specs',
              'group' => 'Specs'   //will be created if necessary
          )
      );

    $installer->endSetup();
  
?>
