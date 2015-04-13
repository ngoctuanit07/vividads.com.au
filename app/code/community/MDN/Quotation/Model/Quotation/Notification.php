<?php

class MDN_Quotation_Model_Quotation_Notification extends Mage_Core_Model_Abstract {

    /**
     * Send email to customer
     */
    public function NotifyCustomer($quote) {
	
	
	return $this->NotifyCreationToAdmin($quote);
        //$translate = Mage::getSingleton('core/translate');
        //$translate->setTranslateInline(false);
        //
        //$templateId = Mage::getStoreConfig('quotation/quote_notification/email_template', $quote->getCustomer()->getStoreId());
        //$identityId = Mage::getStoreConfig('quotation/quote_notification/email_identity', $quote->getCustomer()->getStoreId());
        //
        ////Create array for used variables in email template
        //$data = array(
        //    'subject' => Mage::helper('quotation')->__('New quote available'),
        //    'caption' => $quote->getcaption(),
        //    'name' => $quote->getCustomer()->getName(),
        //    'customer_email' => $quote->getCustomer()->getemail(),
        //    'increment_id' => $quote->getincrement_id(),
        //    'direct_url' => Mage::helper('quotation/DirectAuth')->getDirectUrl($quote),
        //);
        //
        ////Attachment
        //$Attachments = array();
        //if ($quote->GetLinkedProduct() == null) {
        //    $quote->commit();
        //}
        //$pdf = Mage::getModel('Quotation/quotationpdf')->getPdf(array($quote));
        //$Attachment = array();
        //$Attachment['name'] = Mage::helper('quotation')->__('Quotation #') . $quote->getincrement_id() . '.pdf';
        //$Attachment['content'] = $pdf->render();
        //$Attachments[] = $Attachment;
        //
        ////add custom attachment
        //if (Mage::helper('quotation/Attachment')->attachmentExists($quote)) {
        //    $attachmentPath = Mage::helper('quotation/Attachment')->getAttachmentPath($quote);
        //    if (file_exists($attachmentPath)) {
        //        $customAttachment = array();
        //        $customAttachment['name'] = $quote->getadditional_pdf() . '.pdf';
        //        $customAttachment['content'] = file_get_contents($attachmentPath);
        //        $Attachments[] = $customAttachment;
        //    }
        //}
        //
        ////send email
        //if (!empty($templateId))
        //    Mage::getModel('Quotation/Core_Email_Template')
        //            ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
        //            ->sendTransactionalWithAttachment(
        //                    $templateId,
        //                    $identityId,
        //                    $quote->getCustomer()->getemail(),
        //                    $quote->getCustomer()->getname(),
        //                    $data,
        //                    null,
        //                    $Attachments);
        //else
        //    throw new Exception('Template Transactionnel Empty');
        //
        //
        ////store notification date
        //$quote->setnotification_date(date('y-m-d h:i'))
        //        ->setstatus(MDN_Quotation_Model_Quotation::STATUS_ACTIVE)
        //        ->save();
        //$translate->setTranslateInline(true);
        //
        ////add notification in history
        //$quote->addHistory(Mage::helper('quotation')->__('Customer notified'));
        //
        //return $quote;
    }



    /**
     * Send an email to store manager to notify about new quote request
     */
    public function NotifyCreationToAdmin($quote) {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $templateId = Mage::getStoreConfig('quotation/quotation_request/email_template', $quote->getCustomer()->getStoreId());
        $identityId = Mage::getStoreConfig('quotation/quotation_request/email_identity', $quote->getCustomer()->getStoreId());
        
		$store_id = $this->getCurrentStoreId($quote->getCustomer()->getWebsite_id());
		$identityId = array('name'=>Mage::getStoreConfig('trans_email/ident_general/name', $store_id),
						    'email'=>Mage::getStoreConfig('trans_email/ident_general/email', $store_id)); 
		
		//$sendTo1 = Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStoreId());
		$sendTo1 = explode(',',Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStoreId()));
        
        
		
        /****************************************** Start by dev **********************************************/
        
        $customerData = Mage::getModel('customer/customer')->load($quote->getCustomer()->getId())->getData();
        $sendTo = $customerData['email'];
		//$sendTo1 = array_merge($sendTo1, array($sendTo));
		//var_dump($sendTo);
                
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
	
