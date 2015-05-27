<?php   
// Now login on MAGENTO
include('../app/Mage.php');


extract($_REQUEST);
print_r($_REQUEST);

Mage::app($store);
echo 'asfsafsf';
session_start();
print_r($_REQUEST);
Mage::getSingleton("core/session", array("name"=>"frontend"));
    if($email && $pass)
    {
                $email=$email;
                $password=$pass;
            $session = Mage::getSingleton('customer/session', array("name"=>"frontend"));

            try {
                      $log = $session->login($email, $password);
                      $session->setCustomerAsLoggedIn($session->getCustomer());
                      $customer_id=$session->getCustomerId();

                      $send_data["success"]=true;
                      $send_data["message"]="Login Success";
                      $send_data["customer_id"]=$customer_id;
                      $customer = Mage::getSingleton('customer/session')->loginById($customer_id);
                } 

                catch (Exception $ex) {
                            $send_data["success"]=false;
                                $send_data["message"]=$ex->getMessage();
                            }
    }
    else
    {
            $send_data["success"]=false;
            $send_data["message"]="Enter both Email and Password";
    }
    
   // echo json_encode($send_data);

if($send_data["customer_id"] != '')
{
    
    $countryCode = Mage::getStoreConfig('general/country/default');
    //echo "code :".$countryCode;
    $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
    
    $countryCollection = Mage::getModel('directory/country_api')->items();
    
    $session = Mage::getSingleton('customer/session');
   $customerData = Mage::getModel('customer/customer')->load($send_data["customer_id"])->getData();
   
   
   $Fname = $customerData['firstname'];
    $Lname = $customerData['lastname'];
    $Email = $customerData['email'];
    
    $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
   if ($customerAddressId){
          $address = Mage::getModel('customer/address')->load($customerAddressId);
         $address_data = $address->getData();
         $street1 = $address_data['street'];
         $telephone = $address_data['telephone'];
         $country_id = $address_data['country_id'];
         $postcode = $address_data['postcode'];
         $region = $address_data['region'];
         $city = $address_data['city'];
   }
 ?>
  
   <h2>Get a Quote</h2>
                                
                                <?php if($session->isLoggedIn()) { } else { ?>
                                <!--          For login                     -->
                                <div class="login-form" id="customerquote-login">
                                <h3>Returning Customers</h3>
                                    <form id="customerquote-login-form" method="post" action="">
                                        
 
                                        <ul class="form-list">
                                        <li class="email">
                                            <label class="required" for="email"><em>*</em>Email Address</label>
                                            <div class="input-box">
                                            <input type="text" title="Email Address" class="input-text required-entry validate-email" id="email" value="" name="login[username]">
                                            </div>
                                        </li>
                                        <li class="password">
                                            <label class="required" for="pass"><em>*</em>Password</label>
                                            <div class="input-box">
                                            <input type="password" title="Password" id="pass" class="input-text required-entry validate-password" name="login[password]">
                                            </div>
                                        </li>
                                        </ul>
                                    <button id="customerquote-login" name="login" title="Login" class="button login" onclick="quote_login()" type="button"><span><span>Login</span></span></button>
                                    </form>                    
                                </div>
                                <!--           End Login                     -->
                                <?php } ?>
                                
                                <?php if($session->isLoggedIn()) { ?>
                                <div id="customerquote-welcome">
                                <?php echo $customerData['firstname'].' '.$customerData['lastname'];  ?>
                                </div>
                                <?php } ?>
                                <div id="customerquote-info-wrapper">
                                <form method="post" id="customerquote-customer-form" action="<?php echo Mage::getBaseUrl();?>Quotation/Quote/SendRequestFromCart/">
                                    <div style="display:none;">
                                    <input name="customer_id" id="customer_id" value="<?php echo $send_data["customer_id"]?>"/>
                                    <?php
                                        $checkoutSession = Mage::getSingleton('checkout/session');
                                        
                                        ?>
                                       <table cellspacing="0" class="data-table" id="quotation-request-products">
<thead>
        <tr>
            <th><?php echo 'Product'; ?></th>
            <th width="100"><?php echo 'Qty'; ?></th>
        </tr>
</thead>
<tbody>
	<?php foreach($checkoutSession->getQuote()->getAllItems() as $item): ?>
		<?php if (($item->getProduct()->gettype_id() == 'simple') || ($item->getProduct()->gettype_id() == 'virtual') || ($item->getProduct()->gettype_id() == 'downloadable')): ?>
            <tr>
                <td>
                	<a href="<?php echo $item->getProduct()->getProductUrl(); ?>"><?php echo $item->getName(); ?></a>
                	<?php //echo $this->getProductOptions($item); ?>
                </td>
                <td width="100"><input size="5" type="text" name="qty_<?php echo $item->getProduct()->getId(); ?>" id="qty_<?php echo $item->getProduct()->getId(); ?>" value="<?php echo $item->getQty(); ?>"></td>
            </tr>
        <?php endif; ?>
	<?php endforeach; ?>
</tbody>
</table>
                                        <textarea cols="110" rows="10" id="description" name="description">test</textarea>
                                    </div>
                                    <?php
                                    $CurrentUrl = Mage::helper('core/url')->getCurrentUrl();
                                   $url = end(explode(Mage::getBaseUrl(),$CurrentUrl));
                                   ?>
                                   <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $redirect_url?>">
                                <div class="new-customer">
                                </div>
                                
                                <div class="returning-customer">
                                <h3><?php echo $customerText; ?></h3>
                                <!--<form id="customerquote-return-customer-form">-->
                                <fieldset>
                                
                                
                                <div class="customer-infomation">
                                <ul class="form-list">
                                <li><label class="required" for="firstname"><em>*</em>First Name</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" class="input-text required-entry validate-length minimum-length-1 maximum-length-255 validation-passed" value="<?php echo $Fname; ?>" name="firstname" id="firstname">
                                </div>
                                </li>
                                <li><label class="required" for="lastname"><em>*</em>Last Name</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" class="input-text required-entry validate-length minimum-length-1 maximum-length-255 validation-passed" value="<?php echo $Lname; ?>" name="lastname" id="lastname">
                                </div>
                                </li>
                                <li><label class="required" for="email"><em>*</em>Email</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" class="input-text required-entry validate-email validation-passed" value="<?php echo $Email; ?>" name="billing_email" id="billing_email">
                                </div>
                                </li>
                                <li><label for="company_name">Company</label>
                                <div class="input-box" id="">
                                <input type="text" class="input-text validation-passed" value="" name="company_name" id="company_name">
                                </div>
                                </li>
                                <li><label for="personal_phone">Phone Number</label>
                                <div class="input-box" id="">
                                <input type="text" class="input-text validation-passed" value="" name="personal_phone" id="personal_phone">
                                </div>
                                </li>
                                <li><label class="required" for="acee_how_did_you_hear_about_us"><em>*</em>How did you hear about us?</label>
                                <div class="input-box validation-passed" id="">
                                <select class="required-entry validation-passed" name="acee_how_did_you_hear_about_us" id="acee_how_did_you_hear_about_us">
                                <option value=""></option>
                                <option selected="selected" value="Banner Advertisement">Banner Advertisement</option>
                                <option value="Business.com">Business.com</option>
                                <option value="eBay">eBay</option>
                                <option value="Email">Email</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Google">Google</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="MSN">MSN</option>
                                <option value="Other">Other</option>
                                <option value="Prior Customer">Prior Customer</option>
                                <option value="RedZee.com">RedZee.com</option>
                                <option value="Referral">Referral</option>
                                <option value="Review Sites / Online Directories">Review Sites / Online Directories</option>
                                <option value="Telemarketing">Telemarketing</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Yahoo!">Yahoo!</option>
                                </select>
                                </div>
                                </li>
                                <li><label for="salesrep">Service Rep ID</label><input type="text" id="salesrep" name="salesrep" value="" class="salesrep input-text validate-length validation-passed"></li>
                                
                                </ul>
                                </div>
                                
                                </fieldset>
                                
                                
                                <h3>Shipping Address</h3>
                                <fieldset class="">
                                <input type="hidden" id="top_shipping_address_id" value="" name="shipping_address_id">	
                                <input type="hidden" id="top_shipping:address_id" value="" name="shipping[address_id]">
                                <ul class="form-list">
                                <li>
                                <label class="required" for="top_shipping:street1"><em>*</em>Address</label>
                                <div class="input-box validation-error" id="">
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text required-entry street1" value="" id="top_shipping:street1" name="shipping[street][]" title="Street Address">
                                </div>
                                
                                
                                <label> </label>
                                <div class="input-box" id="">
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text validation-passed" value="" id="top_shipping:street2" name="shipping[street][]" title="Street Address 2">
                                </div>
                                
                                </li>
                                
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="top_shipping:city"><em>*</em>City</label>
                                <div class="input-box validation-error" id="">
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" id="top_shipping:city" class="input-text required-entry shipping-city" value="" name="shipping[city]" title="City">
                                </div>
                                </div>
                                <div class="field" id="">
                                <label class="required" for="top_shipping:region"><em>*</em>State/Province</label>
                                <div class="input-box validation-error" id="">
                                
                                <select style="" class="validate-select shipping-region-id" title="State/Province" name="shipping[region_id]" id="top_shipping:region_id" defaultvalue="">
                                <option value="">Please select region, state or province</option>
                                <?php
                                foreach($regionCollection as $region) {
                                ?>
                                <option value="<?php echo $region['name'] ?>" ><?php echo $region['name'] ?></option>
                                <?php
                                }
                                ?>
                                </select>
                                
                                
                                
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" style="display:none;" class="input-text shipping-region validation-passed" title="State/Province" value="" name="shipping[region]" id="top_shipping:region">
                                </div>
                                </div>
                                </li>
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="top_shipping:postcode" id=""><em>*</em>Zip/Postal Code</label>
                                <div class="input-box validation-error" id="">
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" class="input-text validate-zip-international required-entry " value="" id="top_shipping:postcode" name="shipping[postcode]" title="Zip/Postal Code">
                                </div>
                                </div>
                                <div class="field">
                                <label class="required" for="top_shipping:country_id"><em>*</em>Country</label>
                                <div class="input-box validation-passed" id="">
                                <select onchange="updateTopShippingRegion()" title="Country" class="validate-select shipping-country" id="top_shipping:country_id" name="shipping[country_id]">
                                    <option value=""> </option>
                                        <?php
                                        foreach($countryCollection as $country) {
                                        ?>
                                        <option value="<?php echo $country['country_id'] ?>" ><?php echo $country['name'] ?></option>
                                        <?php
                                        }
                                        ?>
                                </select>
                                </div>
                                </div>
                                </li>
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="shipping:telephone"><em>*</em>Telephone</label>
                                <div class="input-box validation-error" id="">
                                <input type="text" onchange="customerQuote.checkSameAsBilling();" id="top_shipping:telephone" class="input-text required-entry telephone " title="Telephone" value="" name="shipping[telephone]">
                                </div>
                                </div>
                                <div class="field">
                                <div class="inhanddate-select"><label for="inhanddate">In Hand Date &nbsp;&nbsp; </label><input type="text" style="margin-right:5px;" class="input-text validate-date validation-passed" value="" id="inhanddate" name="in_hand_date"> <img id="inhanddate_trig" title="Select Date" class="v-middle" alt="Select Date" src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);?>frontend/default/default/images/grid-cal.gif"></div>

                                </div>
                                </li>     
                                <li class="no-display"><input type="hidden" value="0" id="top_shipping_save_in_address_book" name="shipping[save_in_address_book]"></li>
                                
                                </ul>     
                                
                                </fieldset>					    <h3 class="billing-h3">Billing Address</h3>
                                <span class="control">
                                <input type="checkbox" class="checkbox validation-passed" onclick="same_billing(this.checked)" title="Same as Shipping Address" value="1" id="top_billing:same_as_shipping" name="billing[same_as_shipping]"><label for="top_billing:same_as_shipping">Same as Shipping Address</label>
                                </span>
                                <fieldset>
                                <input type="hidden" id="top_billing_address_id" value="" name="billing_address_id">	
                                <input type="hidden" id="top_billing:address_id" value="28649" name="billing[address_id]">
                                <ul class="form-list">
                                <li>
                                <label class="required" for="top_billing:street1"><em>*</em>Address</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text required-entry street1 validation-passed" value="<?php echo $street1;?>" id="top_billing:street1" name="billing[street][]" title="Street Address">
                                </div>
                                
                                
                                <label> </label>
                                <div class="input-box" id="">
                                <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text validation-passed" value="" id="top_billing:street2" name="billing[street][]" title="Street Address 2">
                                </div>
                                
                                </li>
                                
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="top_billing:city"><em>*</em>City</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" onchange="customerQuote.setSameAsBilling(false);" id="top_billing:city" class="input-text required-entry billing-city validation-passed" value="<?php echo $city;?>" name="billing[city]" title="City">
                                </div>
                                </div>
                                <div class="field" id="">
                                <label class="required" for="top_billing:region"><em>*</em>State/Province</label>
                                <div class="input-box validation-passed" id="">
                                <select onchange="customerQuote.setSameAsBilling(false);" style="" class="validate-select billing-region-id validation-passed" title="State/Province" name="billing[region_id]" id="top_billing:region_id" defaultvalue="23">
                                <option value="">Please select region, state or province</option>
                                
                                <?php
                                foreach($regionCollection as $region) {
                                ?>
                                <option value="<?php echo $region['name'] ?>" <?php if($region == $region['name'])echo 'selected'?> ><?php echo $region['name'] ?></option>
                                <?php
                                }
                                ?>
                                
                                </select>
                                
                                <input type="text" style="display:none;" class="input-text billing-region validation-passed" title="State/Province" value="Illinois" name="billing[region]" id="top_billing:region">
                                </div>
                                </div>
                                </li>
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="top_billing:postcode" id=""><em>*</em>Zip/Postal Code</label>
                                <div class="input-box validation-passed" id="">
                                <input type="text" onchange="customerQuote.setSameAsBilling(false);" class="input-text validate-zip-international required-entry validation-passed" value="<?php echo $postcode;?>" id="top_billing:postcode" name="billing[postcode]" title="Zip/Postal Code">
                                </div>
                                </div>
                                <div class="field">
                                <label class="required" for="top_billing:country_id"><em>*</em>Country</label>
                                <div class="input-box validation-passed" id="">
                                <select onchange="updateTopBillingRegion()" title="Country" class="validate-select billing-country validation-passed" id="top_billing:country_id" name="billing[country_id]"><option value=""> </option>
                                    <?php
                                    foreach($countryCollection as $country) {
                                    ?>
                                    <option value="<?php echo $country['country_id'] ?>" <?php if($country_id == $country['country_id'])echo 'selected'?> ><?php echo $country['name'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                </div>
                                </div>
                                </li>
                                <li class="fields">
                                <div class="field">
                                <label class="required" for="billing:telephone"><em>*</em>Telephone</label>
                                <div class="input-box validation-passed" id="">
                                <input  type="text" onchange="customerQuote.setSameAsBilling(false);" id="top_billing:telephone" class="input-text required-entry telephone validation-passed" title="Telephone" value="<?php echo $telephone;?>" name="billing[telephone]">
                                </div>
                                </div>
                                </li>     
                                <li class="no-display">
                                <input type="hidden" value="0" id="top_billing_save_in_address_book" name="billing[save_in_address_book]"></li>
                                
                                </ul>     
                                
                                </fieldset>					    					    	<!--			    </form>-->
                                </div>
                                <div style="display:none;" id="ship"></div><div id='ship_message' style="display:none; color:red;">Please select a option</div>
                                </form> 
                                </div>
	
		
                                
          


	<div id="save_button" class="button-wrapper">
		<button onclick="email_open();" type="button" title="Save &amp; Generate Quote" class="submit button save-quote" id="customerquote-save-button" value="Save &amp; GSubmitCreateForm1()enerate Quote"><span><span>Save &amp; Generate Quote</span></span></button>
	</div>
	
	<div id='email_button' style="display: none" class="button-wrapper">
		<button onclick="SubmitCreateForm();" type="button" title="Email Quote" class="submit button email-quote" id="customerquote-email-button" value="Email Quote">
			<span><span>Email Quote</span></span>
		</button>
	</div>
 <?php           
}
else
{
    echo 'wrong';
}
?>