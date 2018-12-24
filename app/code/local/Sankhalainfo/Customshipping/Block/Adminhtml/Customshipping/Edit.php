<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'customshipping';
        $this->_controller = 'adminhtml_customshipping';
        
        $this->_updateButton('save', 'label', Mage::helper('customshipping')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('customshipping')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('customshipping_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'customshipping_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'customshipping_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('customshipping_data') && Mage::registry('customshipping_data')->getId() ) {
            return Mage::helper('customshipping')->__("Edit Rate '%s'", $this->htmlEscape(Mage::registry('customshipping_data')->getRadius()));
        } else {
            return Mage::helper('customshipping')->__('Add New Rate');
        }
    }
}
