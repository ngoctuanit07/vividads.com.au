<?php

class MDN_Quotation_Model_Quotation_Notification extends Mage_Core_Model_Abstract {

    /**
     * Send email to customer
     */
    public function NotifyCustomer($quote) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $templateId = Mage::getStoreConfig('quotation/quote_notification/email_template', $quote->getCustomer()->getStoreId());
        $identityId = Mage::getStoreConfig('quotation/quote_notification/email_identity', $quote->getCustomer()->getStoreId());

        //Create array for used variables in email template
        $data = array(
            'subject' => Mage::helper('quotation')->__('New quote available'),
            'caption' => $quote->getcaption(),
            'name' => $quote->getCustomer()->getName(),
            'customer_email' => $quote->getCustomer()->getemail(),
            'increment_id' => $quote->getincrement_id(),
            'direct_url' => Mage::helper('quotation/DirectAuth')->getDirectUrl($quote),
        );

        //Attachment
        $Attachments = array();
        if ($quote->GetLinkedProduct() == null) {
            $quote->commit();
        }
        $pdf = Mage::getModel('Quotation/quotationpdf')->getPdf(array($quote));
        $Attachment = array();
        $Attachment['name'] = Mage::helper('quotation')->__('Quotation #') . $quote->getincrement_id() . '.pdf';
        $Attachment['content'] = $pdf->render();
        $Attachments[] = $Attachment;

        //add custom attachment
        if (Mage::helper('quotation/Attachment')->attachmentExists($quote)) {
            $attachmentPath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
            if (file_exists($attachmentPath)) {
                $customAttachment = array();
                $customAttachment['name'] = $quote->getadditional_pdf() . '.pdf';
                $customAttachment['content'] = file_get_contents($attachmentPath);
                $Attachments[] = $customAttachment;
            }
        }

