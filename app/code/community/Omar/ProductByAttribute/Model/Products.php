<?php

class Omar_ProductByAttribute_Model_Products extends Mage_Catalog_Model_Product
{
    public function getItemsCollection($attr, $attrValue)
    {
        /*$collection = $this->getCollection()
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->get_prod_count())
            ->setCurPage($this->get_cur_page())
            //->addAttributeToFilter('manufacturer', array('eq' => $valueId))
        ;*/

        
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSelect('*')
            //->addAttributeToSort('news_from_date', 'desc')
        ;


        if( !empty($attr) && !empty($attrValue) ) {

            $collection->addFieldToFilter(array(
                array( 'attribute' => $attr, 'eq' => $attrValue),
            ));

        }


        $this->setProductCollection($collection);

        return $collection;
        
    }

}
