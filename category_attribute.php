<?php
	set_time_limit(0);	 
	require_once 'app/Mage.php';
	if (!Mage::isInstalled()) {
		echo "Application is not installed yet, please complete install wizard first.";
		exit;
	}
	Mage::app('admin')->setUseSessionInUrl(false);
	
    $setup = Mage::getModel('eav/entity_setup', 'core_setup');
	
	$attributes = array(
	
		'door_material' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Door Material', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'style' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Style', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'door_style' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Door Style', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'face_frame' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Face Frame', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'door_frame' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Door Frame', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'door_center' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Door Center', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'cabinet_sides' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Cabinet Sides', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'cabinet_top_bottom' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Cabinet Top & Bottom', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'interior_finish' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Interior Finish', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'back_panel' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Back Panel', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'back_panel' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Back Panel', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),
		'hinges' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Hinges', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'connectors' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Connectors', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'shelves' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Shelves', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'drawer_box' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Drawer Box', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'drawer_bottom' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Drawer Bottom', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'drawer_length' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Drawer Length', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'drawer_box_finish' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Drawer Box Finish', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),
		'drawer_glides' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Drawer Glides', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'corner_supports' => array(
				  'group'             => 'Specs',
				  'type'              => 'varchar', 
				  'input'             => 'text',
				  'label'             => 'Corner Supports', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),
		
		'about' => array(
				  'group'             => 'General Information',
				  'type'              => 'text', 
				  'input'             => 'textarea',
				  'label'             => 'About', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),

		'assembly' => array(
				  'group'             => 'General Information',
				  'type'              => 'text', 
				  'input'             => 'textarea',
				  'label'             => 'Assembly', 
				  'backend'           => '',
				  'frontend'          => '',    
				  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				  'visible'           => true,  
				  'user_defined'      => true,   
				  'visible_on_front'  => false,
				  'required'          => false,
		),



);
	
	foreach($attributes as $attributeCode => $attribute) {
		 $setup->removeAttribute('catalog_category', "specs", $attribute);	
		 $setup->addAttribute('catalog_category', $attributeCode, $attribute);
	}
	//$setup->removeAttribute('catalog_category', 'about');
	//$setup->removeAttribute('catalog_category', 'assembly');
?>