        //send email
        if (!empty($templateId))
            Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $quote->getCustomer()->getemail(),
                            $quote->getCustomer()->getname(),
                            $data,
                            null,
                            $Attachments);
        else
            throw new Exception('Template Transactionnel Empty');


        //store notification date
        $quote->setnotification_date(date('y-m-d h:i'))
                ->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
                ->save();
        $translate->setTranslateInline(true);

        //add notification in history
        $quote->addHistory(Mage::helper('quotation')->__('Customer notified'));

        return $quote;
    }

    /**
     * Send an email to store manager to notify about new quote request
     */
    public function NotifyCreationToAdmin($quote) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $templateId = Mage::getStoreConfig('quotation/quotation_request/email_template', $quote->getCustomer()->getStoreId());
        $identityId = Mage::getStoreConfig('quotation/quotation_request/email_identity', $quote->getCustomer()->getStoreId());
        $sendTo = Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStoreId());
        
        
        /****************************************** Start by dev **********************************************/
        
        $customerData = Mage::getModel('customer/customer')->load($quote->getCustomer()->getId())->getData();
        $sendTo = $customerData['email'];
                
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
	$sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
         
         try {
                 $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		 $fetchBilling= $chkSystem->fetch();
         } catch (Exception $e){
         echo $e->getMessage();
         }
         
         $billing = '';
         $shipping = '';
         
         $billing .= $fetchBilling['firstname'].' '.$fetchBilling['lastname']."<br/>";
        if($fetchBilling['street1'] != '')
	 $billing = $fetchBilling['street1']."<br/>";
	
	if($fetchBilling['street2'] != '')
	 $billing .= $fetchBilling['street2']."<br/>";
	
	if($fetchBilling['city'] != '')
	$billing .= $fetchBilling['city'].",";
	
	if($fetchBilling['region'] != '')
	$billing .= $fetchBilling['region'].",";
	
	
	if($fetchBilling['region_id'] != '')
	$billing .= $fetchBilling['region_id'].",";
	
	if($fetchBilling['postcode'] != '')
	$billing .= $fetchBilling['postcode']."<br/>";
	
	if($fetchBilling['country_id'] != '')
	$billing .= $fetchBilling['country_id']."<br/>";
	
	if($fetchBilling['telephone'] != '')
	$billing .= 'T:'.$fetchBilling['telephone'];
        
        
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_shipping');
	$sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
         
         try {
                 $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlSystem);
		 $fetchShipping= $chkSystem->fetch();
         } catch (Exception $e){
         echo $e->getMessage();
         }
         
         
         $shipping .= $fetchShipping['firstname'].' '.$fetchShipping['lastname']."<br/>";
        if($fetchShipping['street1'] != '')
	 $shipping = $fetchShipping['street1']."<br/>";
	
	if($fetchShipping['street2'] != '')
	 $shipping .= $fetchShipping['street2']."<br/>";
	
	if($fetchBilling['city'] != '')
	$shipping .= $fetchBilling['city'].",";
	
	if($fetchShipping['region'] != '')
	$shipping .= $fetchShipping['region'].",";
	
	
	if($fetchShipping['region_id'] != '')
	$shipping .= $fetchShipping['region_id'].",";
	
	if($fetchShipping['postcode'] != '')
	$shipping .= $fetchShipping['postcode']."<br/>";
	
	if($fetchShipping['country_id'] != '')
	$shipping .= $fetchShipping['country_id']."<br/>";
	
	if($fetchShipping['telephone'] != '')
	$shipping .= 'T:'.$fetchShipping['telephone'];
        
        
        $carrierTitle =  end(explode('_',$quote->getShippingMethod()));
        $shipping_method = Mage::getStoreConfig('carriers/'.$carrierTitle.'/title');
        $total = 0;
        $itemHtml = '';
	$all_attachments='';
        foreach ($quote->getItems() as $item)
        {
            $_product =Mage::getModel('catalog/product')->load($item->getproductId());
               		
		$productid=$item->getproductId();
		$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('downloads_relation');
		$sqlSaleOrderGrid="select * from ".$temptableSaleOrderGrid." WHERE product_id='".$productid."'";
		try {
		$chkSaleOrderGrid = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlSaleOrderGrid);
		} catch (Exception $e){
		//echo $e>getMessage();
		}

		foreach($chkSaleOrderGrid as $res_objects) 
		{
			//echo $res_objects["file_id"]."<br><br>";	
			$temptableSaleOrderGrid=Mage::getSingleton('core/resource')->getTableName('downloads_files');
			$sqlSaleOrderGrid="select * from ".$temptableSaleOrderGrid." WHERE file_id='".$res_objects["file_id"]."'";
			try {
			$chkSaleOrderGrid2 = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlSaleOrderGrid);
			} catch (Exception $e){
			//echo $e>getMessage();
			}
			foreach($chkSaleOrderGrid2 as $res_objects2) 
			{
				$all_attachments.='<a target="_blank" href="'.Mage::getBaseUrl().'downloads/dl/file/id/'.$res_objects2["file_id"].'/'.$res_objects2["filename"].'"><strong>'.$_product->getSku().'</strong></a><br>';
			}


		}

		


            $itemHtml .= '<tr>
    <td style="padding:7px 9px 9px 9px;vertical-align:top;border-top:1px solid #e0e0e0">
    	<table width="100%" cellspacing="0" cellpadding="0" border="0">
    		<tbody>
	    		<tr>
	    		<td style="width:125px;padding:3px 9px 9px;border-bottom:none;vertical-align:top">
			    <a target="_blank"  href="'.$_product->getProductUrl().'"><img width="125" height="125" src="'.$_product->getImageUrl().'"  /></a>
			</td>
			<td style="width:260px;padding:0px 9px 9px;border-bottom:none;vertical-align:top">
				<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0">
				        				            <a target="_blank" href="'.$_product->getProductUrl().'" style="color:#2f2f2f;font-size:11px">'.$item->getcaption().'</a>
				        				    </h2>
				    			
			        <div>
			            <div>
			
			                <div style="font-size:10px">
			
			                    <dl>';
                                            
            $productOptions= $item->getOptions();
	    $productOptions = Mage::helper('quotation/Serialization')->unserializeObject($productOptions);
                                            
            foreach ($_product->getOptions() as $option) {
               $itemHtml .= '<dt>'.$option->getTitle().'</dt>';
               $values = $option->getValues();
               
               foreach ($values as $value){
                if($productOptions[$option->getId()] == $value->getId()){
               $itemHtml .= '<dd>1.0000 x '.$value->getTitle().' <span>'.$quote->FormatPrice($value->getPrice()).'</span></dd>';
                    }
               }
            }
            
	$itemHtml .= ' </dl>
			                </div>
			            </div>
			        </div>		        			        
					</div>
					</td>
					<td style="font-size:11px;padding:7px 9px 9px 9px;vertical-align:top;border-top:1px solid #e0e0e0">
					'.$item->getsku().'   </td>
				    
					<td style="font-size:11px;padding:7px 9px 9px 9px;vertical-align:top;border-top:1px solid #e0e0e0">
					'.$item->getqty().'    </td>
					<td style="font-size:11px;padding:7px 9px 9px 9px;vertical-align:top;border-top:1px solid #e0e0e0">
						    <span>
					
							    <span>'.$quote->FormatPrice($item->GetTotalPriceWithTaxes($quote)).'</span>            
					</span>
            </td>
				</tr>
			</tbody>
		</table>
	</td>
	
    
