<?php
/**
 * Customer account navigation sidebar - adding removeLinkByName method
 *
 * @category   WL
 * @package    WL_Common
 */

class WL_Common_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
	public function removeLinkByName($name) {
		unset($this->_links[$name]);
	}
}
