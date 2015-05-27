
<?php   
// Now login on MAGENTO
/* filname: ajax_uploadartwork.php
   all functions written  is accessible...
   
*/
include('../app/Mage.php');
Mage::app();

$stores = Mage::app()->getStores();

Mage::app()->setCurrentStore(20);

$_store_id = Mage::app()->getStore()->getId();
//echo $_store_id;
//$_store = Mage::app()->setCurrentStore($store_id);


  $crAction = isset($_REQUEST['crAction'])? $_REQUEST['crAction']:'';
 
  $skinUrl = 'http://outdoorbannershop.com.au/skin/frontend/default/outdoorbannershop/';
  $jsUrl = 'http://outdoorbannershop.com.au/';
  
  if($crAction =='uploadArtWork'){
	  
	  $loadedVars = array('categoryId'=>$_REQUEST['category_id'],
						  'productId'=>$_REQUEST['product_id'],
					);			  
	  
	  }
  
    $_category = Mage::getModel('catalog/category')->load($loadedVars['categoryId']);
	$_model = Mage::getModel('catalog/product');
	$_product = $_model->load($loadedVars['productId']);	
	$_category_id = $loadedVars['category_id'];
	$_product_id = $loadedVars['product_id'];
	$finishOptionFlag =0;
	
		
	
	?>


<script language="javascript" type="text/javascript">
<!--
		/*writing scriptiong for the produts behaviours*/
		
		jQuery(document).ready(function(e) {
               
		  var finishOptions_flag = document.getElementById('finishOptionsFlag').value;	
		  var price = parseFloat(document.getElementById('price_pr').value);
		      price = price.toFixed(2);
		  var saved = parseFloat((price * 28)/100);
		      saved = saved.toFixed(2);
		  
		  document.getElementById('total_display_block_pr').innerHTML = '$'+price;
		  document.getElementById('total_saved_block_pr').innerHTML = '$'+saved;
		  document.getElementById('grand_total_block_pr').innerHTML = '$'+price;
		  document.getElementById('grand_total_pr').value = price;
		  
		  if(finishOptions_flag !=0){
			  jQuery('#finishSelectBoxPr').show();
			  //jQuery('#finishSubBoxPr').show();
		 }
		
          });
		
		/*update the finsih options */
		
		function finishOptionsChange(val){
			/*finish options*/
			
			
			if(val=='HE'){
				jQuery('#finishSubBoxPr').hide();
				jQuery('#topSelectBoxPr').hide();
				jQuery('#bottomSelectBoxPr').hide();
				jQuery('#leftSelectBoxPr').hide();
				jQuery('#rightSelectBoxPr').hide();
				jQuery('#fittingSelectBoxPr').show();
				jQuery('#spacingSelectBoxPr').show();
				}
				
			if(val=='1PT'){
				jQuery('#finishSubBoxPr').show();
				jQuery('#topSelectBoxPr').show();
				jQuery('#bottomSelectBoxPr').hide();
				jQuery('#leftSelectBoxPr').hide();
				jQuery('#rightSelectBoxPr').hide();
				jQuery('#fittingSelectBoxPr').hide();
				jQuery('#spacingSelectBoxPr').hide();
				}
			if(val=='2PLR'){
				jQuery('#finishSubBoxPr').show();
				jQuery('#topSelectBoxPr').hide();
				jQuery('#bottomSelectBoxPr').hide();
				jQuery('#leftSelectBoxPr').show();
				jQuery('#rightSelectBoxPr').show();
				jQuery('#fittingSelectBoxPr').show();
				jQuery('#spacingSelectBoxPr').hide();
				}
			if(val=='2PTB'){
				jQuery('#finishSubBoxPr').show();
				jQuery('#topSelectBoxPr').show();
				jQuery('#bottomSelectBoxPr').show();
				jQuery('#leftSelectBoxPr').hide();
				jQuery('#rightSelectBoxPr').hide();
				jQuery('#fittingSelectBoxPr').hide();
				jQuery('#spacingSelectBoxPr').hide();
				}
				
				getPricePr();			
			
			}

	/*function getArea*/
	
	function getArea(width,height,pUnit){
		
		var area = parseFloat(width) * parseFloat(height) ;
				
		return area;		
		}
	
	/*function getDiscounts*/
	function getDiscounts(){
		
		
		}
	/*function getShipping*/	
	function getShipping(){
		
		
		}
		
	/*function increaseCounterPr*/	
	function increaseCounterPr(obj,val){
		
		if(parseInt(val) > 0){
			var nVal = parseInt(val)+1;
			document.getElementById(obj).value = nVal; 
			}
	}
	/*function decreaseCounterPr*/	
	function decreaseCounterPr(obj,val){
		if(parseInt(val) > 1){
			var nVal = parseInt(val)-1;
			document.getElementById(obj).value = nVal; 
			}
	}
	
	/*function getPrice*/
	function getPricePr(){
		
		/*collecting products attributes and setting price*/
		
		var width=document.getElementById('w_size_w').value;
		var height=document.getElementById('w_size_h').value;
		var quantity = document.getElementById('quantity_pr').value;
		var price = document.getElementById('price_pr').value;
		var pUnit = document.getElementById('sizeUnit').value;
		var area = getArea(width,height,pUnit);
		var total_area = area*quantity;
		var total_area = total_area.toFixed(2);
		var total_price = total_area * parseFloat(price); 
		var total_price = total_price.toFixed(2);
		
		document.getElementById('total_display_block_pr').innerHTML='$'+total_price;
		var saved = parseFloat((total_price * 28)/100);
		    saved = saved.toFixed(2);
		  
		  document.getElementById('total_saved_block_pr').innerHTML = '$'+saved;
		  document.getElementById('grand_total_block_pr').innerHTML = '$'+total_price;
		  document.getElementById('grand_total_pr').value = total_price;
		
		}
		
  /*function limitWords(val)*/
  
  
	  
	  jQuery(document).ready(function(e) {
		  
		  jQuery('#description_pr').bind("change keyup input",function() {
			  
			  var cWords = document.getElementById('counter_pr').value;
			  var val = document.getElementById('description_pr').value;
	  
	  var wordsLeft = parseInt(cWords)-parseInt(val.length);

        var limitNum=300;
	   if(wordsLeft > 0){
		   
		  
	  	document.getElementById('tWords').innerHTML = wordsLeft;
	   }

        if (jQuery(this).val().length > limitNum) {
            jQuery(this).val(jQuery(this).val().substring(0, limitNum));
        }

    });
          
     });
	  
	 
	
		
