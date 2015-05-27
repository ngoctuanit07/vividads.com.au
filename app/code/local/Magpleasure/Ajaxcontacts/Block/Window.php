<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Ajaxcontacts
 * @version    1.0
 * @copyright  Copyright (c) 2011 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Ajaxcontacts_Block_Window extends Mage_Core_Block_Template
{
    const TEMPLATE_PATH = 'ajaxcontacts/window.phtml';

    /**
     * Helper
     *
     * @return Magpleasure_Ajaxcontacts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('ajaxcontacts');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_PATH);
    }

    public function getContactsContent()
    {
        $html = "";
        $contacts = $this->getLayout()
                        ->createBlock('core/template')
                        ->setTemplate('contacts/form.phtml')
                        ->setFormAction($this->getUrl('ajaxcontacts/index/post'))
                    ;
        if ($contacts){
            $html = $contacts->toHtml();
            $dom = $this->_helper()->getSimpledom()->str_get_html($html);
            foreach ($dom->find("div[class=buttons-set]") as $element){
                foreach ($element->find("button[type=submit]") as $button){
                    $button->outertext = '';
                }
                foreach ($element->find("input[type=image]") as $button){
                    $button->outertext = '';
                }
            }
            foreach ($dom->find("div[class=button-set]") as $element){
                foreach ($element->find("button[type=submit]") as $button){
                    $button->outertext = '';
                }
                foreach ($element->find("input[type=image]") as $button){
                    $button->outertext = '';
                }
            }
            $html = $dom->__toString();
        }
        return $html;
    }


}