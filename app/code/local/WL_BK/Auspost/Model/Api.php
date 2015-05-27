<?php
/*
  * @category   WL
  * @package    Auspost
  * @class      WL_Auspost_Model_Api
  * @description Australia Post provides protected APIs to merchants and account customers, of Australia Post. Merchants will use these services within their own site providing Australia Post delivery capabilities.
 */

class WL_Auspost_Model_Api extends Mage_Core_Model_Abstract
{
    const AUSPOST_API_HTTPS = 'https://api.auspost.com.au';
    const AUSPOST_OAUTH_HTTPS = 'https://solutions.auspost.com.au/mydeliveries/oauth/user/authorize';
	const AUSPOST_GET_TOKEN_HTTPS = 'https://solutions.auspost.com.au/mydeliveries/oauth/authorize';
    const AUSPOST_API_USERNAME = '';
    const AUSPOST_API_PASSWORD = '';
    
    const AUSPOST_DEV_API_HTTPS = 'https://devcentre.auspost.com.au/myapi';
    const AUSPOST_DEV_OAUTH_HTTPS = 'https://solutions.auspost.com.au/mydeliveries/oauth/user/authorize';
	const AUSPOST_DEV_GET_TOKEN_HTTPS = 'https://solutions.auspost.com.au/mydeliveries/oauth/authorize';
    const AUSPOST_DEV_API_USERNAME = 'anonymous@auspost.com.au';
    const AUSPOST_DEV_API_PASSWORD = 'password';

    private $apiHttps;
    private $authHttps;
	private $tokenHttps;
    private $apiUsername;
    private $apiPassword;

	protected $_apHelper;
    protected $testMode;

/**
 *
 * Parses XML string to array data
 * 
 * @param    string $xmlString The request action
 * @return   array associative array data
 * 
 */
 
    private function parseXml($xmlString)
    {
		libxml_use_internal_errors(true);
		$xmlObject = simplexml_load_string($xmlString);
        $result = array ();
        if (!empty($xmlObject))
            $this->convertXmlObjToArr($xmlObject, $result);
		return $result;
	}
    
/**
 *
 * Function to convert XML object to array
 * 
 * @param    object $obj XML objec
 * @param    array converted result
 * 
 */ 
 