	///saving as attachement file/////
		
		
		$attachment_url = Mage::getBaseUrl('media').'attachedfiles/'.$quote->getIncrement_id().'.html';
		$filename = $quote->getIncrement_id().'.html';
		
		/*showing attached files*/
		$_html =Mage::getModel('Quotation/Quotation')->showAttachedFiles($quote->getId());
		
		if($email_attachment !=''){
		$email_contents = str_replace('Retain','<p style="text-align:left;font-weight:bold;">Attachment(s):&nbsp;&nbsp;<a href="'.$attachment_url.'" target="_blank">'.$filename.'</a>'.$_html.'</p>Retain',$email_content[10]);
		}else{
		$email_contents = str_replace('Retain','Retain',$email_content[10]);
			}
		
		
		 
		 $all_attachments .=$email_contents;
		//  echo $all_attachments; exit;
		  
		 
		  
	
        foreach ($quote->getItems() as $item)
        {
			
		 
		$_product = $this->getStoreProduct($item->getProduct_id(), $quote);			 	
		$productid = $_product->getEntity_id();
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
				//var_dump($res_objects2);
				$all_attachments .='<a target="_blank" style="float:left; padding-right:10px;" href="'.Mage::getBaseUrl().'downloads/dl/file/id/'.$res_objects2["file_id"].'/'.$res_objects2["filename"].'"><img style="display: block;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  /><strong>'.$_product->getSku().'</strong></a>';
				$all_attachments .='<div style="font-family: Tahoma, Geneva, sans-serif; font-size: 12px; padding:7px 0px; color:#555;">'.$res_objects2['file_description'].'</div>';
			}


		}
		$product_image = '';
		 if($_product->getThumbnail()!='no_selection'){
			 $product_image = $_product->getThumbnail();
		 }
		 
		 if($_product->getImage()!='no_selection'){
		 	$product_image = $_product->getImage();
		 }
		 
		 if($_product->getSmall_image()!='no_selection'){
		 	$product_image = $_product->getSmall_image();
		 }
		
		 $_image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product_image ;		 
		 $_product_url = $this->getProductStoreUrl($_product->getUrl_path(), $quote->getCustomer()->getWebsite_id()); 
		 $itemHtml .= '<tr>
    <td style="padding:0px 0px 0px 0px;vertical-align:top;">
    	<table width="100%" cellspacing="0" cellpadding="0" border="0">
    		<tbody>
	    		<tr>
	    		<td style="width:125px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-left: 1px solid #EAEAEA;border-right: 1px solid #EAEAEA;">
			    <a target="_blank"  href="'.$_product_url.'">
			    <img width="125" height="90" src="'.$_image_url.'" style="padding-right:2px;" /></a>
			</td>
			<td style="width:220px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top; border-right: 1px solid #EAEAEA;">
				<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0;">
				            <a target="_blank" href="'.$_product_url.'" style="color:#2f2f2f;font-size:11px">'.$item->getcaption().'</a>
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
					</div>';
					
					$itemHtml .='<table cellpadding="0" cellspacing="0">
					<tbody>';
					if($_product->getTypeId()=='bundle')
					{
		$bundle_item = array();
		$bundled_product = Mage::getModel('catalog/product')->load($_product->getId());
		$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
		$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
		    		);
 		$optionCollection =  $bundled_product->getTypeInstance(true)->getOptions($bundled_product);
			foreach($optionCollection as $val)
			   		{
						$itemHtml .='<tr><td align="left" valign="top" style="padding:0" colspan="0" ><strong>'.$val->getDefaultTitle().'</strong></td></tr>';
  						foreach($selectionCollection as $option)
						{
			if($val->getId() == $option->getOptionId())
				   {				    
				    $Product =Mage::getModel('catalog/product')->load($option->getProductId());	
					$itemHtml.='<tr>
					<td >
						<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0; font-weight:normal;text-decoration:none;"><strong style="padding-right:2px;">('.round($option->getSelectionQty()).')</strong>'.$Product->getName().'
				        </h2>	        			        
					</div>
					</td>
					</tr>';
					}
					}
					}
					}
					$itemHtml.='</tbody></table>';
					$itemHtml.='
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA; border-bottom:1px solid #eaeaea; vertical-align:top;">
					'.$item->getsku().'   </td>
				    
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;vertical-align:top; border-bottom:1px solid #eaeaea;">
					'.$item->getqty().'    </td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;vertical-align:top; border-bottom:1px solid #eaeaea;">
						    <span>					
							    <!-- span>'.$quote->FormatPrice($item->GetTotalPriceWithTaxes($quote)).'</span -->  
								<span>'.Mage::helper('core')->currency($item->getPrice_ht()*$item->getQty(), true, false).'</span>          
					</span>
            </td>
				</tr>';
				
		
			/**********************************************19-2-2014 SOC GC*********************************************************/	
