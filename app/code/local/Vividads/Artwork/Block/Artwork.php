<?php

class Vividads_Artwork_Block_Test extends Mage_Core_Block_Abstract
{
  
  //public function __construct()
  //  {
  //
  //
  //  $this->setTemplate('artwork/artwork_files.phtml');
  //
  //  }
  
  protected function _toHtml()
  {
     // put here your custom PHP code with output in $html;
     // use arguments like $this->getMyParam1() , $this->getAnotherParam()
	$html="<h1>Naseeb saaday likhay rab nay</h1>";
     return $html;
  }
}

