<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Model_Boards extends Mage_Core_Model_Abstract 
{
    
    public function setOptionArray() {
        
        $options = array();
        $session = Mage::getSingleton('core/session');
        $pinterestBoards = $session->getPinterestBoards();
        if(is_array($pinterestBoards) && isset($pinterestBoards[0]['value'])) {
            $boards = $pinterestBoards;
        } else {
            $email = $this->_getConfig("social/pinterest/email");
            $pass = $this->_getConfig("social/pinterest/password");
            $pin = Mage::getModel('social/autopin');            
            $loginError = $pin->loginToPinterest($email, $pass);
            if (!$loginError) {        
                $boards = $pin->getBoards();             
                $session->setPinterestBoards($boards);
            } else {
                Mage::log("Pinterest error: ".$loginError);
            }
        }
        
        foreach ((array)$boards as $board){
            $options[] = array(
                       'label' => $board['name'],
                       'value' => $board['value']
                );
        }
                    
        $session->setPinterestBoardOptions($options);        
        //Mage::log($options);
        
        return $options;
        
    }
    
    private function _getConfig($code) {
        return Mage::getStoreConfig($code);
    }
    
}
?>