/*			if($_product->getTypeID() == 'bundle')
			{
			    $bundle_item = array();
			    $bundled_product = Mage::getModel('catalog/product')->load($_product->getId());
    			    $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
				$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
			   );
			   
			   $optionCollection =  $bundled_product->getTypeInstance(true)->getOptions($bundled_product);
			   
			 
        
			   foreach($optionCollection as $val)
			   {
				$itemHtml .= '<tr><td align="left" valign="top" style="padding:5px 9px" colspan="6" bgcolor="#EAEAEA"><strong><em>'.$val->getDefaultTitle().'</em></strong></td></tr>';
				
				foreach($selectionCollection as $option)
				{
				   if($val->getId() == $option->getOptionId())
				   {
				    
				    $Product =Mage::getModel('catalog/product')->load($option->getProductId());
				    
    				    $itemHtml .= '<tr>
	    		<td style="width:125px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-left: 1px solid #EAEAEA;border-right: 1px solid #EAEAEA;">
			    <a target="_blank"  href="'.$option->getProductUrl().'"><img width="125" height="90" src="'.$Product->getImageUrl().'"  style="padding-right:2px;"/></a>
			</td>
			<td style="width:260px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-right: 1px solid #EAEAEA;">
				<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0">
				            <a target="_blank" href="'.$option->getProductUrl().'" style="color:#2f2f2f;font-size:11px">'.$Product->getName().'</a>
				        </h2>	        			        
					</div>
					</td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
					'.$Product->getsku().'   </td>
				    
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
					'.$option->getqty().'    </td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
						    <span>';
							
					
			 	$itemHtml .='		    <span>'.$Product->getPrice().'</span>';
				$itemHtml .='            
					</span>
            </td>
				</tr>';
				  
				
				   }
				}
				
			   }
			} 

			*/
				
				
			/*************************************************19-2-2014 EOC gc******************************************************/	
				
			$itemHtml .= '</tbody>
		</table>
	</td>
	
    
