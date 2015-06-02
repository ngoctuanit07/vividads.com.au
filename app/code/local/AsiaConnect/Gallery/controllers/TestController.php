<?php
Class AsiaConnect_Gallery_TestController extends Mage_Core_Controller_Front_Action
{
	public function getPublishImage()
    {
    	$baseDir = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'onface/default/'; 
    	$defaultImage = Mage::getStoreConfig("onface/fanpageupdate/defaultImage");
    	$logoImage = Mage::getStoreConfig('design/header/logo_src');
		if( ($defaultImage) && (file_get_contents($baseDir.$defaultImage)) )
		{
			$publishImage = $baseDir.$defaultImage;
		}
		elseif ( ($logoImage) && (file_get_contents(Mage::getDesign()->getSkinUrl().$logoImage)) )
		{
			$publishImage = Mage::getDesign()->getSkinUrl().$logoImage; 
		}
		else return false;
		return $publishImage;
    }
    
	public function pubAction()
    {
    	$facebook = $this->_getFacebook();
    	$pageId = "132343656844596";
    	$data = array("message"=>"abc",
    				  "access_token"=>Mage::getStoreConfig('onface/fanpagetoken/acessToken'),
    				  "link"=>"http://test14.good-demo.com");
        try 
        {   
			$storyId = $facebook->api('/'.$pageId.'/feed', 'post',$data);
        }
        
        catch (Exception $e){}
        return $this;
    }
	protected function _getFacebook()
    {$pageId = Mage::getStoreConfig('onface/fanpagetoken/pageId');
        if(!$this->_facebook)
        	$this->_facebook = Mage::getModel('facebookapi/facebook');
    	return $this->_facebook; 
    }
    
    /**
     * get country by ip when a customer register account
     */
    public function getCountryByIp()
    {
			//REMOTE_ADDR is for most server but IIS v6, IIS v7 							
			$realip=$_SERVER['REMOTE_ADDR'];
			$info_ip_address=array();
			
			//if server not support, use third-party api
			$key="718b4c6e9d4b09b15cbb35e846457723cd0fe7d842401c7203069b7252f7d289";			
			$xml = simplexml_load_file('http://api.ipinfodb.com/v2/ip_query.php?key='.$key."&ip=".$realip."&timezone=true");														
			if( isset($xml)&& ($xml) )
			{
				$info_ip_address=$xml->children();											
			  	if($info_ip_address[0]=="OK")											
				return $info_ip_address;									
			};
			
			//another third-party api
			$lines = file('http://api.hostip.info/get_html.php?ip=203.162.0.181');
			$str=substr($lines[0], 9);																
			if(isset($str)){
				$begin= strpos($str,'(');
				$code=substr($str,0,$begin);
		   		if( $begin>0 )
		   		{
					$info_ip_address[0]='OK';
					$info_ip_address[1]=strtoupper($code);
					return $info_ip_address;	
		   		}											
			};	
			return null;
    }
    
	public function getCountryFromIP($ip)
	{		
	 	$country = exec("whois $ip  | grep -i country"); // Run a local whois and get the result back
		$country = str_replace("country:", "", "$country");
		$country = str_replace("Country:", "", "$country");
		$country = str_replace("Country :", "", "$country");
		$country = str_replace("country :", "", "$country");
		$country = str_replace("network:country-code:", "", "$country");
		$country = str_replace("network:Country-Code:", "", "$country");
		$country = str_replace("Network:Country-Code:", "", "$country");
		$country = str_replace("network:organization-", "", "$country");
		$country = str_replace("network:organization-usa", "us", "$country");
		$country = str_replace("network:country-code;i:us", "us", "$country");
		$country = str_replace("eu#countryisreallysomewhereinafricanregion", "af", "$country");
		$country = str_replace("", "", "$country");
		$country = str_replace("countryunderunadministration", "", "$country");
		$country = str_replace(" ", "", "$country");
		return $country;
	 }
    
}