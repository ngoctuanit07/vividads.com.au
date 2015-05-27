<?php
/**
* 
*/
class FMA_Reviewsplus_Model_Log 
{
 public function reviewsplusOrderLog($order)
	{
		$logPath = Mage::getModuleDir('', 'FMA_Reviewsplus') . DS . 'log'. DS;
        //if the export directory does not exist, create it
        if (!is_dir($logPath)) 
        {
            mkdir($logPath, 0777, true);
        }
        $order_real_id = $order->getRealOrderId();
        $order_id=$order->getEntityId();
		$cutomer_email=trim($order->getCustomerEmail());
		$order_statue= trim($order->getStatus());
		$customer_Name= ucfirst($order->getCustomerFirstname()).' '. ucfirst($order->getCustomerLastname());
		$store_id=$order->getStoreId();
		$timeStamp=time();
		$newline="\n";
		$items= $order->getAllItems();
		$data[]= array(
			'order_id'		=>$order_id, 
			'order_real_id' =>  $order_real_id,
			'customer_Email'=> $cutomer_email,
			'status'		=> $order_statue,
			'timestamp'		=> $timeStamp,
			'template'		=> 'reviewsplus' .$order->getIncrementId().'.html', 
			'customer_Name'=> $customer_Name, 
			'Store_id'=> $store_id);
		foreach ($data as $_data) 
		{
			//to asi
		}
		$xml[]= array();
		$xml= '';
		$xml .= '<root>';
		$xml  .= '<order_id>';
		$xml .= $_data['order_id'];
		$xml .='</order_id>';
		$xml  .= '<order_real_id>';
		$xml .= $_data['order_real_id'];
		$xml .='</order_real_id>';
		$xml  .= '<customer_Name>';
		$xml .= $_data['customer_Name'] ;
		$xml .= '</customer_Name>';
		$xml  .= '<customer_Email>';
		$xml .=$_data['customer_Email'];
		$xml .= '</customer_Email>';
		$xml  .= '<status>';
		$xml .= $_data['status'];
		$xml .= '</status>';
		$xml  .= '<timestamp>';
		$xml .= $_data['timestamp'] ;
		$xml .= '</timestamp>';
		$xml  .= '<template>';
		$xml .= $_data['template'] ;
		$xml .= '</template>';
		$xml  .= '<store_id>';
		$xml .= $_data['Store_id'] ;
		$xml .= '</store_id>';
		$xml .= '</root>';
		
        file_put_contents(
            $logPath. DS . 'reviewsplus'  .$order->getIncrementId().'.xml', 
            $xml
        );
			/*email body section*/
        $productscount=1;
        $feedbackproductscount = 0;
        $emailimage=  trim(Mage::getStoreConfig('reviewsplus/feedback_config/feedback_email_icon'));
        $feedbackproducts = array();
        try
        {
        	foreach ($items as $itemid => $item) 
        	{
        		$productsincart = Mage::getModel('catalog/product')->load($item->getProductId());
				/*check product visibility*/
				if ($productsincart->getVisibility()!="4") //if not visibile
				{
					//check if invisible product is a child in group product
					$parentIdGrouped = Mage::getModel('catalog/product_type_grouped')
						->getParentIdsByChild( $productsincart->getId() );
					$parentIdConfigurable = Mage::getModel('catalog/product_type_configurable')
						->getParentIdsByChild( $productsincart->getId() );	
					// use parent product if parent is grouped or configurable otherwise move on, these are not the products you are looking for...
					if (!empty($parentIdGrouped[0])) // check for grouped product parent
					{
						$productsincart = Mage::getModel('catalog/product')->load($parentIdGrouped[0]);
						if($productsincart->getTypeId() != "grouped") 
						{ 
						continue; 
					} 
					} else if (!empty($parentIdConfigurable[0])) 
					 { 
						// check for configurable product parent
						$productsincart = Mage::getModel('catalog/product')->load($parentIdConfigurable[0]);
						if($productsincart->getTypeId() != "configurable") { continue; } // paranoia

					}
				}
				// load the visible cart products into array
				if ($productsincart->getVisibility()=== "4")
				{
					$feedbackproducts[]=$productsincart->getId();
				}	

        	}

        	// clean up feedback product array to avoid duplicate products
			$feedbackproducts = array_unique($feedbackproducts);
			
			// get max feedback items
			$maxFeedbackItems=(int)Mage::getStoreConfig('getcustomerfeedback_section1/general/max_feedback_items',$store_id);
			
			$emailHtml=null;
			$emailHtml=$emailHtml. '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #EAEAEA;">'. $newline;	
			$emailHtml=$emailHtml. '<tbody bgcolor="#F6F6F6">'. $newline;	;
			
			if (!is_numeric($maxFeedbackItems)) {
				$maxFeedbackItems=0;
			}
			

			// create the product html
			foreach ($feedbackproducts as $key => $item)
			{
			
				$productsincart = Mage::getModel('catalog/product')->load($item);
				
				// get product attributes
				$productsincartID=$productsincart->getId();
				$productsincartName=utf8_decode($productsincart->getName());
				$productsincartImageURL=$productsincart->getImageUrl();
				$productsincartImageURL=str_replace("https","http",$productsincartImageURL);
				$productsincartVisibility=$productsincart->getVisibility();
				
				if ($productsincart->getVisibility()=== "4") // products must be visible in search and catalogue
				{
					$emailHtml=$emailHtml. '<tr>'. $newline;
					$emailHtml=$emailHtml. '<td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">'. $productscount. '</td>'. $newline;
					$emailHtml=$emailHtml. '<td align="center" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;"><img height="64" width="64" src="'. $productsincartImageURL. '"></td>'. $newline;
					$emailHtml=$emailHtml. '<td align="left" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;">'. htmlentities($productsincartName). '</td>'. $newline;
					
					if (empty($emailimage))
					{
						$emailHtml=$emailHtml. '<td align="center" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;"><a href="'. Mage::getUrl('review/product/list/id/'. $productsincartID . '/#review-form') .'">Leave Feedback</a></td>'. $newline;
					} else {
						$emailHtml=$emailHtml. '<td align="center" valign="top" style="font-size:11px; padding:3px 9px; border-bottom:1px dotted #CCCCCC;"><a href="'. Mage::getUrl('review/product/list/id/'. $productsincartID . '/#review-form') . '"><img src="'. $emailimage. '"></a></td>'. $newline;
					}
					
					$emailHtml=$emailHtml. '</tr>'. $newline;
					
					// increment counters
					$productscount ++;
					$feedbackproductscount ++;
				}
				
				
				if ($maxFeedbackItems > 1) 	// check max feedback item control, must be greater than 1 or feedback is empty.
				{
					
					if ($feedbackproductscount >= $maxFeedbackItems)
					{
						break;
					}
					
				}
			}

			$emailHtml=$emailHtml. '</tbody>'. $newline;
			$emailHtml=$emailHtml. '</table>'. $newline;

			if ($feedbackproductscount===0)
			{ 
				throw new Exception('No valid products to use for customer 
					feedback could be found in the cart for 
					order '. $orderId. '.'); 
			}
			 file_put_contents(
	            $logPath. DS . 'reviewsplus' .$order->getIncrementId().'.html', 
	            $emailHtml
	        );
		}catch (Exception $e) 
        {
			if (empty($e))
			{
				echo "string";
			} else {
		   		Mage::logException($e);
			}
		}
        return true;
	}
}
?>

  