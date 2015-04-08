<?php
class MDN_Quotation_Block_Adminhtml_Renderer_Messages
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
 *      * Render product name to add Configure link
 *           *
 *                * @param   Varien_Object $row
 *                     * @return  string
 *                          */
    public function render(Varien_Object $row)
  {
        $message_numbers  =  $row->getData($this->getColumn()->getIndex());
		$quote_id = $row->getQuotation_id();
		 
		 //var_dump($message_numbers);
		 $history = Mage::getModel('Quotation/History')->load($quote_id)
		 											   ->getCollection()
													   ->addFieldToFilter('qh_quotation_id',$quote_id)
													   ->addFieldToFilter('qh_readstatus',1)
													   ;
		// var_dump($history->getSelect()->__toString());
		//  var_dump(count($history));
		 if(count($history)>0) { 
		 		$message_numbers=count($history);
		 }else{
			 $message_numbers = 0;
			 }
		$messages = '<a target="_blank" title="click to view quote_id = '.$quote_id.'" href="'.Mage::getBaseUrl().'Quotation/Admin/edit/quote_id/'.$quote_id.'/msg/1/">';
		$messages ='<span style="color:red;">('.$message_numbers.') New Messages </span>';
		$messages .='</a>';
		
		return $messages;
    }
}

