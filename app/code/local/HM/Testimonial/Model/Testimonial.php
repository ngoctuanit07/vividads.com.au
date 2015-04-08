<?php

class Hm_Testimonial_Model_Testimonial extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('testimonial/testimonial');
    }
    public function validate()
    {
        $errors = array();

        $helper = Mage::helper('testimonial');
        
        if (!Zend_Validate::is($this->getClientName(), 'NotEmpty')) {
            $errors[] = $helper->__('Name is a required field');
        }
        
        
        if (!Zend_Validate::is($this->getDescription(), 'NotEmpty')) {
            $errors[] = $helper->__('Testimonial is a required field');
        }
         if ($this->getEmail() != ''){
            if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = $helper->__('Wrong email');
            }
        }
        if ($this->getWebsite() != ''){
            if (!Zend_Validate::is($this->getWebsite(), 'Hostname')){
                $errors[] = $helper->__('Wrong website');
            }
        }
        if (Mage::getStoreConfig('hm_testimonial/general/enable')!=false){
            if (!Zend_Validate::is($this->getSecurityCode(), 'NotEmpty')) {
                $errors[] = $helper->__('Security Code is a required field');
            }
            else{
                if ($this->getCodemd5() != ''){
                    if(md5($this->getSecurityCode()) != $this->getCodemd5()) {
                        $errors[] = $helper->__('Security code is incorrect');
                    }
                }
            }
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
    
    public function word_trim($string, $count, $ellipsis = FALSE){
    $words = explode(' ', $string);
    if (count($words) > $count){
      array_splice($words, $count);
      $string = implode(' ', $words);
      if (is_string($ellipsis)){
        $string .= $ellipsis;
      }
      elseif ($ellipsis){
        $string .= '&hellip;';
      }
    }
    return $string;
    }
    
    
}