<?php

class AsiaConnect_Gallery_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_model;
    protected $_scheduleResize = false;
    protected $_scheduleWatermark = false;
    protected $_scheduleRotate = false;
    protected $_angle;
    protected $_watermark;
    protected $_watermarkPosition;
    protected $_watermarkSize;
    protected $_imageFile;
    protected $_placeholder;
	protected $set_background_color;
	protected $_view_mode;
    /**
     * Reset all previos data
     */
    protected function _reset()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleWatermark = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_imageFile = null;
        return $this;
    }

    public function init($imageFile, $mode="detail")
    {
    	$this->_reset();
    	$this->_view_mode = $mode;
        $this->_setModel(Mage::getModel('gallery/image'));
        //$this->_getModel()->setDestinationSubdir($attributeName);
        $this->keepTransparency(true);
        $this->_getModel()->setBackgroundColor($this->getStoreBackgroundColor());
		$this->setImageFile($imageFile);
        return $this;
    }
    public function resize($width, $height = null, $background = true)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        $this->set_background_color = $background;
        return $this;
    }
    public function keepAspectRatio($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }
    public function keepTransparency($flag, $alphaOpacity = null)
    {
        $this->_getModel()->setKeepTransparency($flag);
        return $this;
    }

    public function constrainOnly($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->_getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    public function watermark($fileName, $position, $size=null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size);
        $this->_scheduleWatermark = true;
        return $this;
    }

    public function placeholder($fileName)
    {
        $this->_placeholder = $fileName;
    }

    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'images/catalog/product/placeholder/'.$attr.'.jpg';
        }
        return $this->_placeholder;
    }

    public function __toString()
    {
    	try {
            if( $this->getImageFile() ) {				
                $this->_getModel()->setBaseFile( $this->getImageFile() );
            }
            
            if(!$this->set_background_color || $this->_view_mode=='detail'?!strlen(Mage::getStoreConfig('gallery/info/photo_background_color')):!strlen(Mage::getStoreConfig('gallery/info/simple_photo_background_color'))){
				$info = getimagesize($this->_getModel()->getBaseFile());
				
				$oldWidth 	= $info[0];
				$oldHeight 	= $info[1];
				$newWidth = $this->_getModel()->getWidth();
				$newHeight = $this->_getModel()->getHeight();
	
				if($oldWidth*1.0/$newWidth < $oldHeight*1.0/$newHeight) $newWidth = 1.0*$oldWidth * $newHeight/$oldHeight;
				else $newHeight = 1.0*$oldHeight * $newWidth/$oldWidth;
				
				$this->_getModel()->setWidth($newWidth);
				$this->_getModel()->setHeight($newHeight);
            }
            
            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl();
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if( $this->_scheduleWatermark ) {
                    $this->_getModel()
                        ->setWatermarkPosition( $this->getWatermarkPosition() )
                        ->setWatermarkSize($this->parseSize($this->getWatermarkSize()))
                        ->setWatermark($this->getWatermark(), $this->getWatermarkPosition());
                } else {
                    if( $watermark = Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image") ) {
                        $this->_getModel()
                            ->setWatermarkPosition( $this->getWatermarkPosition() )
                            ->setWatermarkSize($this->parseSize($this->getWatermarkSize()))
                            ->setWatermark($watermark, $this->getWatermarkPosition());
                    }
                }
                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
			//Mage::log($e);
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }
    protected function _getModel()
    {
        return $this->_model;
    }

    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    protected function getAngle()
    {
        return $this->_angle;
    }

    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        return $this;
    }

    protected function getWatermark()
    {
        return $this->_watermark;
    }

    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        return $this;
    }

    protected function getWatermarkPosition()
    {
        if( $this->_watermarkPosition ) {
            return $this->_watermarkPosition;
        } else {
            return Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position");
        }
    }

    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        return $this;
    }

    protected function getWatermarkSize()
    {
        if( $this->_watermarkSize ) {
            return $this->_watermarkSize;
        } else {
            return Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size");
        }
    }

    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if( sizeof($size) == 2 ) {
            return array(
                'width' => ($size[0] > 0) ? $size[0] : null,
                'heigth' => ($size[1] > 0) ? $size[1] : null,
            );
        }
        return false;
    }
    public function getStoreBackgroundColor()
    {
    	$color = $this->_view_mode=="detail"?explode(',',Mage::getStoreConfig('gallery/info/photo_background_color')):explode(',',Mage::getStoreConfig('gallery/info/simple_photo_background_color'));
    	if(sizeof($color)==3)
    	{
    		foreach($color as $item){
    			if(!is_numeric($item) || $item >255) return array(192, 192, 192);
    		}
    		return array((int)$color[0], (int)$color[1], (int)$color[2]);
    	}
    	return array(192, 192, 192);
    }
}