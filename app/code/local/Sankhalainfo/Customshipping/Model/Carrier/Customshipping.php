<?php 

class Sankhalainfo_Customshipping_Model_Carrier_Customshipping 
extends Mage_Shipping_Model_Carrier_Abstract 
implements Mage_Shipping_Model_Carrier_Interface {
	
	CONST CODE = 'customshipping';
	
    protected $_code = self::CODE;
    
    protected $_isFixed = true;
	
	protected $request = null;
	  
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		
		$this->request = $request;
		 
        if (!$this->getConfigFlag('active')) { 	
            return false;
        }
 	
        $shippingPrice = $this->getShippingPrice();
		
		$result = Mage::getModel('shipping/rate_result');
		
        if ($shippingPrice !== false) {
			
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier(self::CODE);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod(self::CODE);
            $method->setMethodTitle($this->getConfigData('name'));
  
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }
	
	public function getQuote() {
		
		foreach ($this->request->getAllItems() as $item) {
			
			return $item->getQuote();
		}
		
		return Mage::getSingleton('sales/quote');
	}
	
	public function getShippingPrice() {
		 
		 $_price = false;
		 
		 $_collection = Mage::getModel('customshipping/customshipping')->getCollection();
		 
		 $_collection->addFieldToFilter('postcode', array('finset' => trim($this->request->getDestPostcode())));
		 
		 if($_collection->getFirstItem() && $_collection->getFirstItem()->getId()) {
			   
			  $_price += (float) $_collection->getFirstItem()->getPrice();
		 } 
		 
		 if($_price && $this->getQuote()->getShippingExtraPerson() == 1) {
			 
			 $_price += (float) $this->getConfigData('extra_person_charge');
		 } 
		  
		 return $_price;
	}
	
    public function getAllowedMethods() {
		
        return array(self::CODE => $this->getConfigData('name'));
    }

}
