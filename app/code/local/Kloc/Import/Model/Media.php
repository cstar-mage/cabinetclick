<?php

class Kloc_Import_Model_Media extends Kloc_Import_Model_Import{

	const IMAGE_URL = 'http://http://www.crowdcontrolcenter.com/';
	
	public function getImagePath(){
		return  Mage::getBaseDir(). DS .'product'. DS. 'images'.DS; 
	}
	
	public function import(){    
		$file = $this->getFile();  
		if($file && count($this->_productField)) {  
			$count = 0;     
			// $productMedia = Mage::getModel('catalog/product_attribute_media_api');
			// $productMedia->remove($_product->getId(),$image['file']);
			while (($productRow = fgetcsv($file)) !== FALSE) {  
				$_product = $this->_prepareProduct($productRow);    
				try{ 
					if($_product->getId()) {
						
						// Remove All old images 
						$oldImages = $_product->getMediaGallery();  	  
						if($oldImages && count($oldImages['images']) > 0) {    
							foreach($oldImages['images'] as &$image) { 
								$image['removed'] = 1;  		
							}
						}
						// End Remove All old images  
						 
						$_product->setMediaGallery($oldImages);
						$this->uploadMedia($_product->getImages(), $_product);     
						$_product->save();
						$count++;  
					} 
				}catch(Exception $e){ }    
				  
			} 
		} 
		fclose($file);
		$this->setProductCount($count);
		return $this;
	}
	protected function _prepareProduct($productRow = array()){	
		$productArray  = array();
		$images = array();
		$media = array('media_1','media_2','media_3','media_4','media_5'); 
		if($this->_productField && count($this->_productField) > 0) { 
		
			foreach($this->_productField as $index => $field) {
			
				$value = trim($productRow[$index]);
				$productArray[$field] = $value;  
				
				if(in_array($field, $media) && $value != ''){
				
					$images[] = $value;
				}
			} 
			
			$sku = @$productArray['sku']; 
					
			$productId = $this->getProduct()->getIdBySku($sku);  
			
			$_product = $this->getProduct();
			
			if($productId) {
				$_product->load($productId);  
			}
			$_product->setImages($images);	
		} 
		return $_product;
	}
	 
	public function uploadMedia($media, $_product) { 
		 
		if($media){
			$mediacount = 1; 
			$imgCount = count($media);
			$mediaAttributes2 = array('thumbnail');
			$mediaAttributes = array('image');
			if($imgCount == 1) {
				$mediaAttributes = array('image','thumbnail','small_image');
			} else if($imgCount == 2){ 
				$mediaAttributes2 = array('thumbnail');
				$mediaAttributes = array('image','small_image');
			} else {
				$mediaAttributes = array('image');
			} 
			foreach($media as $image){ 
				$name = basename($image);
				
				if($name && $name != '') {
					
 					$source = $this->getImagePath().$name;  
					
					/*if(!file_exists($source)){
					
						$this->_downloadLiveImages($name);
					}*/
					if(file_exists($source)) { 
						if($mediacount == 1) {      
							$_product->addImageToMediaGallery($source,$mediaAttributes, false, false);    
						} else if($mediacount == 2){
							$_product->addImageToMediaGallery($source, $mediaAttributes2, false, false);  
						}else if($mediacount == 3){
							$_product->addImageToMediaGallery($source, array('small_image'), false, false);  
						} else {
							$_product->addImageToMediaGallery($source, array(), false, false);
						} 
						$mediacount++;
					}
				} 
			}
		}
		return $_product;
    }
	
	private function _downloadLiveImages($name) {
	
		$name = basename($name);
		$fileurl = self::IMAGE_URL.$name;
		$filepath = $this->getImagePath().urlencode($name);
		
		$ch = curl_init($fileurl);
		$fp = fopen($filepath, 'x');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close($ch);
		fwrite($fp, $raw);
		fclose($fp);
		if(file_exists($filepath)) return $filepath;
		else return false;
	}
	
	public function getFields(){
	
		/* 'CSV Field'     			=> 'Product Field' */  
		$fields = array(   
			'SKU'					=> 'sku',  
		 	'MediaItem1'			=> 'media_1',
			'MediaItem2'			=> 'media_2',
			'MediaItem3'			=> 'media_3',
			'MediaItem4'			=> 'media_4',
			'MediaItem5'			=> 'media_5', 
		); 
		return $fields;
	}
}
