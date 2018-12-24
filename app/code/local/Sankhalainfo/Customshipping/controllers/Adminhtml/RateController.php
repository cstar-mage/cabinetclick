<?php

require_once "Mage/Adminhtml/controllers/Sales/Order/CreateController.php";
 
class Sankhalainfo_Customshipping_Adminhtml_RateController extends Mage_Adminhtml_Sales_Order_CreateController {
	
	public function additionalAction() {
		
		$result = array();
		
		try {
			
			$this->_initSession();
			  
			$this->_getOrderCreateModel()->getQuote()->setShippingExtraPerson($this->getRequest()->getPost('shipping_extra_person') ?: null );
			
			$this->_getOrderCreateModel()->setRecollect(true)->collectShippingRates();
			   
			$this->_getOrderCreateModel()->saveQuote();
			
			$result = ['status' => 'success'];
			
		} catch(\Exception $e) { 
			
			$result = ['status' => 'error'];
		}
		 
		return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
}
