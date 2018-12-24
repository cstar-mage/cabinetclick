<?php

class Kloc_Import_Model_Import extends Mage_Core_Model_Abstract
{
    protected $_fileName = null;
	 
	protected $_setup = null;
	
	protected $_conn = null;
	
	protected $_file = null;
	 
	protected $_productField = array();
	
	protected $_post = null;
	
	protected $_csvHeader = null;
	  
	public function __construct(){
		umask(0);
		set_time_limit(0);	
		ini_set("memory_limit","1G");
		$this->_post = new Varien_Object(Mage::app()->getRequest()->getPost());
		$this->_saveFile();	 
	}
	
	public function getPost(){
	
		return $this->_post;
	}
	
	protected function _saveFile(){
		
		if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {    
			$uploader = new Varien_File_Uploader('file');   
			$uploader->setAllowedExtensions('csv');
			$uploader->setAllowRenameFiles(false);       
			$result = $uploader->save($this->getCsvPath(), $_FILES['file']['name'] );  
			$this->_fileName = $result['file'];  				 		  
		} 
	} 
	
	public function productTypeArray(){
		
		return array(
			Mage_Catalog_Model_Product_Type::TYPE_SIMPLE => Mage::helper('import')->__('Simple'),
			Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE => Mage::helper('import')->__('Configurable'),
		);
	}  
	
	public function getFilePath(){
	
		return $this->getCsvPath().$this->getFileName();
	}
	
	public function getFileName(){
	
		return $this->_fileName;
	}
	
	public function getFile(){ 
		if($this->_file == null){ 
			if($this->getFileName() == null) { 
				
				Mage::throwException(Mage::helper('import')->__('Please upload file.'));   
			} 
			
			if(!file_exists($this->getFilePath())) {
				Mage::throwException(Mage::helper('import')->__('File does not exist.'));  
			}
			
			$this->_file = fopen($this->getFilePath(), 'r');  
			
			$fields = fgetcsv($this->_file);   
			$this->_csvHeader = $fields;
			$this->_prepareField($fields);
			
			if(!count($this->_productField)) { 
				Mage::throwException(Mage::helper('import')->__('Please set field or csv column header not defined.'));
			}
		}
		return  $this->_file;
	} 
	
	public function getAttribute($code){
		return Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $code); 
	}
	
	public function getProduct(){
	
		return Mage::getModel('catalog/product');
	}
	protected function _getSetup(){ 
		if($this->_setup == null) { 
			$this->_setup = Mage::getModel('eav/entity_setup', 'core_setup');
		} 
		return $this->_setup;
	}
	protected function _getConnection(){
		if($this->_conn == null){
			$this->_conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		}
		return $this->_conn;
	} 
	public function getCsvPath(){
	
		return  Mage::getBaseDir(). DS .'product'. DS. 'csv'. DS; 
	} 
	protected function _prepareField($csvFields){
	  	
		$fields = $this->getFields();	  
		foreach($csvFields as  $index => $field) {
			if(array_key_exists(trim($field), $fields)) {
				$this->_productField[$index] = $fields[trim($field)];
			}				
		} 
		return $this;
	} 
}	  
