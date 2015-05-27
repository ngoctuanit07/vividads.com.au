<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'OnepageController.php';

class Fishpig_Proofs_OnepageController extends Mage_Checkout_OnepageController
{
    
    /**
     * Create order action
     */
    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
            $this->getOnepage()->saveOrder();
            
            /************************************* Start by Dev *********************************************/
            $order = new Mage_Sales_Model_Order();
            $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order->loadByIncrementId($incrementId);
            
            $items = $order->getAllItems();
            foreach ($items as $item) {
                
                $ProductId = $item->getProductId();
                
                $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                {
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                    {
                        $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
                        $chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
                    }
                    
                    if($chkOrganiger)
                    {
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                        {
                            $sqlOrganiger1="INSERT INTO ".$temptableOrganiger." SET ot_created_at = NOW(), 
                                           ot_author_user = '".$chkOrganiger[0]['ot_author_user']."' ,
                                           ot_target_user ='".$chkOrganiger[0]['ot_target_user']."', 
                                           ot_caption= '".addslashes($chkOrganiger[0]['ot_caption'])."', 
                                           ot_description = '".addslashes($chkOrganiger[0]['ot_description'])."', 
                                           ot_deadline = '".$chkOrganiger[0]['ot_deadline']."', 
                                           ot_notify_date = '".$chkOrganiger[0]['ot_notify_date']."', 
                                           ot_priority = '".$chkOrganiger[0]['ot_priority']."', 
                                           ot_finished = '".$chkOrganiger[0]['ot_finished']."', 
                                           ot_read ='".$chkOrganiger[0]['ot_read']."', 
                                           ot_origin ='".$chkOrganiger[0]['ot_origin']."', 
                                           ot_category = '".$chkOrganiger[0]['ot_category']."', 
                                           ot_entity_type ='order', 
                                           ot_entity_id = '".$order->getId()."', 
                                           ot_entity_description = '".addslashes($chkOrganiger[0]['ot_entity_description'])."', 
                                           ot_notification_read = '".$chkOrganiger[0]['ot_notification_read']."',
                                           ot_task_type = '".$chkOrganiger[0]['ot_task_type']."'";
                                           
                           $chkOrganiger1 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
                        }
                        
                        //For chain task
                        $last_id = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
                        
                        $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
                        {
                            $sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
                                            order_quote_id = '".$order->getId()."' ,
                                            product_id ='".$chkOrganiger[0]['ot_entity_id']."', 
                                            task_type = '".$chkOrganiger[0]['ot_task_type']."'";
                                            
                            $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
                        }
                    }
                }
                
                /*********************** add planning auto *********************************/
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                {
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                    {
                        $sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
                        $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipping);
                    }
                    
                    if(count($chkShipping) == 0)
                    {
                    
                        $created_date = $order->getCreatedAt();
                        
                        $Product = Mage::getModel('catalog/product')->load($ProductId);
                        
                        $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                        {
                        $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                        $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                        }
                        $order_placed_date =  $created_date;
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                        
                                          
                        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                        
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                        {
                            $sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                            $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                        }
                    }
                }
                
               
            
                /*********************** add planning auto *********************************/
                
                /************************ Get custom option value ******************************/
                
                $_options = $item->getProductOptions();
           
                foreach($_options as $o => $option){
                   
                    foreach($option as $optionvalue)
                    {
                        if($optionvalue['label'] == 'Graphic Design Service'){
                           
                            if($optionvalue['value'] != '')
                            {
                                $title = explode(' ',$optionvalue['value']);
                                
                                if (is_numeric($title[0]))
                                $revison_number = $title[0];
                                else
                                $revison_number = 10000;
                            }
                        }
                    }
                }
                 
                $temptableProduct=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProduct))
                {
                    $sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
                    $chkProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlProduct);
                }
                
                $adminid = $chkProduct[0]['user_id'];
                 
                $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableDesign))
                {
                    $sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$order->getId()."', type='order', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$adminid."', postdate = NOW() ";
                    $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                }
                
                /************************ Get custom option value ******************************/
            }  
            
            /************************************* End by dev ********************************************/

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    
    /***************************** Add custom function ***********************************/
     public function isweekend($date){
     $date = strtotime($date);
     $date = date("l", $date);
     $date = strtolower($date);
     if($date == "sunday"){
      return 1;
     } else {
      return 0;
     }
    }
    
    public function gettimelinedate($day_delay,$created_date,$sunday,$holiday)
    {
        if($sunday == 0 and $holiday == 0)
        {
            $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
        }
        else
        {
            if($sunday == 1)
            {
                $flag = 0;
                $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
                
                $d = $this->isweekend($artwork_date);
                if($holiday == 1)
                {
                    while($flag == 0)
                    {
                        $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                        
                        $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                        $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                        
                        if(count($chkHoliday) > 0)
                        {
                            $d++;
                        }
                        else
                        {
                           $flag = 1; 
                        }
                    }
                    
                }
                else
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                }
                
            }
            else if($holiday == 1)
            {
                $flag = 0;
                $d = 0;
                while($flag == 0)
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                    
                    $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                    $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                    
                    if(count($chkHoliday) > 0 or ($sunday == 1 and $this->isweekend($artwork_date) == 1))
                    {
                        $d++;
                    }
                    else
                    {
                       $flag = 1; 
                    }
                }
            }
            
        }
        
        return $artwork_date;
    }
    /***************************** Add custom function ***********************************/

   
}
