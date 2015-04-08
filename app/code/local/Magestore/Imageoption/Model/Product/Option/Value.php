<?php
class Magestore_Imageoption_Model_Product_Option_Value extends Mage_Catalog_Model_Product_Option_Value 
{
    public function saveValues()
    {	
		foreach ($this->getValues() as $value) {
			$this->setData($value)
                ->setData('option_id', $this->getOption()->getId())
                ->setData('store_id', $this->getOption()->getStoreId());
			$isNew = false;
            if ($this->getData('option_type_id') == '-1') {//change to 0
				$isNew = true;
                $this->unsetData('option_type_id');
            } else {
                $this->setId($this->getData('option_type_id'));
            }

            if ($this->getData('is_delete') == '1') {
                if ($this->getId()) {
                    $this->deleteValues($this->getId());
                    $this->delete();
                }
            } else {
                $this->save();
				if($isNew) {
					// upload image
					$option_type_id = $this->getData('option_type_id');
					$image_id = $value['image_id'];
					$inputfile = 'productimage'. $image_id;
					$imageWidth = 60;
					if(isset($_POST['image_width_'.$image_id]) && ($_POST['image_width_'.$image_id]) != null)
							$imageWidth = $_POST['image_width_'.$image_id];
					$product = $this->getProduct();
					if($product->getId() == 0)
							$imageNames = Mage::helper('imageoption/template')->uploadImageFile($this->getOptionId(),$option_type_id,$inputfile,$imageWidth);
						else
							$imageNames = Mage::helper('imageoption')->uploadImageFile($product,$this->getOptionId(),$option_type_id,$inputfile,$imageWidth);
					// save imageoption
					$model_imageoption = Mage::getModel('imageoption/imageoption');
					
					$imageoption_data['product_id'] = $product->getId();
					$imageoption_data['option_id'] = $this->getOptionId();
					$imageoption_data['option_type_id'] = $option_type_id;
					$imageoption_data['is_template'] = 1;
					$imageoption_data['image'] = $imageNames;
					$imageoption_data['qty'] = $value['qty'];
					$imageoption_data['image_width'] = $imageWidth ;
					
					$model_imageoption->setData($imageoption_data);
					try{
						$model_imageoption->save();
						} catch(Exception $e) {
						  }
					
				} 
            }
        }//eof foreach()
        return $this;
    }
}