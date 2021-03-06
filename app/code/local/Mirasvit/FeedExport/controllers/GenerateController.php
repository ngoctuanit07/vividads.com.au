<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.2
 * @build     551
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_GenerateController extends Mage_Core_Controller_Front_Action
{
    public function runAction()
    {
        try {
            $feed = $this->_getFeedModel();
            $mode = $this->getRequest()->getParam('mode');
            if (!$mode || $mode == 'new') {
                $feed->getGenerator()->getState()->reset();
            }

            $this->getResponse()->clearBody();

            $result = array(
                'success' => true,
                'message' => '',
            );

            $feed->generate();
            $state = $feed->getGenerator()->getState();

            $result['status']  = $state->getStatus();
            $result['success'] = true;

            if ($state->isReady()) {
                $result['message'] = __('Feed file was generated at <a target="_blank" href="'.$feed->getUrl().'?rand='.microtime(true).'">'.$feed->getUrl().'</a>');
            } elseif ($state->isProcessing() || $state->isError()) {
                $result['message'] = nl2br($feed->getGenerator()->getState()->toHtml());
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    protected function _getFeedModel()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $feed = Mage::getModel('feedexport/feed')->load($id);
            Mage::register('current_feed', $feed);

            return $feed;
        }

        return null;
    }
}