-->
</script>
<style>
<!--
 #close_button{
	 background-color: #FF0000;
    border-radius: 6px;
    box-shadow: 2px 2px #666666;
    color: #FFFFFF;
    cursor: pointer;
    float: right;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    text-align: center;
    width: 30px;
	 }
 .product-image{border: 2px solid #DDDDDD;
    border-radius: 10px;
    box-shadow: 4px 5px 2px #F0EEEE;
    overflow: hidden;}
 .product-image img{border:0px;}
 .products{background-color:#f4f4f4; width:400px !important; border:none !important; }
 
-->
</style>

<link rel="stylesheet" href="<?php echo $skinUrl;?>css/style.css" media="all"/>
<link rel="stylesheet"  href="<?php echo $skinUrl;?>css/styles.css" media="all"/>
<link rel="stylesheet"  href="<?php echo $skinUrl;?>css/nav.css" media="all"/>
<link rel="stylesheet"  href="<?php echo $skinUrl;?>css/print.css" media="print"/>
<div class="clearfix"></div>
<div id="innerLeft-tabing" class="float-L clearfix">
  <div class="innerLeft-tabing" id="uploadYourArtworkData">
  <div class="title">Submit Your Artwork Details       </div>
   <form name="printreadyForm" method="post" enctype="multipart/form-data" action="#" >
    
 <div class="detailtab" id="printreadytab1" style="height: 606px;  overflow: auto;    width: 625px;">
        <dl class="clearfix">
          <dt>Project Name</dt>
          <dd>
            <input type="text"  name="projectname" id="projectname" class="textfield" maxlength="70" value="">
          </dd>
        </dl>
        <dl class="clearfix">
          <dt>Category</dt>
          <dd>
          <input type="text" readonly="readonly" name="categoryName" id="categoryName" class="textfield products" maxlength="70" value="<?php echo $_category->getName();?>">
        </dd>
        </dl>
        <dl class="clearfix">
          <dt>Product Name</dt>
          <dd>
            <input type="text" readonly="readonly"   name="productName" id="productName" class="textfield products" maxlength="70" value="<?php echo $_product->getName();?>">
          </dd>
        </dl>
        
        
        <dl class="clearfix">
          <dt>Size</dt>
          <dd class="w263">
            
            <input type="hidden" name="quantity_discount_pr" id="quantity_discount_pr" class="textfield" value="0"  readonly="readonly" style="width:60px;" />
            
            <input type="checkbox" id="bothsides" name="bothsides" value=""   onclick="javascript:javascript:getPricePr()" style="display:none;"/>
            
            
            <div class="sizebox1" style="width:515px;">
              <div class="sizes-block">
                <input name="w_size_w" id="w_size_w" type="text" class="textfield" value="1" onchange="javascript:getPricePr()">
                <div class="float-L"> <a href="javascript:void(0);" class="arrowTop" onclick="javascript:increaseCounterPr('w_size_w', document.getElementById('w_size_w').value),getPricePr()"></a> 
                <a href="javascript:void(0);" class="arrowbtm" onclick="javascript:decreaseCounterPr('w_size_w', document.getElementById('w_size_w').value),getPricePr()"></a> </div>
              </div>
              <span class="dimensions">width</span>
              <div class="sizes-block">
                <input name="w_size_h" id="w_size_h" type="text" class="textfield" value="1" onchange="javascript:getPricePr()">
                <div class="float-L"> <a href="javascript:void(0);" class="arrowTop" onclick="javascript:increaseCounterPr('w_size_h', document.getElementById('w_size_h').value),getPricePr()"></a>
                 <a href="javascript:void(0);" class="arrowbtm" onclick="javascript:decreaseCounterPr('w_size_h', document.getElementById('w_size_h').value),getPricePr()"></a> </div>
              </div>
              <span class="dimensions"> height </span>
              <input name="w_size" id="w_size" type="hidden" value="m">
              <select name="sizeUnit" id="sizeUnit" onchange="javascript:getPricePr(),ftTOin(this.value);" class="textfield dropdown-s">
                <option value="m" >m</option>
                <option value="cm" >cm</option>
                <option value="mm" >mm</option>
                <!--<option value="ft" >Ft</option>
                <option value="inch" >in</option>-->
              </select>
              <input type="hidden" name="c_hecksizew" id="c_hecksizew" style="width:30px;" value=""/>
              <input type="hidden" name="c_hecksizeh" id="c_hecksizeh" style="width:30px;" value=""/>
              <input type="hidden" name="size_major" id="size_major" style="width:50px;" value=""/>
              
             <!-- <a class="blue-clr underline mrg-L10" href="javascript:NewWindow('http://www.outdoorbannershop.com.au/ajax/ajax_price_view.php?id=1','PriceList','825','580','0','yes');" title="See Price">SEE PRICE</a> --></div>
          </dd>
        </dl>
        
        <!-- Ground mount  --> 
        <!-- Ground mount  -->
        <dl class="clearfix">
          <dt>Quantity</dt>
          <dd>
         <select class="textfield dropdown-s" id="quantity_pr" name="quantity_pr" style="width:80px;" onchange="javascript:getPricePr()" >
                    
                     
                     <?php 
				 	for($i=1; $i<201; $i++){
				 ?>
                      <option value="<?php echo $i;?>"><?php echo $i;?></option>
                    <?php 
						}
				?>
                    
                    
               </select>
         
        </dd>
        </dl>
        
         <input type="hidden" name="realPrice_pr" id="realPrice_pr" value="<?php echo $_product->getPrice();?>"  readonly="readonly" style="width:60px;" class="textfield"/>
         <input type="hidden" name="prsq_price_pr" id="prsq_price_pr" value="<?php echo $_product->getPrice_sq_feet();?>"  readonly="readonly" style="width:60px;" class="textfield"/>
       
        <input type="hidden" name="price_show_pr" id="price_show_pr" value="0"  readonly="readonly" style="width:60px;" class="textfield"/>
        <input type="hidden" id="base_total_pr" name="base_total_pr" value="0"  readonly="readonly" style="width:60px;" class="textfield"/>
        <input type="hidden" id="total_price_pr" name="total_price_pr" value="0"  readonly="readonly" style="width:60px;" class="textfield"/>
        <input type="hidden" name="total_area_pr" id="total_area_pr" value="0"  readonly="readonly" style="width:60px;" class="textfield"/>
      
       <dl class="clearfix" id="finishSelectBoxPr" style="display:none;">
          <dt>Finishing</dt>
          <dd>
         <div >
           <select class="textfield" id="finish_sel_pr" onchange="finishOptionsChange(this.value);">
             <option value="0">--Please select--</option>                  
    <?php 	
	 
	 //echo $_product->getId();
	 $_options = $_product->getOptions();
	
	//if product have options then it will show either it will return 0	
	$output = '';
	if(count($_options) > 0){
	 					
	foreach($_options as $_option){
					if($_option->getTitle() =='FinishOptions'){
						$finishOptionFlag =1;						
						//var_dump($_option->getValues());
						foreach($_option->getValues() as $_optVals){
						$output.='<option value="'.$_optVals->getSku().'">'.$_optVals->getTitle().'</option>';	
						}
					}
	             }
	}
	/*display output*/
	 echo $output;
  ?>
     </select>
     
     <input type="hidden" id="finishOptionsFlag" name="finishOptionsFlag" value="<?php echo $finishOptionFlag;?>" />
                               
            </div>            
            <div id="finishSubBoxPr" class="sub_attributes_box" style="display:none;">
              <div id="topSelectBoxPr" style="display:none;" >
                  <div   class="sub_attrib sub_attrib_lable">Top:</div>
                    <select class="textfield sub_attrib" id="size_top_sel_pr" style="width:59px;">
                     <option value="0">--Please Select--</option>
                     <option value="12mm">12mm &Oslash;</option>
                     <option value="15mm">25mm &Oslash;</option>
                     <option value="50mm">50mm &Oslash;</option>
                     <option value="75mm">75mm &Oslash;</option>
                     <option value="100mm">100mm &Oslash;</option>
                    </select>
                    </div>
                    
                    <div id="leftSelectBoxPr" style="display:none;">
                    <div class="sub_attrib sub_attrib_lable">Left:</div>
                    <select class="textfield sub_attrib" id="size_left_sel_pr" style="width:59px;">
                     <option value="0">--Please Select--</option>
                    <option value="12mm">12mm &Oslash;</option>
                     <option value="15mm">25mm &Oslash;</option>
                     <option value="50mm">50mm &Oslash;</option>
                     <option value="75mm">75mm &Oslash;</option>
                     <option value="100mm">100mm &Oslash;</option>
                    </select>
                    </div>
                    <div id="rightSelectBoxPr" style="display:none;">
                    <div   class="sub_attrib sub_attrib_lable">Right:</div>
                    <select class="textfield sub_attrib" id="size_right_sel_pr" style="width:59px;">
                      <option value="0">--Please Select--</option>
                     <option value="12mm">12mm &Oslash;</option>
                     <option value="15mm">25mm &Oslash;</option>
                     <option value="50mm">50mm &Oslash;</option>
                     <option value="75mm">75mm &Oslash;</option>
                     <option value="100mm">100mm &Oslash;</option>
                    </select>
                    </div>
                    
                    <div id="bottomSelectBoxPr"  style="display:none;">
                   <div  class="sub_attrib sub_attrib_lable">Bottom:</div>
                    <select class="textfield sub_attrib" id="size_bottom_sel_pr" style="width:59px;">
                     <option value="0">--Please Select--</option>
                     <option value="12mm">12mm &Oslash;</option>
                     <option value="15mm">25mm &Oslash;</option>
                     <option value="50mm">50mm &Oslash;</option>
                     <option value="75mm">75mm &Oslash;</option>
                     <option value="100mm">100mm &Oslash;</option>
                    </select> 
                    </div>
                  
                  </div>           
        </dd>
        </dl>
        <dl class="clearfix" id="fittingSelectBoxPr" style="display:none;">
          <dt>Fittings</dt>
          <dd>
         <div id="fittingSelectBox_pr">
                 <select class="textfield" id="fittings_sel_pr" >
                   <?php 
			   		/*options of values of finish select box*/
				$_fitting_vals=array(
							'0'=>'None',
							'top_right_bottom_left'=>'Top, Right, Bottom, Left',
							'top_only'=>'Top Only',
							'top_bottom'=>'Top & Bottom',
							'left_right'=>'Left & Right',
							'left_right_bottom'=>'Left, Right, Bottom',
							'corners_only'=>'Corners Only'
							
							);
				foreach($_fitting_vals as $key=>$_fitting){						
			   ?>
                  <option value="<?php echo $key?>"><?php echo $_fitting?></option>
                  <?php
				}
                  ?>
                  </select>                   
        </dd>
        </dl>
        <dl class="clearfix"  id="spacingSelectBoxPr" style="display:none;">
                <dt>Spacing</dt>
                <dd><div >
                  <select class="textfield" id="spacing_sel_pr">
                   <?php 
			   		/*options of values of finish select box*/
				$_spacing_vals=array(
							'0'=>'--Please select--',
							'1000'=>'1000 mm',
							'500'=>'500 mm',
							'300'=>'300 mm'
							);
				foreach($_spacing_vals as $key=>$_spacing){						
			   ?>
                  <option value="<?php echo $key;?>"><?php echo $_spacing;?></option>
                  <?php
				}
                  ?>
                  </select>
                  </div>
                            
                </dd>
              </dl>
              
               <dl class="clearfix" id="baseSelectBoxPr"  style="display:none;">
                <dt>Hardware</dt>
                <dd><div >
                  <select class="textfield" id="base_sel_pr">
                   <option value="0">--Please Select--</option>
                   <?php 	
	 
	 //echo $_product->getId();
	 $_options = $_product->getOptions();
	
	//if product have options then it will show either it will return 0	
	$output = '';
	if(count($_options) > 0){
	 					
	foreach($_options as $_option){
					if($_option->getTitle() =='BaseOptions'){
						
						//var_dump($_option->getValues());
						foreach($_option->getValues() as $_optVals){
						$output.='<option value="'.$_optVals->getSku().'">'.$_optVals->getTitle().'</option>';	
						}
					}
	             }
	}
	/*display output*/
	 echo $output;
  ?>
                  </select>
                  </div>
                                     
                </dd>
              </dl>
        <dl class="clearfix">
                <dt>Shipping</dt>
                <dd>
                  <div id="shippingmethodsPr">
                    <select id="shipping_method_pr" name="shipping_method_pr" class="textfield" onchange="javascript:quantity()">
                      <option value="0">Select Shipping</option>
                      <option value="priority overnight">Priority Overnight (Delivery by 10-16-2013 for Print Ready File) </option>
                      <option value="priority">Priority (Delivery by 10-18-2013 from proof)</option>
                      <option value="expedited">Express (Delivery by 10-23-2013 from proof)</option>
                      <option value="standard" >Standard (Delivery by 10-29-2013 from proof)</option>
                    </select>
                  </div>
                </dd>
              </dl>
        <dl class="clearfix">
          <dt>Price</dt>
          <dd id="total_display_pr"> 
          <div id="total_display_block_pr" class="float-L" style="font-size:16px">$<?php echo $_product->getPrice();?></div>
          <input type="hidden" id="price_pr" name="price_pr" value="<?php echo $_product->getPrice();?>" /> </dd>
        </dl>
        <dl class="clearfix">
          <dt>You Saved</dt>
          <dd id="total_saved_pr"> 
          <div id="total_saved_block_pr" class="float-L" style="font-size:18px">$0.00</div>
          <input type="hidden" id="saved_pr" name="saved_pr" value="" /> 
          <input type="hidden" id="discount_pr" name="discount_pr" value="" />
          </dd>
        </dl>
        <dl class="clearfix">
          <dt>Grand Total</dt>
          <dd id="total_saved_pr"> 
          <div id="grand_total_block_pr" class="float-L" style="font-size:24px">$0.00</div>
          <input type="hidden" id="grand_total_pr" name="grand_total_pr" value="" /> 
          </dd>
        </dl>
        <dl style="height:11px;">
        </dl>
        <!-- Backdrop side --> 
        <!-- End Backdrop side --> 
        <!-- Canopies --> 
        <!-- Canopies -->
        <dl class="float-L">
          <dd class="text float-L">Include any notes or requests to our production team before they process your file...Type here...</dd>
          <dd class="float-L textarea">
            <textarea name="description_pr" id="description_pr" class="textfield w587" onkeyup="javascript:limitWords(document.getElementById('description_pr').value)" onkeydown="javascript:limitWords(document.getElementById('description_pr').value)"></textarea>
            <div class="allowedWords">Allowed Words (<span id="tWords">255</span>)</div>
            <input type="hidden" value="255" id="counter_pr" name="counter_pr" />
          </dd>
          <div class="shadowSprite shadow590"></div>
        </dl>
        <dl style="height:15px;">
        </dl>
        <dl class="float-L">
          <dd class="text float-L">Select Upload Options</dd>
          <dd class="text">
            <div class="upload-module clear">
                <div class="Options1">
                                <input name="uploadimage" type="radio" id="yousendit" value="1" onclick="checkuploadvalue(this.value);return yousend_it('http://www.bannerbuzz.com/sitedrop_yousendit.php?p_id=1381807105','1381807105');" />
                                <a href="javascript:void(0);" onclick="checkuploadvalue(1);return yousend_it('http://www.bannerbuzz.com/sitedrop_yousendit.php?p_id=1381807105','1381807105');" style="color:#000000;">Upload file on FTP server  (up to 2GB)</a>
                                
                                <span>
                <input type="button" value="" name="Upload Files" class="upload-file"  id="uploadusendit" onclick="checkuploadvalue('1');return yousend_it('http://www.bannerbuzz.com/sitedrop_yousendit.php?p_id=1381807105','1381807105');" style="opacity:0.9;"  disabled="disabled" />
                <input type="hidden" name="ysi_id" id="ysi_id" />
                </span> </div>
              <div class="Options2">
                <input name="uploadimage" type="radio" id="artworklater" value="2" onclick="checkuploadvalue(this.value);" />
                <input type="hidden" name="sendlaterartwork" id="sendlater_artwork" />
                                <a href="javascript:void(0);" id="selectopt3" onclick="checkuploadvalue(2);">I’ll send my artwork file later after login.</a>
                              </div>
                          </div>
          </dd>
          <div class="shadowSprite shadow590"></div>
        </dl>
        <dl class="clearfix float-R">
          <input type="hidden" id="accessories_pr" name="accessories_pr" class="textfield" value="0" style="width:60px;" />
          <input type="hidden" name="category_id_pr" id="category_id_pr" value="<?php echo $_category_id; ?>"  />
          <input type="hidden" name="product_id_pr" id="product_id_pr" value="<?php echo $_product->getId();?>"  />
          
          
          <input type="button" class="cart-button add-to-cart" value="" id="addCartBtn" name="addCartBtn" onclick="javascript: return addToCartPr(<?php echo $_product->getPrice();?>, <?php echo $_product->getId();?>,quantity_pr.value);">
        </dl>
        <dl style="height:20px;">
        </dl>
      </div>
    </form>
  </div>
  
  <div class="shadowSprite shadow643">
   
  </div>
</div>

<!--rightbar end-->
<div id="innerRight-specifications" class="float-R clearfix">
  <div style="width:270px; height:220px; border:0px solid #ccc;margin-top:15px;">
  <p class="product-image">
        <?php
		$_previewWidth = 270;
		$_previewHeight = 220;
		$_preloadText ='loading...';
		?>
		
            <img id="mainImage" src="<?php echo Mage::helper('catalog/image')->init($_product, 'image')->resize($_previewWidth, $_previewHeight);?>" alt="<?php echo $_product->getName();?>" title="<?php echo $_product->getName();?>" />
                         
        
    </p>
  </div>
  <div style="margin-top:30px;">
  <!--<div class="mrg-B10">
  <a href="javascript:NewWindow('http://www.outdoorbannershop.com.au/artworkspecs.php','Artwork','750','600','0','yes');" title="Recommended Artwork Specifications" class="underline blue-clr b">Recommended Artwork Specifications</a></div>-->
  <div class="mrg-B10">
    <dl class="clearfix">
      <dt>File Type</dt>
      <dd> JPEG, PDF, DOC, JPG, DOCX,
        EPS, CDR, AI, GIF, PSD, TIF,
        TIFF, PPT, PNG, BMP </dd>
    </dl>
    <dl class="clearfix">
      <dt>Resolution</dt>
      <dd>150 DPI to 600 DPI</dd>
    </dl>
    <dl class="clearfix">
      <dt>Color Mode</dt>
      <dd>CMYK</dd>
    </dl>
    <dl class="clearfix">
      <dt>Text</dt>
      <dd>Convert to Outlines</dd>
    </dl>
    <dl class="clearfix">
      <dt>Layers</dt>
      <dd>Flatten to a Background Layer</dd>
    </dl>
    <dl class="clearfix">
      <dt>Compression</dt>
      <dd>Zip files if necessary</dd>
    </dl>
  </div>
  <!--<div class="instructions">
    <div class="questionsIcon"></div>
    <ul>
      <li><a href="#" class="blue-clr">File Setup Instructions?</a></li>
      <li><a href="#" class="blue-clr">Upload Problems?</a></li>
    </ul>
  </div>-->
  <div class="clear">
    <p>&nbsp;</p>
  </div>
  <div class="supportModule float-R">
    <div class="supportIcon"></div>
    <div class="smalltext">Need Help or questions?</div>
    <div class="box grey-gredient-bg">
      <div class="float-L">
        <div class="calltext"><?php echo Mage::getStoreConfig('general/store_information/phone');?></div>
        <a rel="nofollow" title="send us email" class="emailink" href="mailto:<?php echo Mage::getStoreConfig('trans_email/ident_general');?>">send us email</a>&nbsp; &raquo; </div>
      <div class="float-L livesupport"> 
      <a onclick="window.open('https://messenger.providesupport.com/messenger/bannerbuzz.html', '_blank','menubar=0,location=0,scrollbars=auto,resizable=1,status=0,width=600,height=550');return false;" href="#" rel="nofollow">
      <img src="https://image.providesupport.com/image/bannerbuzz/current" align="bottom" title="Live Support" alt="Live Support"></a> </div>
    </div>
  </div>
</div>
<div class="clear" style="height:40px;"></div>
</div>

<script type="text/javascript">
    var optionsPrice = new Product.OptionsPrice(<?php echo $this->getJsonConfig() ?>);
</script>

    
    <script type="text/javascript">
    //<![CDATA[
        var productAddToCartForm = new VarienForm('product_addtocart_form');
        productAddToCartForm.submit = function(button, url) {
            if (this.validator.validate()) {
                var form = this.form;
                var oldUrl = form.action;

                if (url) {
                   form.action = url;
                }
                var e = null;
                try {
                    this.form.submit();
                } catch (e) {
                }
                this.form.action = oldUrl;
                if (e) {
                    throw e;
                }

                if (button && button != 'undefined') {
                    button.disabled = true;
                }
            }
        }.bind(productAddToCartForm);

        productAddToCartForm.submitLight = function(button, url){
            if(this.validator) {
                var nv = Validation.methods;
                delete Validation.methods['required-entry'];
                delete Validation.methods['validate-one-required'];
                delete Validation.methods['validate-one-required-by-name'];
                // Remove custom datetime validators
                for (var methodName in Validation.methods) {
                    if (methodName.match(/^validate-datetime-.*/i)) {
                        delete Validation.methods[methodName];
                    }
                }

                if (this.validator.validate()) {
                    if (url) {
                        this.form.action = url;
                    }
                    this.form.submit();
                }
                Object.extend(Validation.methods, nv);
            }
        }.bind(productAddToCartForm);
    //]]>
    </script> 
 

