<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Grid_Renderer extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $_object) {
		
		if (!$_object->getData($this->getColumn()->getIndex())) {
            return null;
        }
        
        return "<div style='word-break: break-all;'>{$_object->getData($this->getColumn()->getIndex())}</div>";
	}
}