    private function convertXmlObjToArr($obj, &$arr)
    {
        $children = $obj->children();
        $executed = false;
        foreach ($children as $elementName => $node)
        {
            if( array_key_exists( $elementName , $arr ) )
            {
                if(array_key_exists( 0 ,$arr[$elementName] ) )
                {
                    $i = count($arr[$elementName]);
                    $this->convertXmlObjToArr ($node, $arr[$elementName][$i]);
                }
                else
                {
                    $tmp = $arr[$elementName];
                    $arr[$elementName] = array();
                    $arr[$elementName][0] = $tmp;
                    $i = count($arr[$elementName]);
                    $this->convertXmlObjToArr($node, $arr[$elementName][$i]);
                }
            }
            else
            {
                $arr[$elementName] = array();
                $this->convertXmlObjToArr($node, $arr[$elementName]);   
            }
            $executed = true;
        }
        if(!$executed&&$children->getName()=="")
        {
            $arr = (String)$obj;
        }
        return ;
    } 
 
/**
 *
 * Creates url query string from array
 * 
 * @param    array $query The array key and value
 * @return   string The url query string
 */    
    private function buildHttpQuery($query)
    {
        $query_array = array();
        foreach($query as $key => $key_value)
            $query_array[] = $key . '=' . urlencode( $key_value );
        return implode( '&', $query_array );
    }

/**
 *
 * Request AUSPOST Auth use CURL 
 * 
 * @param    string $url The url to request
 * @return   string The http response
 */    
    private function ausPostRequest($url, $headers = array (), $auth = true) 
    {
	    $curlReq = curl_init();
	    curl_setopt($curlReq, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curlReq, CURLOPT_URL, $url);
	    curl_setopt($curlReq, CURLOPT_HTTPHEADER, $headers);
        
        // Add password if auth is requeried
        if ($auth)
	       curl_setopt($curlReq, CURLOPT_USERPWD, $this->apiUsername . ":" . $this->apiPassword);
	    curl_setopt($curlReq, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($curlReq, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($curlReq);
	    curl_close($curlReq);
	    return $result;
	}

/**
 *
 * Common function to request to AUSPOST service
 * 
 * @param    string $action The request action
 * @param    array $params The request params
 * @param    boolean $auth Request with username password or not
 * @return   array associative array data
 */
    
    protected function apiRequest ($action, $params = array (), $auth = true)
    {
        // Build url
        $url = $this->apiHttps.'/'.$action.'.xml?'.$this->buildHttpQuery($params); // echo $url;
        $headers = array (
	        "Accept: text/html,application/xhtml+xml,application/xml",
	        "Cookie: OBBasicAuth=fromDialog"
        );
        if ($auth)
            $res = $this->ausPostRequest($url, $headers, true);
        else
            $res = $this->ausPostRequest($url, $headers, false);
        return $this->parseXml($res);
    }

/**
 *
 * Gets Auspost access_token to request customer details information
 *
 *	@param    string $token_url The url to get 
 *  @return   string access token key
 */

    public function getAccessToken ($token_url)
    {
        $headers = array (
	        "Accept: text/html,application/xhtml+xml,application/xml",
	        "Cookie: OBBasicAuth=fromDialog"
        );
        $api = Mage::getModel('auspost/api');
		$response = $api->ausPostRequest($token_url, $headers);
        $params = json_decode($response);
        if (!empty($params->access_token))
        	return $params->access_token;
        return null;
    }


/**
 *
 * Gets main module helper
 *
 *  @return   object helper data of auspost module
 */
 
	protected function apHelper()
	{
		if (is_null($this->_apHelper)) 
        {
			$this->_apHelper = Mage::helper('auspost');
		}
		return $this->_apHelper;
	}

/**
 *
 * Api constructor which inits the appropriate API domain, username, password 
 * 
 *
 */
    
	public function _construct()
	{
		parent::_construct();
        $this->testMode = $this->apHelper()->getEnabledTestMode();
        
        if ($this->testMode)
        {
            $this->apiHttps = self::AUSPOST_DEV_API_HTTPS;
            $this->authHttps = self::AUSPOST_DEV_OAUTH_HTTPS;
	        $this->tokenHttps = self::AUSPOST_DEV_GET_TOKEN_HTTPS;
            $this->apiUsername = self::AUSPOST_DEV_API_USERNAME;
            $this->apiPassword = self::AUSPOST_DEV_API_PASSWORD;
        } 
        else
        {
            $this->apiHttps = self::AUSPOST_API_HTTPS;
            $this->authHttps = self::AUSPOST_OAUTH_HTTPS;
	        $this->tokenHttps = self::AUSPOST_GET_TOKEN_HTTPS;
            $this->apiUsername = $this->apHelper()->getAPIUser();
            $this->apiPassword = $this->apHelper()->getAPIPassword();
        } 
	}

 /**
 *
 * Gets api url
 *
 * @return   string url to AUSPOST webservice
 *
 */
    public function getAPIUrl()
    {
        return $this->apiHttps;
    }

/**
 *
 * Gets Auspost auth url
 *
 * @return   string url to AUSPOST auth
 *
 */
    public function getAuthUrl()
    {
        return $this->authHttps;
    }

/**
 *
 * Gets Auspost auth url
 *
 * @return   string url to AUSPOST auth
 *
 */
	public function getTokenUrl()
	{
		return $this->tokenHttps;
	}
    
 /**
 *
 * Gets collection points by postcode via AUSPOST web service
 *
 * @param    string $postcode The postcode which is used as a condition
 * @return   array collection points
 *
 */
 
    public function getCustomerCollectionPoints($postcode = null)
    {
        $params = array ();
        if (!empty($postcode))
            $params['postcode'] = $postcode;
        // Request to AUSPOST service
        $result = $this->apiRequest('CustomerCollectionPoints', $params);  // print '<pre>'; print_r ($result); exit;
        // Data organization
        $points = array ();
        if (empty ($result['CustomerCollectionPoint']['DeliveryPointIdentifier']) && isset($result['CustomerCollectionPoint'][0]))
        {
            foreach ($result['CustomerCollectionPoint'] as $point)
            {
                $points[] = array (
                    'DeliveryPointIdentifier' => $point['Address']['DeliveryPointIdentifier'],
                    'AddressLine' => $point['Address']['AddressLine'],
                    'SuburbOrPlaceOrLocality' => $point['Address']['SuburbOrPlaceOrLocality'],
                    'StateOrTerritory' => $point['Address']['StateOrTerritory'],
                    'PostCode' => $point['Address']['PostCode'],
                    'CountryCode' => $point['Address']['Country']['CountryCode'],
                    'CountryName' => Mage::helper('auspost/location')->getCountryNameByCode($point['Address']['Country']['CountryCode']),
                    'CustomerCollectionPointName' => $point['CustomerCollectionPointName'],
                    'CustomerAccessSummary' => $point['CustomerAccessSummary']
                );
            }
        }
        else if (!empty($result['CustomerCollectionPoint'])) {
            $point = $result['CustomerCollectionPoint'];
            $points[] = array (
                'DeliveryPointIdentifier' => $point['Address']['DeliveryPointIdentifier'],
                'AddressLine' => $point['Address']['AddressLine'],
                'SuburbOrPlaceOrLocality' => $point['Address']['SuburbOrPlaceOrLocality'],
                'StateOrTerritory' => $point['Address']['StateOrTerritory'],
                'PostCode' => $point['Address']['PostCode'],
                'CountryCode' => $point['Address']['Country']['CountryCode'],
                'CountryName' => Mage::helper('auspost/location')->getCountryNameByCode($point['Address']['Country']['CountryCode']),
                'CustomerCollectionPointName' => $point['CustomerCollectionPointName'],
                'CustomerAccessSummary' => $point['CustomerAccessSummary']
            );
        }
        
        // Handle error message response
        if (!empty($result['BusinessException']))
        {
            $points['ErrorMessage'] = $result['BusinessException']['Description'];
        }
        return $points;
    }
    
 /**
 *
 * Gets tracking information about consignment or articles via AUSPOST web service
 *
 * @param    string $code The tracking code which is used as a condition
 * @return   array tracking information
 *
 */    
 
    public function getTracking($code) 
    {
        // Get result
        $result = $this->apiRequest('QueryTracking', array (
            'q' => $code
        ));
        // Data organization
        $trackings = array ();
        if (empty($result['TrackingResult']['TrackingID']) && isset($result['TrackingResult'][0]))
        {
            foreach ($result['TrackingResult'] as $tracking)
            {
                $events = array ();
                if (!empty($tracking['ArticleDetails']['Events']['Event']))
                    $events = $tracking['ArticleDetails']['Events']['Event'];
                if (!empty($tracking['TrackingID']))
                    $trackings[] = array (
                        'TrackingID' => $tracking['TrackingID'],
                        'Events' => $events
                    );
            }
        }
        else {
            $events = array ();
            $tracking = $result['TrackingResult'];
            if (!empty($tracking['ArticleDetails']['Events']['Event']))
                $events = $tracking['ArticleDetails']['Events']['Event'];
            if (!empty($tracking['TrackingID']))
                $trackings[] = array (
                    'TrackingID' => $tracking['TrackingID'],
                    'Events' => $events
                );
        }
        
        if (!empty($result['TrackingResult']['BusinessException']))
        {
            $trackings['ErrorMessage'] = 'Code '.$result['TrackingResult']['BusinessException']['Code'] .': '.$result['TrackingResult']['BusinessException']['Description'];
        }
        return $trackings;
    }

/**
 *
 * Returns the expected delivery date for a delivery / order and a list of available delivery dates to use in a specified delivery date service.
 *
 * @param    string $fromPostcode The postcode where the article will be lodged
 * @param    string $toPostcode The postcode where the article will be delivered to
 * @param    int $networkId Specifies the delivery network (values are 01 = Standard, 02 = Express)
 * @param    string $lodgementDate  The date the article will be lodged in the Australia Post network in ISO 8601 date format e.g. 2011-04-07
 * @param    int $numberOfDates Specifies the number of dates to return from 1-10, The first date return is always the expected delivery date, subsequent dates are available delivery dates for the delivery. Note: validation is required in service
 * @return   array list of available delivery dates
 *
 */   

    public function getDeliveryDates($fromPostcode, $toPostcode, $lodgementDate, $numberOfDates, $networkId)
    {
        // get date unstill enough quantity of available delivery days
        // $fromPostcode = '3000'; $toPostcode = '3015';
        while (count($days)<=$numberOfDates) 
        {
            $res = $this->apiRequest('DeliveryDates', array (
                'fromPostcode' => $fromPostcode,
                'toPostcode' => $toPostcode,
                'networkId' => $networkId,
                'lodgementDate' => $lodgementDate,
                'numberOfDates' => 10
            )); // print '<pre>'; print_r ($res); exit;
            if (!empty($res['BusinessException']))
                break;            
            
            // organise data
            foreach ($res['DeliveryEstimateDates']['DeliveryEstimateDate'] as $day)
            {
                // only get available delivery day
                // if ($day['TimedDeliveryEnabled']=='true')
                    $days[] = $day;
            }
        } 
        // The days count may bigger the needed days. So, only return the needed days.
        if (count($days)>=$numberOfDates)
            return array_slice($days, 0, $numberOfDates);
        return $days;
    }

/**
 *
 * Get Customer Details (address and preferences).
 *
 * @param    string $access_token OAuth access token, credential allowing access by the merchant to the customer address and preference details.
 * @return   array customer information
 *
 */

    public function getCustomerDetails($access_token)
    {
        $result = array ();
        $res = $this->apiRequest('CustomerDetails', array (
            'access_token' => $access_token
        ), false);
        // organise requested data
        /*print '<pre>';
        print_r ($res);*/
        if (!empty($res['DeliveryAddresses']['DeliveryAddress']))
        {
            $description = $res['DeliveryAddresses']['Description'];
            if (!empty($res['DeliveryAddresses']['ElectronicContact'][0]['Telephone']['TelephoneNumber']))
                $phonenumber = $res['DeliveryAddresses']['ElectronicContact'][0]['Telephone']['TelephoneNumber'];
            else
                $phonenumber = '';
            $res = $res['DeliveryAddresses']['DeliveryAddress'];
            
            $result = array (
                'Description' => $description,
                'AddressLine1' => $res['AddressLine'],
                'AddressLine2' => '',
                'Suburb' => $res['SuburbOrPlaceOrLocality'],
                'State' => $res['StateOrTerritory'],
                'StateName' => Mage::helper('auspost/location')->getStateNameByCode($res['StateOrTerritory']),
                'PostCode' => $res['PostCode'],
                'CountryCode' => $res['Country']['CountryCode'],
                'CountryName' => $res['Country']['CountryName'],
                'Phone' => $phonenumber
                
            );
        }
        // return address
        return $result;
    }
    
/**
 *
 * Returns the delivery timeslots.
 *
 * @param    string $date ISO standard identified and a day description as follows: 1-Monday, 2-Tuesday, 3-Wednesday, 4-Thursday, 5-Friday, 6-Saturday, 7-Sunday
 * @return   array list of available delivery timeslots
 *
 */
 
    public function getDeliveryTimeslots ($date)
    {
        $timeslots = $this->apiRequest('DeliveryTimeslots', array(
            'day' => $date
        )); // print '<pre>'; print_r ($timeslots); exit;
        return $timeslots;
    }

 /**
 *
 * Return the capabilities, for a particular day of the week such as standard delivery and timed delivery at a postcode.
 *
 * @param    string $postcode Valid Australian postcode (Numeric string at least four characters long).
 * @return   array particular day of the week
 *
 */    

    public function getPostcodeDeliveryCapabilities($postcode)
    {
        // Get result
	    if ($this->apHelper()->getEnabledTestMode())
	        $result = $this->apiRequest('PostcodeCapability', array (
	            'Postcode' => $postcode
	        ));
	    else
	        $result = $this->apiRequest('PostcodeCapability', array (
		        'postcode' => $postcode
	        ));
        return $result;
    }

/**
 *
 * Validate Australian Address.
 *
 * @param    string $addressLine1 Address line 1
 * @param    string $addressLine2 Address line 2
 * @param    string $suburb Australian suburbs (like Richmond, Manly etc)
 * @param    string $state  Australian states (valid state parameters are VIC, NSW, TAS, NT, WA, SA, QLD, ACT)
 * @param    string $postcode Australian Postcode (Numeric string at least four characters long e.g. 3021)
 * @param    string $country Australia (100 character ISO-3166-1 country name. Currently only Australia is supported!)
 * @return   array Associated array of code and message in case of address is invalid
 *
 */   

    public function getValidateAustralianAddress($addressLine1, $addressLine2, $suburb, $state, $postcode, $country)
    {
        $result = array ();
        $res = $this->apiRequest('ValidateAddress', array (
            'addressLine1' => $addressLine1,
            'addressLine2' => $addressLine2,
            'suburb' => $suburb,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country
        ));
        $result['IsValid'] = $res['ValidAustralianAddress']=='true' ? 1 : 0;
        if (!empty($res['Address']))
        {
            $aL1=''; $aL2='';
            if(is_array($res['Address']['AddressLine']))
            {
                if (isset($res['Address']['AddressLine'][0]))
                    $aL1 = $res['Address']['AddressLine'][0];
                if (isset($res['Address']['AddressLine'][1]))
                    $aL2 = $res['Address']['AddressLine'][1];
            } 
            else
                $aL1 = $res['Address']['AddressLine'];
            $result['SuggestedAddress'] = array (
                'AddressLine1' => $aL1,
                'AddressLine2' => $aL2,
                'Suburb' => $res['Address']['SuburbOrPlaceOrLocality'],
                'State' => $res['Address']['StateOrTerritory'],
                'StateName' => Mage::helper('auspost/location')->getStateNameByCode($res['Address']['StateOrTerritory']),
                'PostCode' => $res['Address']['PostCode'],
                'CountryName' => $res['Address']['Country']['CountryName'],
                'CountryCode' => $res['Address']['Country']['CountryCode']
            );
        }
        if (!empty($res['BusinessException']))
        {
            $result['ErrorMessage'] = $res['BusinessException']['Description'];
        }
        return $result;
    }

/**
 *
 * Returns api username.
 *
  * @return   string api username
 *
 */

    public function getApiUser()
    {
	    return $this->apiUsername;
    }

/**
 *
 * Returns api password.
 *
  * @return   string api password
 *
 */

	public function getApiPassword()
	{
		return $this->apiPassword;
	}
}
