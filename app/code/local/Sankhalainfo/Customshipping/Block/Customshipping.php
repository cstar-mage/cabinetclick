<?php
class Sankhalainfo_Customshipping_Block_Customshipping extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCustomshipping()     
     { 
        if (!$this->hasData('customshipping')) {
            $this->setData('customshipping', Mage::registry('customshipping'));
        }
        return $this->getData('customshipping');
        
    }
}