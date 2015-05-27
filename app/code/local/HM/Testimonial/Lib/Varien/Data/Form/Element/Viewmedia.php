<?php
class Hm_Testimonial_Lib_Varien_Data_Form_Element_Viewmedia extends Varien_Data_Form_Element_Abstract
{
	public function __construct($attributes=array())
	{
		parent::__construct($attributes);
		$this->setType('label');
	}

	public function getElementHtml()
	{
		$model= Mage::getModel('testimonial/testimonial')->load($this->getValue());
		$html.='';
		
		if ($model->getMedia()){
			$media= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$model->getMedia();
		}elseif ($model->getMediaUrl()){
			$media= $model->getMediaUrl();
		}
		
		$file_ext = array();
		$file_ext = explode('.',$media);
		$file_ext = $file_ext[sizeof($file_ext)-1];
		$file_ext = strtolower($file_ext);
		
		if (strpos($media, 'www.youtube.com') || $file_ext=='flv'|| $file_ext=='avi'|| $file_ext=='mp3' || $file_ext=='mp4' ){
			$html.= '<div id="mw_testimonial_view_media" style="width: 200px; height: 150px;">
					</div>
					<script type="text/javascript" charset="utf-8">
					jQuery(document).ready(function(){
					jwplayer("mw_testimonial_view_media").setup({
					"flashplayer": "'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/player.swf",
					"skin": "'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'testimonial/glow.zip",
					"file": "'.$media.'",
					"stretching":"fill",
					"controlbar.position":"over",
					"width":	"200",
					"height":	"150",
					});
					});
					</script>';
		}elseif ($file_ext=='swf'){
			$html.= '<object classid="clsid:your-class-id" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0">
					<param name=movie value="">
					<param name="autoplay" value="false" />
					<param name="controller" value="true" />
					<param name=quality value=high>
					<param name="wmode" value="transparent">
					<embed src="'.$media.'"
					quality="high"
					wmode="transparent"
					width="200"
					pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"
					type="application/x-shockwave-flash">
					</embed>
					</object>';
		}elseif ($file_ext=='jpg'||$file_ext=='png'||$file_ext=='gif'||$file_ext=='jpeg'){
			$html.= '<img src="'.$media.'" width="200px">';
		}
		
			$html.= $this->getAfterElementHtml();
		return $html;
	}

	public function getLabelHtml($idSuffix = ''){
		if (!is_null($this->getLabel())) {
			$html = '<label for="'.$this->getHtmlId() . $idSuffix . '" style="'.$this->getLabelStyle().'">'.$this->getLabel()
			. ( $this->getRequired() ? ' <span class="required">*</span>' : '' ).'</label>'."\n";
		}
		else {
			$html = '';
		}
		return $html;
	}
}