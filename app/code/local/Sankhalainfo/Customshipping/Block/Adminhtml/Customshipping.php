<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct() {
	  
		$this->_controller = 'adminhtml_customshipping';
		$this->_blockGroup = 'customshipping';
		$this->_headerText = Mage::helper('customshipping')->__('Shipping Rate Manager');
		$this->_addButtonLabel = Mage::helper('customshipping')->__('Add New Shipping Rate');
		parent::__construct();
	}
} 
