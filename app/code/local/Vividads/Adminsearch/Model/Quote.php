<?php

/**
 * Search Quote Model
 *
 * @category    Mage
 * @package     Vividads_Adminsearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vividads_Adminsearch_Model_Quote extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Vividads_Adminsearch_Model_Search_Quote
     */
    public function load(Array $attributes)
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        $collection = Mage::getModel('Quotation/Quotation')->getCollection();
	if(!$collection) exit('invalid class');
	list($k,$attr)=each($attributes);
	if($attr)
	        $collection->getSelect()->where($attr.'= "'.$query.
'%"');

else
	$collection->getSelect()->where('increment_id like "'.$query.
'%"');
        foreach ($collection as $quote) {
            $arr[] = array(
                'id'                => 'quote/1/'.$quote->getId(),
                'type'              => Mage::helper('adminhtml')->__('Quote'),
                'name'              => Mage::helper('adminhtml')->__('Quote #%s', $quote->getIncrementId()),
                'description'       => $quote->getBillingFirstname().' '.$quote->getBillingLastname(),
                'form_panel_title'  => Mage::helper('adminhtml')->__('Quote #%s (%s)', $quote->getIncrementId(), $quote->getBillingFirstname().' '.$quote->getBillingLastname()),
                'url' => Mage::helper('adminhtml')->getUrl('Quotation/Admin/edit', array('quote_id'=>$quote->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
