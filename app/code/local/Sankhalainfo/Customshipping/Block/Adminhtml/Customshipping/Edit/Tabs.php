<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customshipping_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customshipping')->__('Shipping Rate Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('customshipping')->__('Shipping Rate Information'),
          'title'     => Mage::helper('customshipping')->__('Shipping Rate Information'),
          'content'   => $this->getLayout()->createBlock('customshipping/adminhtml_customshipping_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
