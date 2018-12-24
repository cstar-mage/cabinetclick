<?php

class Kloc_Import_Block_Adminhtml_Import_Media_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
			->_updateButton('save', 'label', $this->__('Import Product Images')); 
		$data = array(
        'label' =>  'Download Csv Format',
        'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/downloadImageFormat') . '\')',
        'class'     =>  'scalable'
		);
		$this->addButton ('download-csv-format', $data, 0, 100,  'header'); 
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'import_product_id';
        $this->_blockGroup = 'import';
        $this->_controller = 'adminhtml_import_media';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('import')->__('Import Product Images');
    }
}
