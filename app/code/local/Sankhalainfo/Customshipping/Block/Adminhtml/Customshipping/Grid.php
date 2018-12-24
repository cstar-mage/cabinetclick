<?php

class Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
  public function __construct() {
	  
      parent::__construct();
      $this->setId('customshippingGrid');
      $this->setDefaultSort('customshipping_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
	  
      $collection = Mage::getModel('customshipping/customshipping')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
	   
      $this->addColumn('radius', array(
          'header'    => Mage::helper('customshipping')->__('Radius'),
          'align'     =>'left',
          'index'     => 'radius',
          'width'	 => '175px'
      ));
      
      $this->addColumn('price', array(
          'header'    => Mage::helper('customshipping')->__('Price'),
          'align'     =>'left',
          'index'     => 'price',
          'type'	  => 'price',
          'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),	 
          'width'	 => '100px',
          'style'	 => ' word-break: break-all;',
      ));
      
      $this->addColumn('postcode', array(
          'header'    => Mage::helper('customshipping')->__('Postcode'),
          'align'     =>'left',
          'index'     => 'postcode',
          'renderer'  => 'Sankhalainfo_Customshipping_Block_Adminhtml_Customshipping_Grid_Renderer'
      )); 
      
      /*	
      $this->addColumn('status', array(
          'header'    => Mage::helper('customshipping')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));*/
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customshipping')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customshipping')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('customshipping')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('customshipping')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customshipping_id');
        $this->getMassactionBlock()->setFormFieldName('customshipping');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customshipping')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customshipping')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('customshipping/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('customshipping')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('customshipping')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