</tr>';
            $total += $item->GetTotalPriceWithTaxes($quote);
        }
	
	$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	$sqlPlanning="SELECT * FROM ".$temptablePlanning." WHERE quote_id = '".$quote->getId()."' AND planning_type = 'quote' ";
	$chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchALL($sqlPlanning);
	$timeHtml = '';
	foreach($chkPlanning as $planning)
	{
	    $Product = Mage::getModel('catalog/product')->load($planning['product_id']);
	    $timeHtml .= ' <tr class="order_placed_by">
                	<td class="request_delivery" align="center" valign="top" style="font-size:12px; " colspan="2">
                 		<strong>'.$Product->getName().'</strong>
                    </td>
              </tr>
                                                        <tr class="order_placed_by">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Order Placed By:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['order_placed_date'])).'
                    	                	                    </td>
              </tr>
                                                        <tr class="artwork_submitted_by">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Artwork Submitted By:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['artwork_date'])).'
                    	                   	<span class="tip">*</span>                    </td>
              </tr>
                                                        <tr class="proof_approval">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Proof Approval Date:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['proof_date'])).'
                    	                  	<span class="tip">**</span>                    </td>
              </tr>
                                                        <tr class="production_start">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Production Start:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['start_date'])).'
                    	                 	                    </td>
              </tr>
                                                        <tr class="shipping_on">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Shipping On:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['shipping_date'])).'
                    	                  	                    </td>
              </tr>
                                                        <tr class="delivery_on">
                	<td class="left" align="right" valign="top" style="font-size:12px; ">
                 		<strong>Delivery On:</strong>
                    </td>
                    <td class="right" valign="top" style="font-size:12px;">'.date('d/m/Y',strtotime($planning['delivery_date'])).'
                    	             	                    </td>
              </tr>
	      ';
         }
        $grand_total = $total + $quote->getShippingRate();
        
        
        $loginurl = Mage::getBaseUrl().'customer/account/login/';
        $artwork = Mage::getBaseUrl().'artwork';
        $quotelink = Mage::getBaseUrl().'Quotation/Quote/View/quote_id/'.$quote->getId().'/';
        $baseurl = Mage::getBaseUrl();
        
        /******************************************* End by dev ********************************************/
	
	//Adding the planninmg time line
	$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	$sqlPlanning="SELECT * FROM ".$temptablePlanning." WHERE quote_id = '".$quote->getId()."' ";
	$chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchALL($sqlPlanning);

        $url = Mage::helper('adminhtml')->getUrl('Quotation/Admin/edit', array('quote_id' => $quote->getId()));
        $data = array
            (
            'subject' => Mage::Helper('quotation')->__('New Quotation Request'),
            'quote' => $quote->getIncrementId(),
            'customerid' => $quote->getCustomer()->getId(),
            'customer' => $quote->getCustomer()->getName(),
            'itemHtml' => $itemHtml,
           // 'subtotal' => $quote->FormatPrice($total),
	   'subtotal' => $quote->FormatPrice(($total-$quote->getShippingRate()-$quote->GetTaxAmount())),
            'shipprice' => $quote->FormatPrice($quote->getShippingRate()),
            'grandtotal' => $quote->FormatPrice($grand_total),
            'billing' => $billing,
            'shipping' => $shipping,
            'inhand' => $fetchShipping['inhand'],
            'shipping_method' => $shipping_method,
            'increment_id' => $quote->getincrement_id(),
            'loginurl' => $loginurl,
            'artwork' => $artwork,
            'quotelink' => $quotelink,
            'baseurl' => $baseurl,
            'url' => $url,
	    'request_date' => date('d/m/Y',strtotime($fetchShipping['inhand'])),
	    'order_placed_date' => date('d/m/Y',strtotime($chkPlanning[0]['order_placed_date'])),
	    'artwork_date' => date('d/m/Y',strtotime($chkPlanning[0]['artwork_date'])),
	    'proof_date' => date('d/m/Y',strtotime($chkPlanning[0]['proof_date'])),
	    'start_date' => date('d/m/Y',strtotime($chkPlanning[0]['start_date'])),
	    'shipping_date' => date('d/m/Y',strtotime($chkPlanning[0]['shipping_date'])),
	    'delivery_date' => date('d/m/Y',strtotime($chkPlanning[0]['delivery_date'])),
	    'created_date' => date('d/m/Y',strtotime($quote->getCreatedTime())),
	    'timehtml' => $timeHtml,
	    'attach'   => $all_attachments,	
	    'tax' => $quote->FormatPrice($quote->GetTaxAmount())
        );

        if (!empty($templateId)) {
            Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $sendTo,
                            null,
                            $data);
        }
        else
            throw new Exception('Template Transactionnel Empty');

        $translate->setTranslateInline(true);

        return $this;
    }

}
