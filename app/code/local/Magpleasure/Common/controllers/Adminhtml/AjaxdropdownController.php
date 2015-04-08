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
 * @package    Magpleasure_Common
 * @version    0.6.12
 * @copyright  Copyright (c) 2012-2013 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Adminhtml_AjaxdropdownController extends Magpleasure_Common_Controller_Adminhtml_Action
{

    /**
     * Ajax Dropdown List Action
     */
    public function listAction()
    {
        $result = array();

        /** @var $dataSource Magpleasure_Common_Model_Datasource */
        $dataSource = Mage::getModel('magpleasure/datasource')->setParams(array_merge(
            array(
                'query' => $this->getRequest()->getParam('q'),
                'limit' => $this->getRequest()->getParam('l') ? $this->getRequest()->getParam('l') : 10,
                'page' => $this->getRequest()->getParam('p') ? $this->getRequest()->getParam('p') : 1,
            ),
            $this->_commonHelper()->getHash()->getData($this->getRequest()->getParam('h'))
        ));
        $result['data'] = $dataSource->getArrayData();
        $this->_jsonResponse($result);
    }
}