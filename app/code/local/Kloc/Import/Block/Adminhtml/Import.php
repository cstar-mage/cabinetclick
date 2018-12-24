<?php
class Kloc_Import_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_import';
    $this->_blockGroup = 'import';
    $this->_headerText = Mage::helper('import')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('import')->__('Add Item');
    parent::__construct();
  }
}