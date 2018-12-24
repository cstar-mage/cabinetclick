<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('customshipping_form', array('legend'=>Mage::helper('customshipping')->__('Shipping Rate information')));
     
		$fieldset->addField('radius', 'text', array(
			'label'     => Mage::helper('customshipping')->__('Radius'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'radius',
		));

		$fieldset->addField('price', 'text', array(
			'label'     => Mage::helper('customshipping')->__('Price'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'price',
		));

		$fieldset->addField('postcode', 'textarea', array(
			'label'     => Mage::helper('customshipping')->__('Postcode'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'postcode',
		));
     
      if ( Mage::getSingleton('adminhtml/session')->getCustomshippingData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomshippingData());
          Mage::getSingleton('adminhtml/session')->setCustomshippingData(null);
      } elseif ( Mage::registry('customshipping_data') ) {
          $form->setValues(Mage::registry('customshipping_data')->getData());
      }
      return parent::_prepareForm();
  }
}