</tr>';

	//echo $itemHtml; exit;
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
      	$storeurl = $this->getStoreUrl($quote->getCustomer()->getWebsite_id());		
		
		$incrementId = $quote->getIncrement_id();
		$customerid = $quote->getCustomer()->getEntity_id();
		
		$sid =md5('vividexhibits'.$incrementId.$customerid);		
		$authloginurl = $storeurl.'autologin/Directauth/order/quote_id/'.$quote->getIncrement_id().'/SID/'.$sid;
		
		// var_dump('vividexhibits'.$incrementId.'='.$customerid); exit;
		//var_dump($url); 
		
		$pdf_attachment_link = Mage::getUrl().'Quotation/Quote/print/quote_id/'.$quote->getId();		
		$pdf_attachment = '<a style="clear:both;" href="'.$pdf_attachment_link.'" target="_blank" title="Click to download PDF '.$pdf_attachment_link.'"><img style="display: block; clear:both;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  > PDF-'.$quote->getIncrementId().'</a>';
		
		
		
		$data = array
            (
            'subject' => Mage::Helper('quotation')->__('New Quotation Request'),
            'quote' => $quote->getIncrementId(),
            'customerid' => $quote->getCustomer()->getId(),
            'customer' => $quote->getCustomer()->getName(),
            'itemHtml' => $itemHtml,
           // 'subtotal' => $quote->FormatPrice($total),
	        //'subtotal' => $quote->FormatPrice(($total-$quote->getShippingRate()-$quote->GetTaxAmount())),
            'subtotal'=> number_format($quote->getPriceHt(), 2, '.', ''),
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
			'sid'  => $sid,
			'pdfquotelink'=>$pdf_attachment,
			'tax' => $quote->FormatPrice($quote->GetTaxAmount()),
			'athuloginurl'=>$authloginurl,
			);
 
      
		 
	    if (!empty($templateId)) {           
		   
		 /*    
		   $emailtemplate = Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()
					                                                           ->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            null,
                            null,
                            $data);
	   Zend_debug::dump($emailtemplate);
	   Zend_debug::dump($sendTo1)
	   
	   echo $emailtemplate->getTemplate_text();
	    exit;					
	 	*/ 					
	   	
					 
		//var_dump($sendTo1); exit;
	    Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $sendTo,
                            null,
                             $data);
		 
		 Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $sendTo1,
                            null,
                             $data);
		 
		 //
	 	$sendTo2 = array('zulfequar.memon@gmail.com','imran.memon@gmail.com' );	
	    
		///bypass sytem and send email to zulfequar Memon
		 Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $sendTo2,
                            null,
                            $data
							);
		 					 
				 
        }else{
            throw new Exception('Template Transactionnel Empty');

        $translate->setTranslateInline(true);
		}
        return $this;
    }
    
    //Start for comment 10_03_2014
    public function chatNotify($comment,$quote_id)
    {
		///* Sender Name */
		//$supportName = Mage::getStoreConfig('trans_email/ident_support/name'); 
		///* Sender Email */
		//$supportEmail = Mage::getStoreConfig('trans_email/ident_support/email');
	
		$translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
		$quote = Mage::getModel('Quotation/Quotation')->load($quote_id);
        $templateId = 44;
		
       // $identityId = Mage::getStoreConfig('quotation/quotation_request/email_identity', $quote->getCustomer()->getStoreId());
        $customerData = Mage::getModel('customer/customer')->load($quote->getCustomerId())->getData();
		 
		$identityId = array('name'=>Mage::getStoreConfig('trans_email/ident_general/name', $customerData['store_id']),
						    'email'=>Mage::getStoreConfig('trans_email/ident_general/email', $customerData['store_id'])); 
		
		$store_id = $this->getCurrentStoreId($quote->getCustomer()->getWebsite_id());
		$identityId = array('name'=>Mage::getStoreConfig('trans_email/ident_general/name', $store_id),
						    'email'=>Mage::getStoreConfig('trans_email/ident_general/email', $store_id)); 
		
		$sendTo = explode(',',Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStore_id()));
	   
		
		
		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();	
		
		$customer_email = array_merge(array($customerData['email']),$sendTo);
		
		$customer_email = array($customerData['email']);
		$myQuoteId = $quote->getIncrement_id();
		 
		$incrementId= $quote->getIncrement_id();
		$customerid = $quote->getCustomer()->getEntity_id();
		
		$storeurl = $this->getStoreUrl($quote->getCustomer()->getWebsite_id());				
		$sid =md5('vividexhibits'.$incrementId.$customerid);		
		$authloginurl = $storeurl.'autologin/Directauth/order/quote_id/'.$quote->getIncrement_id().'/SID/'.$sid;
		
		 
		  
		 
		$template_vars = array('myQuoteId'=>$quote->getIncrement_id() );
		$data = array('messageChat'=>$comment, 
					  'myQuoteId'=>$myQuoteId,
					  'myOrderId'=>$myQuoteId,
					  'messageChat'=>$comment,
					  'customerName'=>$customerData['firstname'].' '.$customerData['lastname'],
					  'store'=>Mage::getModel('core/store')->load($store_id),
					  'authloginurl'=>$authloginurl,
					  );
	 	$emailtemplate = Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()
					                                                           ->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            $customer_email,
                            null,
                            $data
							);
		$translate->setTranslateInline(true);					
		//  Zend_debug::dump($emailtemplate);
							
		//$userdata = Mage::getModel('admin/user')->load($dataAll['target_id']);
		/*
		$mail = Mage::getModel('core/email');
		$mail->setToName($customerData['firstname'].' '.$customerData['lastname']);
		$mail->setToEmail($customerData['email']);
		$mail->setBody($comment);
		$mail->setSubject('A comment from admin');
		$mail->setFromEmail($user->getUser()->getEmail());
		$mail->setFromName($user->getUser()->getFirstname().' '.$user->getUser()->getLastname());
		$mail->setType('html');// YOu can use Html or text as Mail format
		$mail->send();
		*/
    }
    //End for comment 10_03_2014

