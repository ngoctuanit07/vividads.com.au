<?php

class MW_FollowUpEmail_Model_Observer

{

	protected $_dontsendemailtime;

	protected $_intTimeLast;

	protected $_intTimeFrom;

	protected $_now;
	
	protected $_intTimeCleanMail;

	

	protected function _intTime()

    {

        $cartisabandonedafterconfig = Mage::getStoreConfig('followupemail/config/cartisabandonedafter');	

		$dontsendemailhoursconfig = Mage::getStoreConfig('followupemail/config/dontsendemailhours');	
		
		$autocleanqueueemail = Mage::getStoreConfig('followupemail/config/autocleanqueueemail');	

		if(is_numeric($cartisabandonedafterconfig)){

			$this->_intTimeLast = ($cartisabandonedafterconfig * 60)*60;	

		}

		else

			$this->_intTimeLast = 24 * 3600;	

		

		if(is_numeric($dontsendemailhoursconfig)){

			$this->_dontsendemailtime = ($dontsendemailhoursconfig * 60)*60;	

		}

		else

			$this->_dontsendemailtime = 24 * 3600;	
			
		if(is_numeric($autocleanqueueemail)){

			$this->_intTimeCleanMail = ($autocleanqueueemail * 24)*3600;	

		}

		else

			$this->_intTimeCleanMail = (60 * 24) * 3600;	

						

		$this->_intTimeFrom = 24 * 3600;	

		$this->_now = time();	

    }

	

	public function getCheckoutSession()

    {

        return Mage::getSingleton('checkout/session');

    }

	

