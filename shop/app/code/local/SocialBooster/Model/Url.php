<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */
class MageWorx_SocialBooster_Model_Url implements Zend_Filter_Interface {

    const REGEX = "~(?:https?://(?:(?:(?:(?:(?:[a-zA-Z\d](?:(?:[a-zA-Z\d]|-)*[a-zA-Z\d])?)\.)*(?:[a-zA-Z](?:(?:[a-zA-Z\d]|-)*[a-zA-Z\d])?))|(?:(?:\d+)(?:\.(?:\d+)){3}))(?::(?:\d+))?)(?:/(?:(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*)(?:/(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*))*)(?:\?(?:(?:(?:[a-zA-Z\d$\-_.+!*'(),]|(?:%[a-fA-F\d]{2}))|[;:@&=])*))?)?)~";
    const SERVICE_ISGD    = 'http://is.gd/api.php?longurl=%s';
    const SERVICE_TINYURL = 'http://tinyurl.com/api-create.php?url=%s';

    public $options = array(
        'maxsize' => 35,
        'service' => self::SERVICE_ISGD,
        'timeout' => 5
    );

    public function __construct(array $options = array()) {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function filter($text) {
        $url = $text;
        $matches = $replacements = array();
        $http = new Zend_Http_Client();
        if (strlen($url) > $this->options['maxsize']) {
            $http->setConfig(array(
                'timeout' => $this->options['timeout']
            ));
            $http->setUri(
                sprintf($this->options['service'], urlencode($url))
            );
            $response = $http->request(Zend_Http_Client::GET);
            if ($response->isSuccessful()){
                $replacements[$url] = $response->getBody();
            }
            $http->resetParameters();
        }

        if (empty($replacements)) {
            return $text;
        }
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $text
        );
    }
}