/**
     * Preivew email which will be showed to customer to customer
     */
    public function cPreviewEmail($quote) {
	
	    $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $templateId = Mage::getStoreConfig('quotation/quotation_request/email_template', $quote->getCustomer()->getStoreId());
        
		$store_id = $this->getCurrentStoreId($quote->getCustomer()->getWebsite_id());
		$identityId = array('name'=>Mage::getStoreConfig('trans_email/ident_general/name', $store_id),
						    'email'=>Mage::getStoreConfig('trans_email/ident_general/email', $store_id)); 
		$identityId = Mage::getStoreConfig('quotation/quotation_request/email_identity', $quote->getCustomer()->getStoreId());
        //$sendTo1 = Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStoreId());
	    $sendTo1 = explode(',',Mage::getStoreConfig('quotation/quotation_request/send_to', $quote->getCustomer()->getStoreId()));
	
	    /*email attachements*/
	
	    $email_attachment =  $_REQUEST['email_attachment']; 
	   
		
        
 /****************************************** Start by dev **********************************************/
        
        $customerData = Mage::getModel('customer/customer')->load($quote->getCustomer()->getId())->getData();
        $sendTo = $customerData['email'];
		
		      
        $tableBilling = Mage::getSingleton('core/resource')->getTableName('quotation_billing');
	    $sqlSystem="SELECT * FROM ".$tableBilling." WHERE quotation_id = '".$quote->getId()."' ";
         
         try {
                 $chkSystem = Mage::getSingleton('core/resource')
				 			  ->getConnection('core_write')
							  ->query($sqlSystem);
		 $fetchBilling = $chkSystem->fetch();         
		 
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
			
		 
		$_product = $this->getStoreProduct($item->getProduct_id(), $quote);			 	
		$productid = $_product->getEntity_id();
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
				//var_dump($res_objects2);
				$all_attachments .='<a target="_blank" style="float:left; padding-right:10px;" href="'.Mage::getBaseUrl().'downloads/dl/file/id/'.$res_objects2["file_id"].'/'.$res_objects2["filename"].'"><img style="display: block;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  /><strong>'.$_product->getSku().'</strong></a> ';
				$all_attachments .='<div style="font-family: Tahoma, Geneva, sans-serif; font-size: 12px; padding:7px 0px; color:#555;">'.$res_objects2['file_description'].'</div>';
			}


		}
		$product_image = '';
		 if($_product->getThumbnail()!='no_selection'){
			 $product_image = $_product->getThumbnail();
		 }
		 
		 if($_product->getImage()!='no_selection'){
		 	$product_image = $_product->getImage();
		 }
		 
		 if($_product->getSmall_image()!='no_selection'){
		 	$product_image = $_product->getSmall_image();
		 }
		
		 $_image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product_image ;		 
		 $_product_url = $this->getProductStoreUrl($_product->getUrl_path(), $quote->getCustomer()->getWebsite_id()); 
		 $itemHtml .= '<tr>
    <td style="padding:0px 0px 0px 0px;vertical-align:top;">
    	<table width="100%" cellspacing="0" cellpadding="0" border="0">
    		<tbody>
	    		<tr>
	    		<td style="width:125px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-left: 1px solid #EAEAEA;border-right: 1px solid #EAEAEA;">
			    <a target="_blank"  href="'.$_product_url.'">
			    <img width="125" height="90" src="'.$_image_url.'" style="padding-right:2px;" /></a>
			</td>
			<td style="width:220px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top; border-right: 1px solid #EAEAEA;">
				<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0;">
				            <a target="_blank" href="'.$_product_url.'" style="color:#2f2f2f;font-size:11px">'.$item->getcaption().'</a>
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
					</div>';
					
					$itemHtml .='<table cellpadding="0" cellspacing="0">
					<tbody>';
					if($_product->getTypeId()=='bundle')
					{
		$bundle_item = array();
		$bundled_product = Mage::getModel('catalog/product')->load($_product->getId());
		$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
		$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
		    		);
 		$optionCollection =  $bundled_product->getTypeInstance(true)->getOptions($bundled_product);
			
			foreach($optionCollection as $val)
			   		{
						$itemHtml .='<tr><td align="left" valign="top" style="padding:0" colspan="0" ><strong>'.$val->getDefaultTitle().'</strong></td></tr>';
  						foreach($selectionCollection as $option)
						{
			if($val->getId() == $option->getOptionId())
				   {				    
				    $Product =Mage::getModel('catalog/product')->load($option->getProductId());	
					$itemHtml.='<tr>
					<td >
						<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0; font-weight:normal;text-decoration:none;"><strong style="padding-right:2px;">('.round($option->getSelectionQty()).')</strong>'.$Product->getName().'
				        </h2>	        			        
					</div>
					</td>
					</tr>';
					}
					}
					}
					}
					$itemHtml.='</tbody></table>';
					$itemHtml.='
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA; border-bottom:1px solid #eaeaea; vertical-align:top;">
					'.$item->getsku().'   </td>
				    
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;vertical-align:top; border-bottom:1px solid #eaeaea;">
					'.$item->getqty().'    </td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;vertical-align:top; border-bottom:1px solid #eaeaea;">
						    <span>					
							    <!-- span>'.$quote->FormatPrice($item->GetTotalPriceWithTaxes($quote)).'</span -->  
								<span>'.Mage::helper('core')->currency($item->getPrice_ht()*$item->getQty(), true, false).'</span>          
					</span>
            </td>
				</tr>';
				
		
			/**********************************************19-2-2014 SOC GC*********************************************************/	
