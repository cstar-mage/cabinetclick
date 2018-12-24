<?php

class Kloc_Import_Model_Product  extends Kloc_Import_Model_Import{
    
	protected $_defaultData = array();
	
	protected $_categories = array();
	
	protected $_associatedProducts = array();
	
	protected $_skusAndIds =  array();
	protected $_newSkusAndIds = array();
	
	protected $_simpleOptions = array();
	
	public function import(){  

		$file = $this->getFile();  
		$count = 0;  
		$errorLog = false;
		if($file && count($this->_productField)) {  
			$skus = array();
			while (($productRow = fgetcsv($file)) !== FALSE) { 
				$_product = $this->_prepareProduct($productRow);  
				$sku = $_product->getSku();  
				if($sku) { 
					if(in_array($sku, $skus)){
						continue;
					} 
					$skus[] = $sku;     
					try{   
						if($_product->getSuperAttribute()) { 
							$this->_preapreConfigurableProduct($_product);  
						} 
						$_product->save();    
						$this->_newSkusAndIds[$_product->getSku()] = $_product->getId();
						$count++;       
						if($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
							
							 // $this->_preapreConfigurableProduct($this->_prepareProduct($productRow), true); 
						}
					}catch(Exception $e){ 
						$errorLog = 'error in sku ['.$sku.'] '.$e->getMessage();
						Mage::log($errorLog, null, 'csv-product.log', true);    
					}   
				}
			} 
			fclose($file);
		}
		if($errorLog) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please check log file  "csv-product.log" some products are not imported.'));
		}
		$this->setProductCount($count);  
		return $this;
	}
	
	protected function _getSkusAndIds(){
		if($this->_skusAndIds == null)
		{	 
			$productTable = Mage::getSingleton('core/resource')->getTableName('catalog/product');
			$query = 'SELECT `entity_id`,`sku` FROM ' . $productTable;
			$results = $this->_getConnection()->fetchAll($query);
			if(count($results) > 0){
				$this->_skusAndIds = array();
				foreach($results as $result)
				{
					$this->_skusAndIds[trim($result['sku'])] = $result['entity_id'];
				}
			}
		} 
		
		if(count($this->_newSkusAndIds) > 0) {
			foreach($this->_newSkusAndIds as $sku => $id) {
				
				$this->_skusAndIds[$sku] = $id;
			}
		}
		
		return $this->_skusAndIds; 
	}
	
	protected function _preapreConfigurableProduct($_product, $optionPrice = false){
		 
		$this->_associatedProducts = array();
		$superOptionsPrice = array();
		$superOptions = array(); 
		$superAttibutes = $_product->getSuperAttribute();
		$superAttibuteIds = array(); 
		foreach($superAttibutes as $superAttibute){ 
			// Validate super attribute for create configurable product
			 $attreibuteId = $this->validateSuperAttribute($superAttibute); 
			 $superAttibuteIds[] = $attreibuteId;
			$otpions = array();	
			$values = array();
			
			foreach(explode(',', $_product->getData($superAttibute)) as $option){
					
					$optionValues = explode(':', $option); 
					$otpions[] = $optionValues[0]; 
					$superOptionsPrice[$superAttibute][$optionValues[0]] = @$optionValues[1];
			} 
			$superOptions[$superAttibute] = $otpions;
			
			
			
		} 
		// Start Create Associated Product
		
		$quickProductdata = $this->_getQuickProduct($_product); 	
		
		if(isset($quickProductdata['simple_sku']) &&  count($quickProductdata['simple_sku']) > 0){

			$this->_addAssociatedProductBySku($superAttibutes, @$quickProductdata['simple_sku']);	
		}
		/*else{
			$this->_createAssociatedProduct($superAttibutes, $superOptions, $quickProductdata, 0); 
		}*/
		
		$_product->unsetData('super_attribute');
		// End create Associated Product
		
		// Strat Assign Associated Product to configurable product
		$_product->getTypeInstance()->setUsedProductAttributeIds($superAttibuteIds);
		$_product->setCanSaveConfigurableAttributes(true); 
		if($_product->getId()) {  
			$tempProduct = Mage::getModel('catalog/product')->load($_product->getId());
			//echo '<pre>';print_r($tempProduct->getData());die;
			$configurableAttributesData = $tempProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($tempProduct); 
		} else { 
			$configurableAttributesData = $_product->getTypeInstance()->getConfigurableAttributesAsArray();
		} 
		$_product->setConfigurableAttributesData($configurableAttributesData);   
		$_product->setConfigurableProductsData($this->_associatedProducts);   
		// End Assign Associated Product to configurable product
		 
		 
		 $newConfigurableAttributesData = $_product->getData('configurable_attributes_data'); 
		 
		if($_product->getId() && $optionPrice == true) {
		 
			$tempProduct = Mage::getModel('catalog/product')->load($_product->getId());
			$newConfigurableAttributesData = $tempProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($tempProduct); 
			
			foreach($newConfigurableAttributesData as $arributeIndex => $configAttribute){
				 $attribute_code = $configAttribute['attribute_code'];
				 foreach($configAttribute['values']  as $optionIndex => $attributeOptions){
					 $price = $superOptionsPrice[$attribute_code][$attributeOptions['label']]; 
					 if($price) {
						$newConfigurableAttributesData[$arributeIndex]['values'][$optionIndex]['pricing_value'] = $price;
					 }
				 }
			}
			
			$_product->setConfigurableAttributesData($newConfigurableAttributesData);      
			
			try{
				$_product->save();
			}catch(Exception $e){ } 
		}     
		
		unset($tempProduct); 
		return $_product;   
	}
	public function validateSuperAttribute($code){
	
		$attribute = $this->getAttribute($code);
		
		if(!$attribute || !$attribute->getId()) {
		
			Mage::throwException('Attribute "'.$code.'" does not exist.');
		}
		
		if(!$attribute->getIsGlobal()){
		
			Mage::throwException('Attribute "'.$code.'" must be global for create configurable product.');
		}
		
		if(!$attribute->getIsConfigurable()){
		
			Mage::throwException('Attribute "'.$code.'" must be used in configurable.');
		}
		if($attribute->getFrontendInput() != 'select'){
		
			Mage::throwException('Attribute "'.$code.'"  type must be dropdown.');
		}
		
		return $attribute->getId();
		  
	}
	
	protected  function _createAssociatedProduct($attributes, $superOptions, $data, $iterate) {
	
		if(isset($attributes[$iterate])) {   
		
			$code = $attributes[$iterate];
			if(isset($superOptions[$code]) && is_array($superOptions[$code])) {
			
				$iterate++; 
				foreach($superOptions[$code] as $option) {    
				
					$currentData = $data;
					$optionId = $this->getAttributeOptionId($code, $option);
					$data[$code] = $optionId;
					$data['config_options'][$code] = $optionId;  
					if(!isset($attributes[$iterate])) {   
					 
						
						$sku = $data['sku'].'-'.$option; 
						$sku = trim(str_replace(' ', '-', $sku)); 
						$name = $data['name'].'-'.$option;      	    
						$data['name'] = $name;
						$data['sku']  = $sku;   
						

						$oldProduct = $this->getSimpleProductCollection($data['config_options'], $data['simple_sku']);
						  
						if($oldProduct && $oldProduct->getId()) {
							
							continue; 
						}  
						
						$productId = $this->getProduct()->getIdBySku($sku);   
						$_associatedProduct = $this->getProduct(); 
						$_associatedProduct->setData($data);
						if($productId) {
							$_associatedProduct->load($productId);  
							if($_associatedProduct->getId()){
								$stockData = $_associatedProduct->getStockItem()->getData();
								$_associatedProduct->setStockData($stockData); 
							} 
						} 	
						 	 
						try{
							$_associatedProduct->save();
							if ($productId = $_associatedProduct->getId()) {
								foreach($attributes as $attr) {
								
									$tempAttribute = $this->getAttribute($attr);
									$this->_associatedProducts[$productId][] = array(
										'attribute_id' => $tempAttribute->getId(),
										'label'        => $_associatedProduct->getAttributeText($attr),
										'value_index'  => $_associatedProduct->getData($tempAttribute->getAttributeCode()),
										 
									);										
								}
							}
						}catch(Exception $e){ } 
					 
						 
					} else { 
						$data['sku'] = $data['sku'].'-'.$option;  
						$data['name'] = $data['name'].'-'.$option;
						$this->_createAssociatedProduct($attributes,$superOptions, $data,$iterate);	
					} 
					
					$data  = $currentData;
				}
			}
		}
		return $this;
	}
	
	protected function _addAssociatedProductBySku($superAttribute = array(), $skus = array()){
		  
		foreach($skus as $sku) {
			
			$product = $this->getProduct();
			
			$productId = $product->getIdBySku($sku);
			
			if($productId) {
				
				$product->load($productId);
				
				if($product && $product->getId()) {
					
					foreach($superAttribute as $code)  {
						
						$value = $product->getData($code); 
						if($value && $value != '') {
							$tempAttribute = $this->getAttribute($code);
							$this->_associatedProducts[$productId][] = array(
								'attribute_id' => $tempAttribute->getId(),
								'label'        => $product->getAttributeText($code),
								'value_index'  => $value,
								 
							); 					
						}
					
					}
				}
			}
			
		} 
		return $this;
		
	}
	public function getSimpleProductCollection($options, $simpleSku = array()){
		
		
		$collection = $this->getProduct()->getCollection()->addFieldToFilter('sku', $simpleSku);
		
		foreach($options as $code => $value){
			
			$collection->addFieldToFilter($code, $value);
		} 
		//echo '++++<pre>';print_r($collection->getFirstItem()->getData());die;
		if(count($collection) > 0) {
			
			return $collection->getFirstItem();
		}
		
		return false;
	}  
	
	protected function _getQuickProduct($superProduct){
		 
		$data = $superProduct->getData();  
		$data['type_id'] =   Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
		$data['visibility'] =   Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE; 
		return $data;
		
		/*  
		return  array(
		
			'type_id'				=>	Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
			'attribute_set_id'		=>	$superProduct->getAttributeSetId(),
			'name'					=>	$superProduct->getName(),
			'sku'					=>	$superProduct->getSku(), 
			'description'			=>	$superProduct->getDescription(),  
			'weight'				=>	$superProduct->getWeight(),
			'status'				=>	Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
			'tax_class_id' 			=> 	$superProduct->getTaxClassId(),
			'visibility'			=>	Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE, 
			'price'					=> 	$superProduct->getPrice(),  
			'stock_data'			=> 	$this->_getDefaultStockData(100),  
			'website_ids' 			=>  $superProduct->getWebsiteIds(),
			'oem' 					=>  $superProduct->getOem(),
			'simple_sku' 			=>  $superProduct->getSimpleSku(),
		);  
		 */
	}
	
	protected function _prepareProduct($productRow = array()){
		$productArray  = array();
		$priceFields = $this->getPriceFields();    
		$superAttributes = array();
		$simpleAttributes = array();
		$skipAttributes = array('tax_class_id','status','visibility');
		$configurable = false;
		
		if($this->_productField && count($this->_productField) > 0) {
		
			foreach($this->_productField as $index => $field) {
				$value = trim($productRow[$index]);
				$productArray[$field] = $value;  
			} 
			
			// check configurable product 
			$configurableType = @$productArray['configurable_type'];   
			if($configurableType) {
				$configurableType = explode(',', $configurableType);
				$configurableType = array_filter($configurableType); 
				 
				$combineArray = array_combine($this->_csvHeader, $productRow);
				foreach($configurableType as $code) {  
					$fileds = $this->getFields(); 
					$attributeField = $fileds[trim($code)];
					if(!$attributeField || $attributeField == '') {
						$attributeField =  strtolower(trim($code));
						$productArray[strtolower(trim($code))] = $combineArray[trim($code)]; 
					}
					
					$superAttributes[] = $attributeField;
				}    
				$superAttributes = array_filter($superAttributes); 
				$skipAttributes = array_merge($skipAttributes, $superAttributes);
				if(count($configurableType) == count($superAttributes)){
					$configurable = true;    
				} 
			} 
			// end check configurable product  
			$productArray = array_merge($this->_getDefaultData(), $productArray);
			 
			$sku = @$productArray['sku']; 
					
			$productId = $this->getProduct()->getIdBySku($sku);  
			
			$_product = $this->getProduct();
			
			if($productId) {
				$_product->load($productId);  
			} 
			 
			foreach($productArray as $field => $value) {  
				 	
				if(in_array($field, $priceFields)) {
					 
					$value = $this->_filterPrice($value); 
					
				} else  if ($field == 'qty') {
					
					$field  = 'stock_data'; 
					
					if($productId) { 
						$stock = $_product->getStockItem()->getData();  
						$stock['qty'] = $this->getQty($value);
						$value = $stock;	 
					} else {
						$value = $this->_getDefaultStockData($value);
					}
					
				} else if ($field == 'tax_class_id') {
				
					$value = 2; // Taxbale Goods
					
				} else if ($field == 'category_ids') { 
					$value = array($this->_getCategoryIds($value)); 
					
				} else if($field == 'status' || $field == 'freeshipping' || $field == 'promotion' || $field == 'callforprice' || $field == 'googlecheckout') {
					
					if(strtolower($value) == 'yes') {
						$value = 1; 
					} else {
						$value = 2; 
					}
				} else if($field == 'description'){ 
					
					$value = $this->cleanHtml($this->convertSpecialChar($value));  
					
				} else if($field == 'grouped_link_data'){
					// For Grouped Product
					$_grpSkus = array_filter(explode('::',$value));
					if(count($_grpSkus) > 0){  
						$groupData = array(); 
						
						foreach($_grpSkus as $_gSkuu) {
							
							$groupData =  array_filter(explode('|',$_gSkuu));
							$product_id = $this->getProduct()->getIdBySku(@$groupData[0]);
							if($product_id) { 
								$_grpData[$product_id] = array('qty' => '', 'position' => @$groupData[1]);
							}  
						}
						
						$value = $_grpData; 
						$_product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
					}
				} else if($field == 'simple_sku'){
					
					$value = array_unique(array_filter(explode('::', $value)));
				} else if($field == 'pricerange'){
					
					$value = preg_replace('/[^0-9.-]/u', '', $value);
				}
				
				if(!in_array($field, $skipAttributes)) {
					$attribute = $this->getAttribute($field);	
					if($attribute && $attribute->getId()) {   
						$type = strtolower($attribute->getFrontendInput()); 
						if($type == 'select' || $type == 'multiselect') {  
							$value = $this->getAttributeOptionId($field, $value);
						} else if($type == 'boolean') {    
							if(strtolower($value) == 'yes'){
								$value = 1;
							} else {
								$value = 0;	
							} 
						} 
					}
				}
				$_product->setData($field, $value);
			}
			 
			// Google Description
			/*if($_product->hasData('description')) {
				
				$_product->setData('short_description', strip_tags($_product->getDescription()));
			}*/
			// end Google Description 
			if($configurable && $superAttributes){
			
				$_product->setSuperAttribute($superAttributes);
				$_product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
			}
			
			//$this->updateAdditionalData($_product);
			
		}
		return $_product;

	}
	
	public function updateAdditionalData($_product){
		
		$cost = (float) $_product->getCost(); 
		$price = $msrp = $cost;
		
		$pricePercent = (float) Mage::getStoreConfig('custom/import/price', Mage::app()->getStore());
		
		if($pricePercent > 0) {
			
			$price = ($cost * $pricePercent); 
		} 
		
		$msrpPercent = (float) Mage::getStoreConfig('custom/import/msrp', Mage::app()->getStore()); 
		
		if($msrpPercent > 0) {
			
			$msrp = ($cost * $msrpPercent);
		} 
		
		$_product->setMsrp($msrp);
		
		$_product->setPrice($price);
		
		$dimension = '';
		 
		$dimension .=  (float) $_product->getWidth()."\" W x ". (float) $_product->getDepth()."\" D x ";
		$dimension .=  (float) $_product->getHeight()."\" H x ". (float) $_product->getLength()."\" L"; 
		
		$_product->setDimension($dimension);
		
		return $this;   
	}
	
	public function convertSpecialChar($str) {
		 
		 $str =  preg_replace("/[\x80-\xFF]/", '', $str); 
		 $str =  preg_replace("/[\xEF\xBB\xBF]/", '', $str); 
		 $str =  preg_replace("/[\x95]/", '', $str);  
		 return $str;  
	}
	
	public function cleanHtml($src){ 
		
		libxml_use_internal_errors(true);
		
		$x = new DOMDocument;
		$x->loadHTML('<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />'.$src);
		$x->formatOutput = true;
		$ret = preg_replace('~<(?:!DOCTYPE|/?(?:html|body|head))[^>]*>\s*~i', '', $x->saveHTML());
		return trim(str_replace('<meta http-equiv="Content-Type" content="text/html;charset=utf-8">','',$ret));

	}
	  
	public function getCategoryArray(){
		if($this->_categories == null) {
			$catCollection = Mage::getResourceModel('catalog/category_collection')->addAttributeToSelect('name');
			foreach($catCollection as $category) {    
				$this->_categories[] = array('name'=>$category->getName(), 'id'=>$category->getId());
			}
		}
		return $this->_categories;
	}
	
	protected function _getCategoryIds($value){ 
		$categoryIds = array(); 
		$collection = $this->getCategoryArray();
		if($value) {  
			$categories = explode('/', $value);
			if($categories && count($categories) > 0) {
				$categories[0] = 'Default Category';
				foreach($categories as $index => $name) {   
					$name = trim($name); 
					$found = false;
					
					foreach($collection as $cat){
						if(strtolower($cat['name']) == strtolower($name)) {  
							$id = $cat['id']; 
							$found = true;
							if($index > 0) {
								$currentCat = Mage::getModel('catalog/category')->load($id);
								if($categoryIds[$index-1] == $currentCat->getParentId()) {
									break;
								} else {
									$found = false;  
								} 
							} else {
								break;
							}
						} 
					}
					if(!$found) {
						$category = Mage::getModel('catalog/category');
						$category->setName($name);  
						$category->setIsActive(1);
						$category->setIncludeInMenu(1);
						$category->setDisplayMode('PRODUCTS');
						$category->setParentId();
						$parentCategory = Mage::getModel('catalog/category')->load($categoryIds[$index-1]);
						$category->setPath($parentCategory->getPath());
						try{ 
							$category->save();  
							if($category->getId()){
								$this->_categories[] = array('name'=>$category->getName(), 'id'=>$category->getId());
							}
							$id = $category->getId();
						}catch(Exception $e){ echo $e->getMessage();} 
					} 
					$categoryIds[$index] = $id;
				}
			} 
		} 
		return $categoryIds[count($categoryIds)-1];
	}
	
	protected function getAttributeOptionId($field, $value){ 
		$attribute = $this->getAttribute($field);	  
		if(!$value || !$attribute || !$attribute->getId()) {
			return ''; 
		}
		$optionId = '';
		$options = $attribute->getSource()->getAllOptions(false);
		if($options && count($options) > 0) {
			foreach($options as $option){
				if(trim($option['label']) ==  $value){
					$optionId = $option['value'];
					break;
				}
			}
		} 		
		if(!$optionId) {
			$option = '';
			$option['attribute_id'] = $attribute->getAttributeId();
			$option['value']['option'][0] = $value;
			$option['value']['option'][1] = $value;  
			$option['order']['option'] = ''; 
			$this->_getSetup()->addAttributeOption($option);
			
			$attribute = $this->getAttribute($field);
			$options = $attribute->getSource()->getAllOptions(false);
			if($options && count($options) > 0) {
				foreach($options as $option){
					if(trim($option['label']) ==  $value){
						$optionId = $option['value'];
						break;
					}
				}
			} 
		} 
		return $optionId;
		
	}
	
	protected  function _getDefaultStockData($qty){  
		return array(
			'use_config_manage_stock'=>1,
			'is_in_stock'=>1,  
			'qty' => $this->getQty($qty),
		);
	}
	
	protected function _filterPrice($value){  
		$price = preg_replace('/[^0-9.]/u', '', $value);
		return  (float) $price;
	} 
	
	protected function _getDefaultData(){
		if($this->_defaultData == null) {
			$this->_defaultData = array(
				'website_ids' 		=> array(Mage::app()->getStore(true)->getWebsite()->getId()),
				'store_id' 			=> Mage::app()->getStore()->getId(),
				'attribute_set_id' 	=> Mage::getModel('catalog/product')->getDefaultAttributeSetId(),
				'type_id' 			=> Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
				'visibility'		=> Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
				'is_salable'		=> true,
				
			);
		}
		return $this->_defaultData; 
	}
	
	public function getQty($qty){
	
		return $qty?$qty:100;
	}
	
	public function getPriceFields(){
	
		return  array('price');
	}
	
	public function getFields(){
	
		/* 'CSV Field'     			=> 'Product Field' */  
		$fields = array(   
			'Name'					=> 'name',
			'Color'					=> 'color',
			'Description'			=> 'description',
			'Short'					=> 'short_description',
			'SKU'					=> 'sku',
			'Weight'				=> 'weight',
			'Active'				=> 'status',
			'FreeShipping' 			=> 'is_free_shipping',
			'Promotion'            	=> 'promotion',
			'CallForPrice'			=> 'call_for_price',
			'Price'					=> 'price',
			'GoogleCheckout' 		=> 'enable_googlecheckout',
			'Taxable'				=> 'tax_class_id',
			'MetaTitle'				=> 'meta_title',
			'MetaKeywords'			=> 'meta_keyword',
			'MetaDescription' 		=> 'meta_description',
			'Quantity'				=> 'qty',
			'Category'				=> 'category_ids',
			'Configurable'			=> 'configurable_type',
			'SimpleSku'             => 'simple_sku'
		); 
		return $fields;
	}
}
