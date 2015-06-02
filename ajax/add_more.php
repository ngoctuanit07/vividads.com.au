<?php
    ob_start();
    include_once '../app/Mage.php';
    Mage::app();
    $storeId=$_POST["store_id"];
    $root_cat=$_POST["root_cat"];
    
    
    
    $_category = Mage::getModel('catalog/category')->load($root_cat);
    $_subcategories = $_category->getChildrenCategories();



?>        
                    <div class="one_pro">
                            <div class="con">
                              <span class="lab">Category:</span>
                              <span class="inp">
                                <select name="category[]" class="cat" onchange="getProduct(this.value,this);">
                                    <option value="">Select a category</option>
                                      <?php
                                      foreach($_subcategories as $_sub){
                                      
                                      $_sub=Mage::getModel('catalog/category')->setStoreId($storeId)->load($_sub->getId());
                                      
                                      ?>    
                                      
                                          <option value="<?php echo $_sub->getId(); ?>"><?php echo $_sub->getName(); ?></option>
                                          
                                      <?php
                                      
                                      }
                                      
                                      ?>
                                </select>
                             </span>
                                
                            </div>
                            <div class="con proddiv">
                                <span class="lab">
                                    Products:
                                </span>
                                <span class="inp">
                                    <select name="products[]" class="prod">
                                          <option value="">Select a product</option>
                                    </select>
                                </span>
                              
                            </div>
                                
                            <div class="im_con prodimgdiv">
                                <span class="im">Product Image:</span>
                                <div class="def_cl"></div>
                                
                            </div>
                            
                            <div class="pro_qty">
                                <span class="lab">
                                    Enter Quantity:
                                </span>
                                <input type="text" name="quantity[]" class="quantity" value="1" />
                            </div>
                            
                            <div class="del_button">
                                <a href="javascript:void(0);" class="del_it">Delete</a>
                            </div>
                    </div>