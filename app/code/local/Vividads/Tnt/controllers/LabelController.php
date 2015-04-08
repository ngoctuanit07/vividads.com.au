<?php
class Vividads_Tnt_LabelsController extends Mage_Core_Controller_Front_Action{
	public function indexAction(){

		$vivid['identifier']='VIVID';
		$vivid['account']='21664906';
		$vivid['company']='VIVID ADS';
		$vivid['address1'] ='302 BRIDGE STREET';
		$vivid['address2']='';
		$vivid['city']='PORT MELBOURNE' ;
		$vivid['state']='VIC';
		$vivid['zip']='3207';
		$vivid['name']='DESPATCH';
		$vivid['email']="support@vividads.com.au";	
		$date = date('d-m-Y');
		
		$shipmentId	= $this->getRequest()->getParam('shipment_id');		
		$shipmentsModel = Mage::getModel('multishipping/shipment')->load($shipmentId);	
		$shipId = $shipmentsModel['shipment_id'];
						
		if(!$shipId){
				echo 'Shipment not Found.';
		}
		else{				
			$count = 0;	$paid = 0.00;			
			$orderId = $shipmentsModel->getOrderId();
			$order = Mage::getModel('sales/order')->load($orderId);						
			$status = $order->getStatus();
			$subtotal = $order->getSubtotal();
			$totalamt = $order->getGrandTotal();			
			$totalDues = $order->getTotalDue();
			$paid = $totalamt-$totalDues;
			if($paid < $totalamt)					 
				$stamp_url="/media/tnt/NOT PAID.gif";				
			if($paid > 0 && $paid == $totalamt)								
				$stamp_url="/media/tnt/Paid.gif";				
				
			$shippingAddressId = $shipmentsModel['shipping_address_id'];	
			$addressModel = Mage::getModel('sales/order_address')->load($shippingAddressId);			
			$postCode = $addressModel['postcode'];
			$city = $addressModel['city'];
			$firstName = $addressModel['firstname'];
			$lastName = $addressModel['lastname'];
			$region = $addressModel['region'];
			$street = $addressModel['street'];
			$telephone = $addressModel['telephone'];
			$company = $addressModel['company'];
																			
			$collection = Mage::getModel('sales/order_shipment_comment')->getCollection()->addFieldToFilter('parent_id',array('eq' => $shipId));
			foreach($collection as $_collection){
				$commentId = $_collection->getId();					
				$commentModel = Mage::getModel('sales/order_shipment_comment')->load($commentId);
				$comment = $commentModel->getComment();
			}							
			$items = Mage::getModel('sales/order_shipment_item')->getCollection()
					 ->addFieldToFilter('parent_id',array('eq' => $shipId));				
			$total = count($items);							
			$style = array(
					'position' => '',
					'align' => 'C',
					'stretch' => false,
					'fitwidth' => true,
					'cellfitalign' => '',
					'border' => true,
					'hpadding' => 'auto',
					'vpadding' => 'auto',
					'fgcolor' => array(0,0,0),
					'bgcolor' => false, //array(255,255,255),
					'text' => true,
					'font' => 'helvetica',
					'fontsize' => 8,
					'stretchtext' => 4					
				);															
			$pdf = new TCPDF_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetFont('helvetica', '', 8);				
			$counter=1;						
			foreach($items as $item){
				$productId = $item->getProductId();
				$qty = $item->getQtyToInvoice();
				$product = Mage::getModel('catalog/product')->load($productId);
				$name = $product->getName();
				$unitPrice = $product->getPrice();
				$sku = $product->getSku();
				$weight = $product->getWeight();										
				$count++;														
				$conNote = '00313113000054209';// It wil be dynamic
				$barcode = "6104".$conNote.(str_pad($count,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));					
				$params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
				$itemno = "001" . $conNote . (str_pad($count,3,"0",STR_PAD_LEFT));																											
				$pdf->AddPage("L","A4");		
$tbl = <<<EOD
<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
  <td colspan="1" align="left" ><table width="100%" border="" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="50%" style="border-bottom:#000 2px solid"><table  border="0" align="left" cellpadding="0" cellspacing="0" height="10px">
        <tr>
          <td align="left" valign="top"><span style="line-height:0.8em;font-size:38;font-weight:bold">{$postCode}</span>
          <span style="font-size:13;font-weight:bold">{$region} </span></td>
        </tr>
      </table></td>
      <td  align="right" style="border-bottom:#000 2px solid"><table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="right" style="font-size:15"><strong>LV3</strong></td>
        </tr>
        <tr>
          <td align="right" style="font-size:13"><strong>VVD000054209</strong></td>
        </tr>
        <tr>
          <td align="right" valign="bottom" style="font-size:8">Itm:$itemno </td>
        </tr>
      </table></td>
    </tr>
    <tr>
  <td style="font-size:13"><strong>Service Title here</strong></td>
    <td align="right">
<table border="0" cellspacing="0" cellpadding="0" align="right">
           <tr>
              <td style="font-size:7">Sort</td>
              <td rowspan="2" style="font-size:13"><strong>30015</strong></td>
            </tr>
            <tr>
              <td style="font-size:7">Bin:</td>
              </tr>
          </table>
</td>
  </tr>
  <tr>
    <td colspan="2"  style="border-top:#000 2px solid"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left" style="font-size:9">$date</td>
        <td align="center" style="font-size:9">{$count} of {$total}</td>
        <td align="center"style="font-size:9">Item Wt:{$weight} Kg</td>
        <td align="right" style="font-size:9">Ex LV3</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" align="center" style="border-bottom:#000 2px solid;border-top:#000 2px solid;font-size:9">Does not contain any dangerous goods</td>
  </tr>
  <tr>
    <td colspan="2" align="left"  ><table width="100%" border="0" align="left">
      <tr>
        <td valign="top" style="font-size:9" width="20">To:</td>
        <td style="font-size:14;font-weight:bold;line-height:0.8em" width="220" height="80">{$firstname} {$lastname}, {$company}, {$street}, , {$city}, {$region}</td>
      </tr>
    </table>
      </td>
  </tr>
  <tr>
        <td valign="top" width="30" style="border-top:#000 2px solid;font-size:8">From:</td>
        <td width="220" style="border-top:#000 2px solid;font-size:7">{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']}<br>
Senders Ref: LYZ-70952-3</td>
      </tr>
<tr> <td colspan="2"></td></tr>
  <tr>
    <td colspan="2" >
	<table border="0" width="230" align="center" cellpadding="0" cellspacing="0" >
	<tr>
		<td colspan="2" align="left" style="font-size:7" height="20">
		<strong>Special Instructions: $comment</strong>
		</td>
	</tr>
        <tr>
          <td  width="50%" valign="top" height="75" style="line-height:0.9;border:#000 2px solid;font-size:7;" align="left"><strong>CN:VVD000054209<br>Itm:00100313113000054209001<br>{$count} of {$total}<br>TO:</strong><BR>{$firstname} {$lastname}, {$company}, {$street}, , {$city}, {$region} {$postcode}</td>
          <td width="50%" valign="top" align="left" style="line-height:0.9;border-bottom:#000 2px solid; border-right:#000 2px solid;font-size:6;border-top:#000 2px solid"><strong>Service Title here<br>Con Note Wt.: {$weight} Kg.<br><br>FROM:</span><BR>{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']} {$vivid['zip']}</td></tr>

<tr><td colspan="2"></td></tr>       
 <tr>
          <td colspan="2"><br><tcpdf method="write1DBarcode" params="{$params}" /></td>
          </tr>
    </table>
	</td></tr>
        </table>
	</td>
  </tr>
</table>
EOD;

$paidstamp=<<<EOD
<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr><td align="left">Company:</td><td align="left" ><strong> {$company}</strong></td></tr>
	<tr><td align="left">Name:</td><td align="left" ><strong> {$firstname} {$lastname}</strong></td></tr>
	<tr><td align="left">Phone:</td><td align="left" ><strong> {$telephone}</strong></td></tr>
	<tr><td align="left"><h1 style="font-size:28">{$invoicecode}</h1></td></tr>
	<tr><td align="left"><img width="100" height="100" border="0" src="{$site_url}images/Main/{$paystatus}.gif"></td></tr>
	<tr><td align="left"><h1 style="font-size:35">{$pickup}{$urgent}</h1></td></tr></table>
EOD;

$o_items=<<<EOD
<table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<h1>Shipped Items</h1>
		</td>
	</tr>
EOD;

$pdf->writeHTMLCell(0, 0, '', '', $tbl, 0, 1, 0, true, '', true);
$pdf->writeHTML($paidstamp, true, false, false, false, '');
	
if($counter==1){
	$pdf->writeHTML($o_items, true, false, false, false, '');
	$order_items=$order->getAllItems();																	
		foreach($order_items as $orderDetails){
			$prodid=$orderDetails->getProductId();
			$product = Mage::getModel('catalog/product')->load($prodid);															
			$prodname=$product->getName();	
			$quantity = $product->getQty();											
			$thumbnail = $product->getThumbnailUrl();												
			$url = explode("://", $thumbnail);
			$url[0]."</br>"; 						
			$url1 = $url[1];
			//$imageurl = substr($url1,25);																															
			$imageurl = substr($url1,21);																															

$o_items_img=<<<EOD
	<tr>			
		<td><strong>$quantity</strong>
			<img src="{$imageurl}" alt="Image not shown" width="75px" height="75px" />{$prodname}			
		</td>		
	</tr>
EOD;
				
	$pdf->writeHTML($o_items_img, true, false, true, false, '');
	}//end of foreach	
	

}//END of if
$stamp=<<<EOD
	<tr>
		<td>
			<img src="{$stamp_url}" alt="Stamp not Shown" width="75px" height="75px" />
		</td>
	</tr>
</table>

EOD;
$pdf->Ln();
			$pdf->writeHTML($stamp, true, false, true, false, '');
			$counter++;
			}//end of foreach						
			$pdf->Output('example_001.pdf', 'I');
		}//end of else body	
	}//end of function		
}//end of class		
?>