	public function eventAddQueue($arvgs){
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		$this->_intTime();

		$order = $arvgs->getOrder();

		$groupId = $order->getCustomerGroupId();

		$storeId = $order->getStoreId();

		$store = Mage::getModel('core/store')->load($storeId);

		$this->eventDeleteQueue($arvgs);

		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::ORDER_STATUS_PREFIX.$order->getStatus();		

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();		

		if(is_array($rules) && count($rules) > 0){			

			foreach($rules as $rule){				

				if($eventStatus == $rule['event']){

					if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;	

					$items = $order->getAllItems();					

					$productIds = array();

					$senderInfo = array();

					$senderInfo['sender_name'] = $rule['sender_name'];

					$senderInfo['sender_email'] = $rule['sender_email'];					

					foreach($items as $item){
						if ($item->getParentItem()) continue;
						$productIds[] = $item->getProductId();

					}					

					//$params[] = $order->getId();

					$emailChain = unserialize($rule['email_chain']);		           

		            $queue = Mage::getModel('followupemail/emailqueue');		          

					$customerInfo = $this->_getCustomer($order->getCustomerId(),$order);					

					foreach ($emailChain as $emailChainItem) {

						$params = array();

						//get content of current email template							

	                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

						//Mage::log($emailTemplate);

						//$emailTemplateContent = $emailTemplate['content'];  						

						$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];

						$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'order',$order->getId());

						$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));

						$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

						$params['senderInfo'] = $senderInfo;

						$params['productIds'] = $productIds;

						$params['orderId'] = $order->getId();						

						$params['data'] = "";

						$params['customer'] = "";

						$params['customerId'] = $order->getCustomerId();

						$params['cart'] = "";

						$params['storeId'] = $storeId;

						$params['code'] = $code;

						//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customerInfo,$linkDirect);

						$content = "";

						// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
						if($customerInfo['customer_email'] == "") continue;
						if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customerInfo['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){							

								$queue->add(

			                        time() + $timeSent * 60,

									$rule['rule_id'],

									$order->getId(),

									$emailTemplate['sender_name'],

									$emailTemplate['sender_email'],

									$customerInfo['customer_name'],				

									$customerInfo['customer_email'],

									$emailTemplate['subject'],				

									$content,

									serialize($params),

									$emailTemplate['code'],

									0,

									$code

		                    	);

							//}	

						}	                   			               

					}

				}				

			}		

		}

		// Check event type is Order Updated

		$ruleCollectionOrderUpdated = Mage::getModel('followupemail/rules')->getCollection();

		$rulesOrderUpdated = $ruleCollectionOrderUpdated->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::ORDER_UPDATED,$storeId,$groupId)->getData();		

		if(is_array($rulesOrderUpdated) && count($rulesOrderUpdated) > 0){			

			foreach($rulesOrderUpdated as $rule){	

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];							

				$conditionsArr = unserialize($rule['conditions_serialized']);

				$collectionCondition = $conditionsArr['conditions'];

				foreach($collectionCondition as $condition){

					$eventCondition = $condition['value'];					

					if($eventStatus == $eventCondition){

						if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;	

						$productIds = array();						
						$items = $order->getAllItems();	
						foreach($items as $item){
 						if ($item->getParentItem()) continue;
						$productIds[] = $item->getProductId();
						}
						
						$emailChain = unserialize($rule['email_chain']);		           

			            $queue = Mage::getModel('followupemail/emailqueue');		          

						$customerInfo = $this->_getCustomer($order->getCustomerId(),$order);					

						foreach ($emailChain as $emailChainItem) {

							$params = array();							

							//get content of current email template							

		                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

							//Mage::log($emailTemplate);

							//$emailTemplateContent = $emailTemplate['content'];  

							$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];

							$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'order',$order->getId());

							$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));

							$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

							$params['senderInfo'] = $senderInfo;

							$params['productIds'] = $productIds;

							$params['orderId'] = $order->getId();							

							$params['data'] = "";

							$params['customer'] = "";

							$params['customerId'] = $order->getCustomerId();

							$params['cart'] = "";

							$params['storeId'] = $storeId;

							$params['code'] = $code;

							//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customerInfo,$linkDirect);

							$content = "";
							if($customerInfo['customer_email'] == "") continue;
							if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customerInfo['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

								//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

									$queue->add(

				                        time() + $timeSent * 60,

										$rule['rule_id'],

										$order->getId(),

										$emailTemplate['sender_name'],

										$emailTemplate['sender_email'],

										$customerInfo['customer_name'],				

										$customerInfo['customer_email'],

										$emailTemplate['subject'],				

										$content,

										serialize($params),

										$emailTemplate['code'],

										0,

										$code

			                    	);	

								//}

							}	                   			               

						}

					}

				}

			}	

		}		

	}

	

	public function newOrder($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();

		$order = $arvgs->getOrder();

		$items = $order->getAllItems();

		$productIds = array();

		$sku = array();

		foreach($items as $item){
			if ($item->getParentItem()) continue;
			$productIds[] = $item->getProductId();

			$sku[] = $item->getSku();

		}

		$customer = $this->_getCustomer($order->getCustomerId(),$order);		

		$groupId = $order->getCustomerGroupId();

		$storeId = $order->getStoreId();

		$store = Mage::getModel('core/store')->load($storeId);		

		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::NEW_ORDER_PLACED;

		$this->eventNewPlaceOrderDeleteQueue($eventStatus,$customer['customer_email'],$sku,$groupId,$storeId);

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();		

		if(is_array($rules) && count($rules) > 0){

			foreach($rules as $rule){

				if($eventStatus == $rule['event']){					

					if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;		

					$senderInfo = array();

					$senderInfo['sender_name'] = $rule['sender_name'];

					$senderInfo['sender_email'] = $rule['sender_email'];											

					$cartInfo = array();									

					//$params[] = $order->getId();

					$emailChain = unserialize($rule['email_chain']);		           

		            $queue = Mage::getModel('followupemail/emailqueue');		          					

					foreach ($emailChain as $emailChainItem) {

						//get content of current email template							

	                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

						//$emailTemplateContent = $emailTemplate['content'];  

						$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];

						$code = MW_FollowUpEmail_Helper_Data::encryptCode($customer['customer_email'],'order',$order->getId());

						$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));

						$params = array();

						$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

						$params['senderInfo'] = $senderInfo;

						$params['productIds'] = $productIds;

						$params['orderId'] = $order->getId();						

						$params['data'] = "";

						$params['customer'] = "";

						$params['customerId'] = $order->getCustomerId();

						$params['cart'] = "";

						$params['storeId'] = $storeId;

						$params['code'] = $code;

						//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customer,$linkDirect);

						$content = "";

						// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
						if($customer['customer_email'] == "") continue;
						if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customer['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

							//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

								$queue->add(

			                        time() + $timeSent * 60,

									$rule['rule_id'],

									$order->getId(),

									$emailTemplate['sender_name'],

									$emailTemplate['sender_email'],

									$customer['customer_name'],				

									$customer['customer_email'],

									$emailTemplate['subject'],				

									$content,

									serialize($params),

									$emailTemplate['code'],

									0,

									$code

		                    	);

							//}	

						}	                   			               

					}

				}				

			}		

		}		

	}

	

	public function eventNewPlaceOrderDeleteQueue($eventStatus,$email,$sku,$groupId = 0,$storeId = 0){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();		

		if(is_array($rules)){

			foreach($rules as $rule){								

				$cancelEvent = explode(',',$rule["cancel_event"]);			

				if(in_array($eventStatus,$cancelEvent)){

					$queue = Mage::getModel('followupemail/emailqueue');

					if($rule["event"] == MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_DOWNLOAD){

						$conditionsArr = unserialize($rule['conditions_serialized']);

						$collectionCondition = $conditionsArr['conditions'];

						foreach($collectionCondition as $condition){

							if($condition['attribute'] == "sku"){

								if(in_array($condition['value'],$sku)){

									$queueEmails = $queue->getCollection()

										->addFieldToFilter('rule_id', $rule['rule_id'])											

										->addFieldToFilter('recipient_email', $email)

										->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

									$queueEmails->load();

									

									foreach($queueEmails->getData() as $queueEmail){							

										 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

										 $deleteQueue->delete();

									}

								}

							}

						}						

					}	

					else{

						//$customerInfo = $this->_getCustomer($order->getCustomerId());										

						$queueEmails = $queue->getCollection()

							->addFieldToFilter('rule_id', $rule['rule_id'])											

							->addFieldToFilter('recipient_email', $email)

							->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

						$queueEmails->load();

						

						foreach($queueEmails->getData() as $queueEmail){							

							 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

							 $deleteQueue->delete();

						}	

					}													                   			              				

				}			

			}		

		}				

	}

	

	private function _checkAbandonedCarts()

    {       		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();

		$product = Mage::getModel('catalog/product');

				

        $resource = Mage::getSingleton('core/resource');

        $read = $resource->getConnection('core_read');



        $select = $read->select()

            ->from(array('q' => $resource->getTableName('sales/quote')), array(

            'store_id' => 'q.store_id',

            'quote_id' => 'q.entity_id',

            'customer_id' => 'q.customer_id',

            'subtotal' => 'q.subtotal',

            'subtotal_with_discount' => 'q.subtotal_with_discount',

            'grand_total' => 'q.grand_total',

            'items_qty' => 'q.items_qty',

            //'store_id' => 'q.store_id',

            'updated_at' => 'q.updated_at'))

            ->joinLeft(array('a' => $resource->getTableName('sales/quote_address')),

            'q.entity_id=a.quote_id AND a.address_type="billing"',

            array(

                'customer_email' => new Zend_Db_Expr('IFNULL(q.customer_email, a.email)'),

                'customer_firstname' => new Zend_Db_Expr('IFNULL(q.customer_firstname, a.firstname)'),

                'customer_middlename' => new Zend_Db_Expr('IFNULL(q.customer_middlename, a.middlename)'),

                'customer_lastname' => new Zend_Db_Expr('IFNULL(q.customer_lastname, a.lastname)'),

				'city' => 'a.city',

                'state' => 'a.region',

                'zipcode' => 'a.postcode',

                'country_id' => 'a.country_id',

            ))

            ->joinInner(array('i' => $resource->getTableName('sales/quote_item')), 'q.entity_id=i.quote_id', array(

            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),

            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)'),

			'sku' => new Zend_Db_Expr('GROUP_CONCAT(i.sku)'),

            'product_type' => new Zend_Db_Expr('GROUP_CONCAT(i.product_type)')

        	))

            ->where('q.is_active=1') 

			->where('q.updated_at > ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,

            $this->_now - ($this->_intTimeFrom+$this->_intTimeLast)))           

            /*->where('q.updated_at < ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,

            $now - $intTimeLastHour))*/			

            ->where('q.items_count>0')

            ->where('q.customer_email IS NOT NULL OR a.email IS NOT NULL')

            ->where('i.parent_item_id IS NULL')

            ->group('q.entity_id')

            ->order('updated_at');		

		//mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));

        $carts = $read->fetchAll($select);

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_ABANDON_CART,$storeId,$groupId)->getData();			

		foreach ($carts as $cart) {				

			$timeAbandonCart = strtotime($cart['updated_at'])+$this->_intTimeLast;

			if($timeAbandonCart > time()) continue;			

			$store = Mage::getModel('core/store')->load($storeId);

            $productIds = explode(',', $cart['product_ids']);   

			$customerInfo = $this->_getCustomer($cart['customer_id'],null);         

			foreach($rules as $rule){						

				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,$cart,$cart['customer_id'])) continue;

				$senderInfo = array();

				$senderInfo['sender_email'] = $rule['sender_email'];

				$senderInfo['sender_name'] = $rule['sender_name'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);					

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($cart['customer_email'],'cart',0);

					$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));					

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;

					$params['productIds'] = $productIds;

					$params['orderId'] = "";

					$params['cart'] = $cart;					

					$params['data'] = "";

					$params['customer'] = "";	

					$params['customerId'] = $cart['customer_id'];

					$params['storeId'] = $storeId;

					$params['code'] = "";				
					$params['codeCart'] = $code;				

					//$content = $this->_prepareContentEmailAbandonCart($emailTemplate,$cart,$customerInfo,$linkDirect,$productIds);

					$content = "";

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($cart['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$cart['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,1)){

						//if($this->_checkMailSentExist($rule['rule_id'],0,$cart['customer_email'],$emailTemplate['code'],1)){

							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$cart['customer_firstname'].' '.$cart['customer_lastname'],				

								$cart['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								1,

								$code

	                    	);	

						//}

					}                   			               

				}

			}			

		}		

    }

	

	public function eventDeleteQueue($arvgs){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;

		$order = $arvgs->getOrder();

		$eventStatus = "order_status_".$order->getStatus();		

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = $order->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){								

				$cancelEvent = explode(',',$rule["cancel_event"]);			

				if(in_array($eventStatus,$cancelEvent)){					

		            $queue = Mage::getModel('followupemail/emailqueue');		           				

						$queueEmails = $queue->getCollection()

							->addFieldToFilter('rule_id', $rule['rule_id'])

							->addFieldToFilter('order_id', $order->getId())

							->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);							

						$queueEmails->load();

						

						foreach($queueEmails->getData() as $queueEmail){							

							 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);							 

							 $deleteQueue->delete();

						}								                   			              					

				}			

			}		

		}		

		else{

			if($eventStatus == $rules['event']){

				$queueEmailCollection = Mage::getModel('followupemail/rules')->getCollection();

			}

		}	

	}

	

	protected function _checkExistQueueEmail($ruleId,$orderId,$email,$templateEmailId,$scheduledAt,$isabandoncart){
		
		//return true;

		$queueEmailCollection = Mage::getModel('followupemail/emailqueue')->getCollection();		

        $queueEmails = $queueEmailCollection->getQueueEmail($ruleId,$orderId,$email,$templateEmailId,$isabandoncart,$this->_dontsendemailtime,$scheduledAt)->getData();	

		if(count($queueEmails) > 0){

			return false;		

		}

		return true;

	}

		

	protected function _checkMailSentExist($ruleId,$orderId,$email,$templateEmailId,$isabandoncart){

		$queueEmailCollection = Mage::getModel('followupemail/emailqueue')->getCollection();		

        $queueEmails = $queueEmailCollection->getMailSentExist($ruleId,$orderId,$email,$templateEmailId,$isabandoncart)->getData();			

		if(count($queueEmails) > 0){

			foreach($queueEmails as $queueEmail){

				$timeSent = strtotime($queueEmail['sent_at'])+$this->_dontsendemailhours;				

				if($timeSent > $this->_now) return false;

			}			

		}

		return true;		

	}	

	

	public function mailFreeDownload($arvgs){		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		
		$this->_intTime();

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_DOWNLOAD);

		Mage::helper('customerlogs')->addActivity('Free Download', 'Free Download', $data['sku'], $data['title_download']);	

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_DOWNLOAD,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";																

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";							

					$params['storeId'] = $storeId;

					$params['code'] = $code;		

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

						//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku']

	                    	);	

						//}

					}                 			               

				}

			}

		}

	}

	

	public function mailFreeTrialRequest($arvgs){		

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REQUEST);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Free Installation', $data['sku'], $data['title_download']);	

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REQUEST,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;					

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

						//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku']

	                    	);	

						//}

					}                 			               

				}

			}

		}

	}

	

	public function mailFreeTrialRemind($arvgs){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();		

		$data = $arvgs->getData();		

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REMIND);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Remind', $data['sku'], $data['title_download']);	

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REMIND,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;					

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";										

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

						//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku']

	                    	);

						//}	

					}                 			               

				}

			}

		}

	} 

	

	public function mailFreeTrialDownload($arvgs){	
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();		

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_DOWNLOAD);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Download', $data['sku'], $data['title_download']);	

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_DOWNLOAD,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;						

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					if($data['customer_email'] == "") continue;

					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],time() + $timeSent * 60,0)){

						//if($this->_checkMailSentExist($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku']

	                    	);	

						//}

					}                 			               

				}

			}

		}				

	}

	

	protected function deleteQueueFreeTrial($data,$event){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($event,$storeId,$groupId)->getData();					

		if(is_array($rules)){

			foreach($rules as $rule){														

	            $queue = Mage::getModel('followupemail/emailqueue');		           				

				$queueEmails = $queue->getCollection()

					->addFieldToFilter('rule_id', $rule['rule_id'])

					->addFieldToFilter('recipient_email', $data["customer_email"])

					->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

				$queueEmails->load();						

				foreach($queueEmails->getData() as $queueEmail){							

					$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

					$deleteQueue->delete();					

				}								                   			              									

			}		

		}

	}	

	

	public function _getCustomer($customerId,$order){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$customerInfo = array();		

        if ($customerId) {

            $customer = Mage::getModel('customer/customer')->load($customerId);

			if ($customer) {    				 

	            $middlename = $customer->getMiddlename();

	            $customerInfo['customer_name'] = $customer->getFirstname() . ' ' . ($middlename ? $middlename . ' ' : '') . $customer->getLastname();

				$customerInfo['first_name'] = $customer->getFirstname();

				$customerInfo['last_name'] = $customer->getLastname();

	            $customerInfo['customer_email'] = $customer->getEmail();

				$customerAddressId = $customer->getDefaultBilling();

				$address = array();

				$htmlAddress = "";

				if ($customerAddressId){

				       $address = Mage::getModel('customer/address')->load($customerAddressId);

				       $htmlAddress = $address->format('html');

				} 

				$customerInfo['default_address'] = $htmlAddress;

				$customerInfo['city'] = $address['city'];

				$customerInfo['state'] = $address['region'];

				$customerInfo['zip_code'] = $address['postcode'];

				$countryName = Mage::getModel('directory/country')->load($address['country_id'])->getName(); 

				$customerInfo['country'] = $countryName;

	        }

        }
		else{
			if($order != null){
				$orderAddress = MW_FollowUpEmail_Helper_Data::getOrderAddress($order, 'billing');
	            if (!$orderAddress)
	                $orderAddress = MW_FollowUpEmail_Helper_Data::getOrderAddress($order, 'shipping');
	     
				$middlename = $orderAddress->getMiddlename();
				$customerInfo['customer_name'] = $orderAddress->getFirstname() . ' ' . ($middlename ? $middlename . ' ' : '') . $orderAddress->getLastname();				
				$customerInfo['customer_email'] = $order->getCustomerEmail();
				$customerInfo['first_name'] = $orderAddress->getFirstname();
				$customerInfo['last_name'] = $orderAddress->getLastname();
				$customerInfo['default_address'] = "";
				$customerInfo['city'] = $orderAddress->getCity();
				$customerInfo['state'] = $orderAddress->getRegionId();
				$customerInfo['zip_code'] = $orderAddress->getPostcode();
				$countryName = Mage::getModel('directory/country')->load($orderAddress->getCountryId())->getName(); 
				$customerInfo['country'] = $countryName;	
			}			
		}

        return $customerInfo;

	}		   	

	

	public function cartUpdated($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_CART_UPDATED;

		if(Mage::getSingleton('customer/session')->isLoggedIn()) {

			$customer = Mage::getSingleton('customer/session')->getCustomer();

			$email = $customer->getEmail();		

			$storeId = Mage::app()->getStore()->getStoreId();

			$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

			$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

	        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();			

			if(is_array($rules)){

				foreach($rules as $rule){														

		            $queue = Mage::getModel('followupemail/emailqueue');		           				

					$queueEmails = $queue->getCollection()

						->addFieldToFilter('rule_id', $rule['rule_id'])

						->addFieldToFilter('recipient_email', $email)

						->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

					$queueEmails->load();						

					foreach($queueEmails->getData() as $queueEmail){							

						 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

						 $deleteQueue->delete();

					}								                   			              									

				}		

			}

		}

	}

	

	public function customerLogin($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$customer = $arvgs->getCustomer();

		$data = $customer->getData();

		$session = Mage::getSingleton('core/session');	

		if($session->getCheckFollowUpEmail() == ""){

			$session->setCheckFollowUpEmail($data['email']);

			$this->eventLoginDelete($data);						

		}			

	}

	

	public function customerLogout($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$session = Mage::getSingleton('core/session');	

		$session->setCheckFollowUpEmail("");

	}

	public function customerUpdated($arvgs){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();
				
		if($arvgs->getCustomer() != ""){
			$customerSession = $arvgs->getCustomer()->getData();	
		}		
		else{
			$customerSession = Mage::helper('customer')->getCustomer()->getData();	
		}

		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::CUSTOMER_ACCOUNT_UPDATED;		

		$storeId = $customerSession['store_id'];

		$groupId = $customerSession['group_id'];

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();	
		
		$customerInfo = $this->_getCustomer($customerSession['entity_id'],null);		

		if(is_array($rules)){

			foreach($rules as $rule){	
			
				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,null,$customerSession['entity_id'])) continue;

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];													

	            $emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;

					$params['productIds'] = "";

					$params['orderId'] = "";

					$params['data'] = "";

					$params['storeId'] = $storeId;

					$params['code'] = $code;

					$params['customer'] = $customerInfo;

					$params['customerId'] = $customerSession['entity_id'];

					$params['cart'] = "";

					//$content = $this->_prepareContentEmail($emailTemplate,array(),0,$customerInfo,"");					

					$content = "";					

					if($customerSession['email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$customerSession['email'],$emailTemplate['code'],time() + $timeSent * 60,0)){
					$queue->add(

                        time() + $timeSent * 60,

						$rule['rule_id'],

						0,

						$emailTemplate['sender_name'],

						$emailTemplate['sender_email'],

						$customerSession['firstname'].' '.$customerSession['lastname'],				

						$customerSession['email'],

						$emailTemplate['subject'],				

						$content,

						serialize($params),

						$emailTemplate['code'],

						1,

						$code

                	);	
					
					}					                 			              

				}								                   			              									

			}		

		}	
	}

	public function newCustomerSignedUp($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();
		if($arvgs->getCustomer() != ""){
			$customer = $arvgs->getCustomer()->getData();
		}
		else{
			$customer = Mage::helper('customer')->getCustomer()->getData();	
		}
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::NEW_CUSTOMER_SIGNED_UP;		

		$storeId = $customer['store_id'];

		$groupId = $customer['group_id'];

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();	

		$customerInfo = $this->_getCustomer($customer['entity_id'],null);		

		if(is_array($rules)){

			foreach($rules as $rule){	
			
				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,null,$customer['entity_id'])) continue;

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];													

	            $emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;

					$params['productIds'] = "";

					$params['orderId'] = "";

					$params['data'] = "";

					$params['storeId'] = $storeId;

					$params['code'] = $code;

					$params['customer'] = $customerInfo;

					$params['customerId'] = $customer['entity_id'];

					$params['cart'] = "";

					//$content = $this->_prepareContentEmail($emailTemplate,array(),0,$customerInfo,"");					

					$content = "";					

					if($customer['email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$customer['email'],$emailTemplate['code'],time() + $timeSent * 60,0)){
					$queue->add(

                        time() + $timeSent * 60,

						$rule['rule_id'],

						0,

						$emailTemplate['sender_name'],

						$emailTemplate['sender_email'],

						$customer['firstname'].' '.$customer['lastname'],				

						$customer['email'],

						$emailTemplate['subject'],				

						$content,

						serialize($params),

						$emailTemplate['code'],

						1,

						$code

                	);
					}						                 			              

				}								                   			              									

			}		

		}		

	}

	

	public function eventLoginDelete($data){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::CUSTOMER_LOGGED_IN;		

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();			

		if(is_array($rules)){

			foreach($rules as $rule){														

	            $queue = Mage::getModel('followupemail/emailqueue');		           				

				$queueEmails = $queue->getCollection()

					->addFieldToFilter('rule_id', $rule['rule_id'])

					->addFieldToFilter('recipient_email', $data["email"])

					->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

				$queueEmails->load();						

				foreach($queueEmails->getData() as $queueEmail){							

					$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

					$deleteQueue->delete();

					$quote = Mage::getSingleton('checkout/session')->loadCustomerQuote();

					$quoteId = $quote->getQuote()->getId();

					//mage::log($quote);

					$conn     = Mage::getModel('core/resource')->getConnection('core_write');

					$resource = Mage::getSingleton('core/resource');

					$tblQuote = $resource->getTableName('sales/quote');

					$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

					$currentDate = date($dateFormatIso, time());

					$sql      = "UPDATE `$tblQuote` SET `updated_at`='$currentDate' WHERE `entity_id`='$quoteId'";

					$conn->query($sql);

				}								                   			              									

			}		

		}		

	}

	

	public function runCron()

    {		
		
		$this->_intTime();

		$this->_checkAbandonedCarts();		

        $currenttime = date('Y-m-d H:i:s',time());    	

    	$queueEmails = Mage::getModel('followupemail/emailqueue')->getCollection()

											->addFieldToFilter('status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY)

											->addFieldToFilter('scheduled_at',array('to' => $currenttime));

		foreach($queueEmails->load()->getData() as $d){

      	//Mage::log("FUE:runCron:".$d['queue_id']);

			$emailBefore = Mage::getModel('followupemail/emailqueue')->load($d['queue_id']);

      		$params = $emailBefore->getParams();      

			if (@unserialize($params) === FALSE){

        		//Mage::log("FUE:runCron:".$d['queue_id'].':updateparams');

				Mage::getModel('followupemail/observer')->updateParamsEmail($emailBefore);

			}			

			$result = Mage::getModel('followupemail/emailqueue')->load($d['queue_id'])->send();	
			if($result === true){}			
			else if($result == 3){
				Mage::log("FUE log:");
				Mage::log("Email ".$d['recipient_email']." sent error because:");
				Mage::log("This email is not sent to customer neither BBCed to anyone.");
			}
			else{
				Mage::log("FUE log:");
				Mage::log("Email ".$d['recipient_email']." ccould not be sent.");				
			}

		}

    }
	
	public function runCronCleanMail(){
		$this->_intTime();
		$time = date('Y-m-d H:i:s',time() - $this->_intTimeCleanMail);    	

    	$queueEmails = Mage::getModel('followupemail/emailqueue')->getCollection()
											->addFieldToFilter('sent_at',array('to' => $time));
											$queueEmails->getSelect()->where('status = 2 or status = 3');											

		foreach($queueEmails->load()->getData() as $d){

			$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($d['queue_id']);

			$deleteQueue->delete();

		}
	}
	
	

	// Function update param fix bug

	public function updateParamsEmail($email){

		$model = Mage::getModel('followupemail/emailqueue');

		$ruleId = $email->getRuleId();

		$orderId = $email->getOrderId();

		$productIds = array();

		$customerId = array();

		$cart = null;

		if($orderId > 0){

			$order = Mage::getModel('sales/order')->load($orderId);

			$items = $order->getAllItems();

			foreach ($items as $itemId => $item){              
				if ($item->getParentItem()) continue;
               $productIds[]=$item->getProductId();               

            }

			$storeId = $order->getStoreId();

			$customerId = $order->getCustomerId();

		}

		else{

			$orderId = "";

			$resource = Mage::getSingleton('core/resource');

	        $read = $resource->getConnection('core_read');

	        $select = $read->select()

	            ->from(array('q' => $resource->getTableName('sales/quote')), array(

	            'store_id' => 'q.store_id',

	            'quote_id' => 'q.entity_id',

	            'customer_id' => 'q.customer_id',

	            'subtotal' => 'q.subtotal',
				
				'subtotal_with_discount' => 'q.subtotal_with_discount',

            	'grand_total' => 'q.grand_total',

	            'items_qty' => 'q.items_qty',

	            //'store_id' => 'q.store_id',

	            'updated_at' => 'q.updated_at'))

	            ->joinLeft(array('a' => $resource->getTableName('sales/quote_address')),

	            'q.entity_id=a.quote_id AND a.address_type="billing"',

	            array(

	                'customer_email' => new Zend_Db_Expr('IFNULL(q.customer_email, a.email)'),

	                'customer_firstname' => new Zend_Db_Expr('IFNULL(q.customer_firstname, a.firstname)'),

	                'customer_middlename' => new Zend_Db_Expr('IFNULL(q.customer_middlename, a.middlename)'),

	                'customer_lastname' => new Zend_Db_Expr('IFNULL(q.customer_lastname, a.lastname)'),

	            ))

	            ->joinInner(array('i' => $resource->getTableName('sales/quote_item')), 'q.entity_id=i.quote_id', array(

	            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),

	            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)')

	        ))

	            ->where('q.is_active=1') 

				->where('q.customer_email = ?',$email->getRecipientEmail())           

	            /*->where('q.updated_at < ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,

	            $now - $intTimeLastHour))*/			

	            ->where('q.items_count>0')	            

	            ->where('i.parent_item_id IS NULL')

	            ->group('q.entity_id')

	            ->order('updated_at');		

			//mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));

	        $carts = $read->fetchAll($select);

			foreach ($carts as $_cart) {				

	            $productIds = explode(',', $_cart['product_ids']);  

				$cart = $_cart;

				$customerId = $_cart['customer_id'];

				$storeId = $_cart['store_id'];

			}

		}		

		$emailTemplate  = Mage::getModel('core/email_template')->loadByCode($email->getEmailtemplateId());		

		$rule = Mage::getModel('followupemail/rules')->load($ruleId);

		$senderInfo = array();

		$senderInfo['sender_name'] = $rule->getSenderName();

		$senderInfo['sender_email'] = $rule->getSenderEmail();

		$params = array();

		$params['templateEmailId'] = 'email:'.$emailTemplate->getTemplateId();

		$params['senderInfo'] = $senderInfo;

		$params['productIds'] = $productIds;

		$params['orderId'] = $orderId;						

		$params['data'] = "";

		$params['customer'] = "";

		$params['customerId'] = $customerId;

		$params['cart'] = $cart;

		$params['storeId'] = $storeId;

		$params['code'] = $email->getCode();

		$model->load($email->getQueueId());

		$model->setParams(serialize($params));

		//$model->setParams("");

		$model->save();

	}
	
	public function checkLicense($o)
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mwcore', $modules2) || !$modulesArray['MW_Mwcore']->is('active') || Mage::getStoreConfig('mwcore/config/enabled')!=1)
		{
			Mage::helper('followupemail')->disableConfig();
		}
		
	}

}