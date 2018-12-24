<?php

class Kloc_Import_Block_Adminhtml_Import_Relatedproduct_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
				'id'      => 'edit_form',
				'action'  => $this->getUrl('*/*/relatedproductPost'),
				'method'  => 'post',
				'enctype' => 'multipart/form-data'
		));
		
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('attributes_form', array('legend'=>Mage::helper('import')->__('Import Related Product')));
		
		/* $fieldset->addField('type', 'select', array(
			  'label'     => Mage::helper('import')->__('Product Type'),
			  'class'     => 'required-entry',  
			  'required'  => true,
			  'name'      => 'type',
			  'options'	=> Mage::getModel('import/import')->productTypeArray(),
		));  */
		
		$fieldset->addField('file', 'file', array(
			  'label'     => Mage::helper('import')->__('Upload File'),
			  'class'     => 'required-entry',  
			  'required'  => true,
			  'name'      => 'file',
		));  
	  
		 
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	} 
}
