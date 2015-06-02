<?php
/**
 * Class Aptoplex_EasyUploader_Block_Help
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Block_Help extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = <<<HTML
<div>
    <p>{$this->__('Help and Support for any of our products can be obtained via our ')}<a href="http://support.aptoplex.com" title="Support Centre" alt="Support Centre" target="_blank" rel="norefferer">Support Centre.</a></p>
    <p>{$this->__('Alternatively, you can ')} <a href="https://www.aptoplex.com/store/contacts" title="Contact us" alt="Contact us" target="_blank" rel="noreferrer">{$this->__('Contact Us')}</a> {$this->__('or e-mail us at')} <a href="mailto:support@aptoplex.com" rel="noreferrer">support@aptoplex.com</a></p>
    <br/>
    <h4>{$this->__('Keep Informed')}</h4>
    <a href="https://www.aptoplex.com" title="Aptoplex official website" alt="Aptoplex official website" target="_blank" rel="noreferrer">Aptoplex.com</a><br/>
    <a href="http://www.magentocommerce.com/magento-connect/developer/aptoplex" title="Aptoplex developer profile at Magento Connect" alt="Aptoplex at Magento Connect" target="_blank" rel="noreferrer">Magento Connect</a><br/>
    <a href="https://twitter.com/Aptoplex" title="Aptoplex on Twitter" alt="Aptoplex on Twitter" target="_blank" rel="noreferrer">Twitter</a><br/>
    <a href="https://www.google.com/+Aptoplex" title="Aptoplex on Google+" alt="Aptoplex on Google+" target="_blank" rel="noreferrer">Google+</a><br/>
    <a href="https://www.youtube.com/user/aptoplex" title="Aptoplex on YouTube" alt="Aptoplex on YouTube" target="_blank" rel="noreferrer">YouTube</a><br/>
</div>
HTML;
        return $html;
    }
}