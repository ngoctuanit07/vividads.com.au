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

class MD_Vividslider_Model_System_Config_Source_Dropdown_Animations
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'random',
                'label' => 'Full Random',
            ),
            array(
                'value' => 'slideInRight',
                'label' => 'Slide In Right',
            ),
            array(
                'value' => 'slideInLeft',
                'label' => 'Slide In Left',
            ),
            array(
                'value' => 'slideInRighteaseOutBounce',
                'label' => 'Bouncing with Slide In Right',
            ),
            array(
                'value' => 'slideInLefteaseOutBounce',
                'label' => 'Bouncing with Slide In Left',
            ),
            array(
                'value' => 'slideInRighteaseInOutCirc',
                'label' => 'Braking with Slide In Right',
            ),
            array(
                'value' => 'slideInLefteaseInOutCirc',
                'label' => 'Braking with Slide In Left',
            ),
            array(
                'value' => 'sliceDown',
                'label' => 'Slice Down',
            ),
            array(
                'value' => 'sliceDownLeft',
                'label' => 'Slice Down Left',
            ),
            array(
                'value' => 'sliceUp',
                'label' => 'Slice Up',
            ),
            array(
                'value' => 'sliceUpLeft',
                'label' => 'Slice Up Left',
            ),
            array(
                'value' => 'sliceUpDown',
                'label' => 'Slice Up Down',
            ),
            array(
                'value' => 'sliceUpDownLeft',
                'label' => 'Slice Up Down Left',
            ),
            array(
                'value' => 'fold',
                'label' => 'Fold',
            ),
            array(
                'value' => 'fade',
                'label' => 'Fade',
            ),
            array(
                'value' => 'boxRandom',
                'label' => 'Box Random',
            ),
            array(
                'value' => 'boxRain',
                'label' => 'Box Rain',
            ),
            array(
                'value' => 'boxRainReverse',
                'label' => 'Box Rain Reverse',
            ),
            array(
                'value' => 'boxRainGrow',
                'label' => 'Box Rain Grow',
            ),
            array(
                'value' => 'boxRainGrowReverse',
                'label' => 'Box Rain Grow Reverse',
            )
        );
    }
}