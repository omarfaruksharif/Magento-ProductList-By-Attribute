<?php

class Omar_ProductByAttribute_Block_List extends Mage_Catalog_Block_Product_List
{
    
    protected $_itemCollection = null;

    protected $_attr = null;
    protected $_attrValue = null;

    public function __construct() {
        parent::__construct();
        
        /*
         *  We are recieving the attribute code value from URL in this 
         *  format: http://example.com/prodattr/attribute/manufacturer/6
         *  Here, "manufacturer" is the attribute code and "6" is the attribute value
         */
        $params = $this->getRequest()->getParams();

        if(!empty($params)) {
            $paramsKeys = array_keys($params);

            if(!empty($paramsKeys[0])) {
                $this->_attr = $paramsKeys[0];
                $this->_attrValue = $params[ $this->_attr ];
            }
        }
    }

    public function getAttr() {
        return $this->_attr;
    }

    public function getAttrValue() {
        return $this->_attrValue;
    }

    public function getItems()
    {
        if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
            return false;
        }

        if (is_null($this->_itemCollection)) {
            /* Get Products collection Using our Products Model */
            //$this->_itemCollection = Mage::getModel('omar_productbyattribute/products')->getItemsCollection( $this->getAttr(), $this->getAttrValue() );
            
            /* Get Products collection Using Magento Core Products Model */
            $this->_itemCollection = $this->getProductCollection();
        }

        return $this->_itemCollection;
        //return false;
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
            return null;
        }
        return $this->getChildHtml('toolbar');
    }

    public function _getProductCollection()
    {

        try {
            if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
                return null;
            }

            $collection = Mage::getResourceModel('catalog/product_collection');
            
            /*
             *  If there are no records returning though there are products
             *  in the store, try commenting the following line.
             */
            $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

            $collection = $this->_addProductAttributesAndPrices($collection)
            //$collection
                ->addStoreFilter()
                ->addAttributeToFilter( $this->getAttr(),  $this->getAttrValue() )
                ->addAttributeToSort('news_from_date', 'desc')
                ->setPageSize($this->getProductsToDisplayPerPage())
                ->setCurPage($this->getCurrentPageNumber())
            ;

            $this->setProductCollection($collection);

            return $collection;
        } catch(Exception $e) {
            //header('Localtion: /');
            echo $e->getMessage();
        }

    }


    public function getProductsToDisplayPerPage() {
        return 12;
    }

    public function getCurrentPageNumber() {
        return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
    }


    public function getLoadedProductCollection() {
        return $this->getItems();
        //return false;
    }

    
    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChild('product_list_toolbar_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';
    }


    public function getPageTitle() {
        if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
            return false;
        }

        return $this->__( ucfirst($this->getAttr()) . ': ' . $this->getAttributeLabel(  ) );
    }

    public function getAttributeLabel() {

        if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            //->setStoreId( Mage_Core_Model_App::ADMIN_STORE_ID )
            ->{'set'.$this->getAttr()}( $this->getAttrValue() ); // not loading the product - just creating a simple instance

        $label = $product->getAttributeText( $this->getAttr() );

        return $label;
        
        //return '';
    }

    public function getBreadcrumb() {
        if( empty( $this->getAttr() ) && empty( $this->getAttrValue() ) ) {
            return false;
        }

        // get breadcrumbs block
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        // add first item with link
        $breadcrumbs->addCrumb(
            'home',
            array(
                'label'=>$this->__('Home'),
                'title'=>$this->__('Home'),
                'link'=>Mage::getBaseUrl()
            )
        );
        // add second item without link
        $breadcrumbs->addCrumb(
            'attribute',
            array(
                'label'=>$this->getPageTitle(),
                'title'=>$this->getPageTitle()
            )
        );

        return $breadcrumbs;
    }


}
