<?php
class Kloc_Import_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/import?id=15 
    	 *  or
    	 * http://site.com/import/id/15 	
    	 */
    	/* 
		$import_id = $this->getRequest()->getParam('id');

  		if($import_id != null && $import_id != '')	{
			$import = Mage::getModel('import/import')->load($import_id)->getData();
		} else {
			$import = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($import == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$importTable = $resource->getTableName('import');
			
			$select = $read->select()
			   ->from($importTable,array('import_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$import = $read->fetchRow($select);
		}
		Mage::register('import', $import);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}