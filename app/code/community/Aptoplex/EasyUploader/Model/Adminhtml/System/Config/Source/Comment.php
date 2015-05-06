<?php
/**
 * Class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Comment
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Comment extends Mage_Core_Model_Abstract {

    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue) {
        $html = '';
        switch ($element->getName()) {
            case 'file_chunk_size':
                $html .= <<<HTML
<strong>Default: </strong>16384 (16 Mb)<br/>Size of each file chunk in <strong>kilobytes</strong> for any files that need to be split into smaller pieces before uploading. This is useful if you need to get around any restrictions your web host may have in place regarding PHP's <strong>post_max_size</strong> and <strong>upload_max_filesize</strong> settings.<br/>
<strong style="color:red;">CAUTION:</strong> Setting this to too low a value can have an adverse effect on server performance. We recommend no lower than <strong>256</strong> (0.25 Mb) as a safe minimum. Please consult the documentation for further details.
HTML;
                // Only expose these php settings if we're not in demo mode.
                if (!Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
                    $html .=    '<br/>';
                    $html .=    '<strong style="color:red;">Current PHP settings being returned by the server:</strong><br/>';
                    $html .=    '<strong>post_max_size: </strong>' . ini_get('post_max_size') . '<br/>';
                    $html .=    '<strong>upload_max_filesize: </strong>' . ini_get('upload_max_filesize');
                }
                break;
            default:
                break;
        }

        return $html;
    }
}