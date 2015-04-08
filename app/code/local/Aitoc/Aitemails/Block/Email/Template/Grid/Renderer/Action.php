<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitemails_Block_Email_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
    	$actions = array();

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');
        $aParams    = array();
        
        if ($curWebsite)
        {
            $aParams['website'] = $curWebsite;
        }
        if ($curStore)
        {
            $aParams['store'] = $curStore;
        }
        if (!$aParams)
        {
            list($aParams['scope'],$aParams['scopeid']) = Mage::getModel('aitemails/aitemails')->getCurrentScope();
        }
        $aParams['fromaitemails'] = 1;
            
        if ($row->getCustomTemplate())
        {
            $aParams['id'] = $row->getCustomTemplateId();
            
    	    $actions[] = array(
    		    'url'		=>  $this->getUrl('adminhtml/system_email_template/edit', $aParams),
	    	    'caption'	=>	$this->__('Edit Custom Template')
    	    );
        } else 
        {
            $aParams['templatecode'] = $row->getCode();
            $aParams['localecode'] = Mage::registry('aitemails_email_template_scope_locale');
            $aParams['scope'] = $row->getScope();
            $aParams['scopeid'] = $row->getScopeId();
            
            $actions[] = array(
                'url'        =>  $this->getUrl('adminhtml/system_email_template/new', $aParams),
                'caption'    =>    $this->__('Create Custom Template')
            );
        }

        $this->getColumn()->setActions($actions);

    	return parent::render($row);
    }

    protected function _getEscapedValue($value)
    {
    	return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
    	$html = array();
    	$attributesObject = new Varien_Object();
    	foreach ($actions as $action) {
    		$attributesObject->setData($action['@']);
    		$html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
    	}
    	return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }
}