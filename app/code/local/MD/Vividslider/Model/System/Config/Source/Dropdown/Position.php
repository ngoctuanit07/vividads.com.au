<?php
/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
class MD_Vividslider_Model_System_Config_Source_Dropdown_Position
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'top',
                'label' => 'Top',
            ),
            array(
                'value' => 'bottom',
                'label' => 'Bottom',
            ),
            array(
                'value' => 'lefttop',
                'label' => 'Top at Left side',
            ),
            array(
                'value' => 'righttop',
                'label' => 'Top at Right side',
            ),
            array(
                'value' => 'leftbottom',
                'label' => 'Bottom at Left side',
            ),
            array(
                'value' => 'rightbottom',
                'label' => 'Bottom at Right side',
            )
        );
    }
}