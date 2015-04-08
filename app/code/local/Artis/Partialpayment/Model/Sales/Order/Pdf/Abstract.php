<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Artis_Partialpayment_Model_Sales_Order_Pdf_Abstract extends Mage_Sales_Model_Order_Pdf_Invoice
{
    

    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
                    
                    /*************** dec code by start *****************/
                    if($totalData['label'] == 'Grand Total:')
                    {
                        $lineBlock['lines'][] = array(
                                array(
                                    'text'      => 'Total Paid :',
                                    'feed'      => 475,
                                    'align'     => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font'      => 'bold'
                                ),
                                array(
                                    'text'      => Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().number_format(abs($order->getTotalPaid()), 2, '.', ' '),
                                    'feed'      => 565,
                                    'align'     => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font'      => 'bold'
                                ),
                            );
                        
                        $lineBlock['lines'][] = array(
                                array(
                                    'text'      => 'Total Due :',
                                    'feed'      => 475,
                                    'align'     => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font'      => 'bold'
                                ),
                                array(
                                    'text'      => Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().number_format(abs($order->getTotalDue()), 2, '.', ' '),
                                    'feed'      => 565,
                                    'align'     => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font'      => 'bold'
                                ),
                            );
                    }
                    /*************** dec code by end *****************/
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }

    
}
