<?php
class AsiaConnect_Gallery_Controller_Action extends Mage_Core_Controller_Front_Action
{
    protected function _addCrumb($breadcrumbsBlock,$label, $title, $link=null,$first = false, $last = false, $readOnly = false)
    {
        $breadcrumbsBlock->addCrumb($label, array('label'=>$label, 'title'=>$title, 'link'=>$link,'first'=>$first,'last'=>$last,'readonly'=>$readOnly));
        return $this;
    }
}