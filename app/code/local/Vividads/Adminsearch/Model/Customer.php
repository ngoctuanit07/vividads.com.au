<?php

/**
 * Search Customer Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author     Asif Sajjad <asifsajjad@gmail.com> 
 */
class Vividads_Adminsearch_Model_Customer extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Customer
     */
    public function load(Array $attributes)
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
	foreach($attributes as $attr){
		$query=$this->getQuery();
		if(strpos($attr,"_") === 0)
			$query="%".$query;
                if(strrpos($attr,"_") > 0)
			$query.="%";
		$attr=trim($attr,"_");
		$queryattr[]=array('attribute'=>$attr, 'like' =>$query);
	}

        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
            ->joinAttribute('billingtelephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->addAttributeToFilter($queryattr)
            ->setPage(1, 10)
            ->load();

        foreach ($collection->getItems() as $customer) {
            $arr[] = array(
                'id'            => 'customer/1/'.$customer->getId(),
                'type'          => Mage::helper('adminhtml')->__('Customer'),
                'name'          => $customer->getName(),
                'email'          => $customer->getEmail(),
                'description'   => $customer->getCompany(),
                'telelphone'   => $customer->getBillingtelephone(),
                'store'   =>Mage::app()->getStore( $customer->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id'=>$customer->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
