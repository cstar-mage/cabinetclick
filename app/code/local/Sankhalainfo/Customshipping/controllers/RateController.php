<?php 

require_once "Mage/Checkout/controllers/OnepageController.php";

class Sankhalainfo_Customshipping_RateController extends Mage_Checkout_OnepageController {
    
    public function additionalAction() {
		
		try {
			 
			$this->getOnepage()->getQuote()->setShippingExtraPerson($this->getRequest()->getPost('shipping_extra_person') ?: null);
			
			$this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
			   
			$this->getOnepage()->getQuote()->save();
			
			$result = ['status' => 'success'];
			
		} catch(\Exception $e) { 
			
			$result = ['status' => 'error'];
		}
		 
		return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result)); 
	}
}