/*			if($_product->getTypeID() == 'bundle')
			{
			    $bundle_item = array();
			    $bundled_product = Mage::getModel('catalog/product')->load($_product->getId());
    			    $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
				$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
			   );
			   
			   $optionCollection =  $bundled_product->getTypeInstance(true)->getOptions($bundled_product);
			   
			 
        
			   foreach($optionCollection as $val)
			   {
				$itemHtml .= '<tr><td align="left" valign="top" style="padding:5px 9px" colspan="6" bgcolor="#EAEAEA"><strong><em>'.$val->getDefaultTitle().'</em></strong></td></tr>';
				
				foreach($selectionCollection as $option)
				{
				   if($val->getId() == $option->getOptionId())
				   {
				    
				    $Product =Mage::getModel('catalog/product')->load($option->getProductId());
				    
    				    $itemHtml .= '<tr>
	    		<td style="width:125px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-left: 1px solid #EAEAEA;border-right: 1px solid #EAEAEA;">
			    <a target="_blank"  href="'.$option->getProductUrl().'"><img width="125" height="90" src="'.$Product->getImageUrl().'"  style="padding-right:2px;"/></a>
			</td>
			<td style="width:260px;padding:7px 9px 9px;border-bottom:1px solid #EAEAEA;vertical-align:top;border-right: 1px solid #EAEAEA;">
				<div>
					<h2 style="color:#2f2f2f;font-size:11px;margin-top:0">
				            <a target="_blank" href="'.$option->getProductUrl().'" style="color:#2f2f2f;font-size:11px">'.$Product->getName().'</a>
				        </h2>	        			        
					</div>
					</td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
					'.$Product->getsku().'   </td>
				    
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
					'.$option->getqty().'    </td>
					<td style="font-size:11px;padding:7px 9px 3px 9px;border-right: 1px solid #EAEAEA;border-bottom:1px solid #EAEAEA;vertical-align:top;">
						    <span>';
							
					
			 	$itemHtml .='		    <span>'.$Product->getPrice().'</span>';
				$itemHtml .='            
					</span>
            </td>
				</tr>';
				  
				
				   }
				}
				
			   }
			} 

			*/
				
				
			/*************************************************19-2-2014 EOC gc******************************************************/	
				
			$itemHtml .= '</tbody>
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
	$incrementId = $quote->getIncrementId();
	$customerid = $quote->getCustomer()->getId();

		$sid =md5('vividexhibits'.$incrementId.$customerid);
        $url = Mage::helper('adminhtml')->getUrl('Quotation/Admin/edit', array('quote_id' => $quote->getId()));
      	$storeurl = $this->getStoreUrl($quote->getCustomer()->getWebsite_id());		
		$sid =md5('vividexhibits'.$incrementId.$customerid);	
		
		
		$authloginurl = $storeurl.'autologin/Directauth/order/quote_id/'.$quote->getIncrement_id().'/SID/'.$sid;
		//var_dump($url); 
		
		
		
		
		
		$data = array
            (
            'subject' => Mage::Helper('quotation')->__('New Quotation Request'),
            'quote' => $quote->getIncrementId(),
            'customerid' => $quote->getCustomer()->getId(),
            'customer' => $quote->getCustomer()->getName(),
            'itemHtml' => $itemHtml,
           // 'subtotal' => $quote->FormatPrice($total),
	       // 'subtotal' => $quote->FormatPrice(($total-$quote->getShippingRate()-$quote->GetTaxAmount())),
            'subtotal'=> number_format($quote->getPriceHt(), 2, '.', ''),
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
			'sid'  => $sid,
			'tax' => $quote->FormatPrice($quote->GetTaxAmount()),
			'athuloginurl'=>$authloginurl,
			);
 
 	  
    if (!empty($templateId)) {
           
		   
		   
		   $emailtemplate = Mage::getModel('Quotation/Core_Email_Template')
                    ->setDesignConfig(array('area' => 'adminhtml', 'store' => $quote->getCustomer()
					                                                           ->getStoreId()))
                    ->sendTransactionalWithAttachment(
                            $templateId,
                            $identityId,
                            null,
                            null,
                            $data);
			 				
		
		
			$email_text = $emailtemplate->getMail()->getBodyHtml(); 
			$emailObject = new ArrayObject($email_text);
			$emailTxt = $emailObject->getArrayCopy();


		foreach($emailTxt as $email){
			$email_content[]=$email;
			
			}
							 
        }else{
            throw new Exception('Template Transactionnel Empty');
        	$translate->setTranslateInline(true);
		}
		
		
		
		///saving as attachement file/////
		$file_directory = Mage::getBaseDir('media').'/attachedfiles/';
		$_file = fopen($file_directory.$quote->getIncrement_id().'.html','w');
		$_conn = fwrite($_file,$email_attachment); // or die('could not write to file');
		$_conn = fclose($_file);
		
		$attachment_url = Mage::getBaseUrl('media').'attachedfiles/'.$quote->getIncrement_id().'.html';
		$filename = $quote->getIncrement_id().'.html';
		
		/*showing attached files*/
		$_html =Mage::getModel('Quotation/Quotation')->showAttachedFiles($quote->getId());
		
		
		$pdf_attachment_link = Mage::getUrl().'Quotation/Quote/print/quote_id/'.$quote->getId();		
		$pdf_attachment = ' <a href="'.$pdf_attachment_link.'" target="_blank" title="Click to download PDF '.$pdf_attachment_link.'"><img style="display: block; clear:both;" border="0" src="http://tablethrows.co.nz/media/pdf_attach_icon.png"  alt="attachement icon"  > PDF-'.$quote->getIncrementId().'</a>';
		
		
		if($email_attachment !=''){
		$email_contents = str_replace('Retain',$pdf_attachment.'<p style="text-align:left;font-weight:bold;">Attachment(s):&nbsp;&nbsp;<a href="'.$attachment_url.'" target="_blank">'.$filename.'</a>'.$_html.'</p>Retain',$email_content[10]);
		}else{
			$email_contents = str_replace('Retain',$pdf_attachment.'Retain',$email_content[10]);
			}
		
        return $email_contents;
    
    }
	
	///get store specific product
	public function getStoreProduct($product_id=0, $quote=null){
		 
		$store_id = Mage::app()
    					->getWebsite($quote->getCustomer()->getWebsite_id())
    					->getDefaultGroup()
    					->getDefaultStoreId()
						;
		
		$_products = new Mage_Catalog_Model_Resource_Product_Collection(); 
		 
		$_products->addAttributeToFilter('entity_id',$product_id)
				  ->addAttributeToSelect(array('*'))
				  ->setStoreId($store_id)
				;	
		 
	   foreach($_products as $_cproduct){
			 $_product= $_cproduct;
			  
			 }
			 
		return $_product;	 
		
		} 
		
	///getProductUrlstore wise		
	public function getProductStoreUrl($product_url=null, $website_id=0){
		
		$website = Mage::app()
    					->getWebsite($website_id)
    					//->getDefaultGroup()
    					//->getDefaultStoreId()
						;
			return $website->getName().$product_url;			
		}
		
	public function getCurrentStoreId($website_id=0){
		
		$store_id = Mage::app()
    					->getWebsite($website_id)
    					 ->getDefaultGroup()
    					 ->getDefaultStoreId()
						;
		return $store_id;
		
		}
		
	///getProductUrlstore wise		
	public function getStoreUrl($website_id=0){
		
		$website = Mage::app()
    					->getWebsite($website_id)
    					// ->getDefaultGroup()
    					// ->getDefaultStoreId()
						;
			 
			return $website->getName();			
		}	
}
