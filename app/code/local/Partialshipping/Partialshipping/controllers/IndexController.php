<?php
class Partialshipping_Partialshipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/partialshipping?id=15 
    	 *  or
    	 * http://site.com/partialshipping/id/15 	
    	 */
    	/* 
		$partialshipping_id = $this->getRequest()->getParam('id');

  		if($partialshipping_id != null && $partialshipping_id != '')	{
			$partialshipping = Mage::getModel('partialshipping/partialshipping')->load($partialshipping_id)->getData();
		} else {
			$partialshipping = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($partialshipping == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$partialshippingTable = $resource->getTableName('partialshipping');
			
			$select = $read->select()
			   ->from($partialshippingTable,array('partialshipping_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$partialshipping = $read->fetchRow($select);
		}
		Mage::register('partialshipping', $partialshipping);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    function uploadetfileAction()
    {
	    $directory = Mage::getBaseDir('media') . DS ."shiplabel" . DS;

	    //get all text files with a .txt extension.
	    $texts = glob($directory . "*.txt");
	    
	    $date = date('dMY');
	    
	    //print each file name
	    foreach($texts as $file)
	    {
		if(strpos($file,$date))
		{
		    //echo $file;
		    
		    $remote_file = '/outbox/'.basename($file);
		    
		    chmod($file, 0777);
		    $fp = fopen($file, 'r');
		    
		    // set up basic connection
		    $conn_id = ftp_connect('ftp.tnt.com.au') or die("Could not connect");
		    
		    // login with username and password
		    $login_result = ftp_login($conn_id, 'vivid548', 'myV^@d7M0');
		    ftp_pasv($conn_id, true);
		    $ret = ftp_nb_put($conn_id, $remote_file, $file, FTP_ASCII, FTP_AUTORESUME);
		    
		    while ($ret == FTP_MOREDATA) {
		    
		       // Do whatever you want
		      // echo ".";
		    
		       // Continue upload...
		       $ret = ftp_nb_continue($conn_id);
		    }
		    
		    if ($ret != FTP_FINISHED) {
		       ///echo "There was an error uploading the file...";
		      // exit(1);
		    }
		    
		    fclose($fp);
		    // close the connection
		    ftp_close($conn_id);
		}
	    }
    }
    
    ///create manifest 21-4-2014 starts
    function createManifestAction()
    {
	$date=$this->getRequest()->getParam('date');
	$serv = array('Road Express','Overnight Express','ONFC Satchel','9:00 Express','Technology Express - Sensitive Express','12:00 Express','10:00 Express');
	
	$ret='';
	$count = 0;
	foreach($serv as $v){
	   $res = Mage::getModel('partialshipping/partialshipping')->getManifest($v,$date);
	   
	    if($res){
		
		$path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$res;
		$url2 = Mage::helper('adminhtml')->getUrl('partialshipping/adminhtml_partialshipping/download');
	        if (file_exists($path)) {
		    $ret .='<span><a href="'.$url2.'file/'.$res.'" >Download '.$res.'</a></span><br/>';
		    
		}
	   }
	}
	
	if($ret ==''){
	    echo "No data found";
	}else{
	    echo $ret;
	}
	
    
    }
    ///create manifest 21-4-2014 ends
    
    function TNTtrackerAction()
    {
	require_once('lib_simple_html_dom.php');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$tableNameGrid = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
	$tableNameTrack = Mage::getSingleton('core/resource')->getTableName('partialshipping_track');
						
	$select = $connectionRead->select()
	->from($tableNameGrid, array('*'))
	->where('track_number!=? AND status != "shipmented"','');
	
	$row1 =$connectionRead->fetchAll($select);
	//print_r($row1);
	
	foreach($row1 as $track)
	{
	    $Url = 'http://www.tntexpress.com.au/InterAction/ASPs/CnmHxAS.asp?'.$track['track_number'];
	    
	    //$Url = 'http://www.tntexpress.com.au/InterAction/ASPs/CnmHxAS.asp?VVD000055714';
	    
	    $html = file_get_html($Url);
	    $data = $html->find('font[class=f2]');
	    $data_count = count($data);
	    
	    array_shift($data);
	    $i = 1;
	    if($data_count > 1)
	    {
		foreach($data as $element)
		{
		    //if(strpos($element,'class="f1"'))
		    //echo $element->innertext;
		    
		    if($i == 1)
		    {
			echo $status = $element->innertext;
			$i++;
		    }
		    else if($i == 2)
		    {
			echo $date = $element->innertext;
			$i++;
		    }
		    elseif($i == 3)
		    {
			echo $time = $element->innertext;
			$i++;
		    }
		    else if($i == 4)
		    {
			echo $depot = $element->innertext;
			
			$connectionWrite->beginTransaction();
			$data1= array();
			$data1['shipping_id']= $track['entity_id'];
			$data1['tracking_number']=$track['track_number'];
			$data1['status']=$status;
			$data1['date']= $date;
			$data1['time']=$time;
			$data1['depot']=$depot;
			$connectionWrite->insert($tableNameTrack, $data1);
			$connectionWrite->commit();

			$i=1;
		    }
		    
		    
		}
		
		if($status == 'Delivered Short')
		{
		    $connectionWrite->beginTransaction();
		    $data2 = array();
		    $data2['status'] = 'shipmented';
		    $where = $connectionWrite->quoteInto('entity_id =?', $track['entity_id']);
		    $connectionWrite->update($tableNameGrid, $data2, $where);
		    $connectionWrite->commit();
		}
		
	    }
	    
	    
	}
	
	exit;
    }
}