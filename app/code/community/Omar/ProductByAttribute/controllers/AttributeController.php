<?php


class Omar_ProductByAttribute_AttributeController extends Mage_Core_Controller_Front_Action
{

    public $_block;


    //  prodattr/attribute/index/manufacturer/6/?dir=asc&order=price
    public function indexAction()
    {
        $this->loadLayout();

        $this->_block = Mage::getBlockSingleton('omar_productbyattribute/list');
        
        /*
         *  If there is no attribute and attribute value passed to the URL,
         *  we are reditecting the user to Home Page.
         */
        if( empty( $this->_block->getAttr() ) || empty( $this->_block->getAttrValue() ) ) {
            return Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
        }

        $title = $this->_block->getPageTitle();

        if(!empty($title)) {
            $this->getLayout()->getBlock('head')->setTitle( $title );
        }

        $this->_block->getBreadcrumb();

        $this->renderLayout();
    }


}
