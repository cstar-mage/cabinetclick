<?php

class Kloc_Import_Model_Relatedproduct extends Kloc_Import_Model_Import{

	 
	
	public function import(){     
		$file = $this->getFile();  
		if($file && count($this->_productField)) {  
			$count = 0;     
			while (($productRow = fgetcsv($file)) !== FALSE) {  
				$_product = $this->_prepareProduct($productRow);    
				try{ 
					if($_product->getId()) {  
						$_product->save();
						$count++;     
					} 
				}catch(Exception $e){  }    
				  
			} 
		}   
		fclose($file);
		$this->setProductCount($count);
		return $this;
	}
	protected function _prepareProduct($productRow = array()){	
		$productArray  = array(); 
		$relatedLinkData = array();
		if($this->_productField && count($this->_productField) > 0) { 
		
			foreach($this->_productField as $index => $field) {
			
				$value = trim($productRow[$index]);
				$productArray[$field] = $value;   
				 
			}   
			
			$sku = @$productArray['sku']; 
		 
			$productId = $this->getProduct()->getIdBySku($sku);  
			
			$_product = $this->getProduct();
			
			if($productId) {
				$_product->load($productId);  
			}
			
			if(isset($productArray['relatedproductsku'])) {
				
				$relatedProducts = explode(';', $productArray['relatedproductsku']);
				$relatedProduct = $this->getProduct();
				foreach($relatedProducts as $relatedSku) {
					
					$relatedProductId = $relatedProduct->getIdBySku(trim($relatedSku)); 
					if($relatedProductId) {
						$relatedLinkData[$relatedProductId] = array('position' => 0);
					}
				}
				
				if(count($relatedLinkData) > 0) {
					
					$_product->setRelatedLinkData($relatedLinkData);	
				}
			} 
			 
		}  
		return $_product;
	}
	
	public function getFields(){
	
		/* 'CSV Field'     			=> 'Product Field' */  
		$fields = array(   
			'SKU'					=> 'sku',  
		 	'RelatedProductSku'		=> 'relatedproductsku',  
		); 
		return $fields;
	}
}
