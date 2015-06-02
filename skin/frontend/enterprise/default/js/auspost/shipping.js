// used in shipping.phtml
function loadCollectionPoint (url) 
{
    jQuery('#shipping_continue_button').attr('disabled', 'disabled');
    jQuery('#container-collection-point').show();
    jQuery('#shippinginfo-please-wait').show();
    jQuery.get(url+'postcode/'+jQuery('#cp_postcode').val(),
        function(data) {
            jQuery('div#container-collection-point-select').html(data);
            jQuery('#shippinginfo-please-wait').hide();
        }
    );
}

function syncCollectionPoint (url)
{
    jQuery('#shipping_continue_button').attr('disabled', 'disabled');
    jQuery('#shippinginfo-please-wait').show();
    jQuery.getJSON(url+'id/'+jQuery('#colection-point-select-box').val(),
        function(data) {
            $('shipping:street1').setValue('[Collection Point #'+data['DeliveryPointIdentifier']+'] ');
            $('shipping:street2').setValue(data['AddressLine']);
            $('shipping:country_id').setValue(data['CountryCode']);
            shippingRegionUpdater.update();
            $('shipping:postcode').setValue(data['PostCode']);
            $('shipping:city').setValue(data['SuburbOrPlaceOrLocality']);
            $('shipping:region').setValue(data['StateOrTerritory']);
            jQuery('#shippinginfo-please-wait').hide();
            jQuery('#shipping_continue_button').attr('disabled', false);
            
            // if no name, and phone number
            if($('shipping:firstname').getValue()=='' || $('shipping:firstname').getValue()==null)
                $('shipping:firstname').setValue(billing_data.firstname);
            if($('shipping:lastname').getValue()=='' || $('shipping:lastname').getValue()==null)
                $('shipping:lastname').setValue(billing_data.lastname);
            if($('shipping:telephone').getValue()=='' || $('shipping:telephone').getValue()==null)
                $('shipping:telephone').setValue(billing_data.telephone);
                
            if (data['CustomerAccessSummary'] != '' && data['CustomerAccessSummary'] != null)
            {
                jQuery('#collection_point_instruction').html(data['CustomerCollectionPointName']+' ('+ data['CustomerAccessSummary']+')');
                jQuery('#customer_access_summary').show();
            }
            else {
                jQuery('#collection_point_instruction').html('');
                jQuery('#customer_access_summary').hide();
            }
        }
    );
}

function setShippingInfo (customer)
{
    $('shipping:street1').setValue(customer.AddressLine1);
    $('shipping:street2').setValue(customer.AddressLine2);
    $('shipping:city').setValue(customer.Suburb);
    $('shipping:country_id').setValue(customer.CountryCode);
    shippingRegionUpdater.update();
    $('shipping:postcode').setValue(customer.PostCode);
    $('shipping:region').setValue(customer.State);
    $('shipping:telephone').setValue(customer.Phone);
    jQuery('select.shipping_region_id option').each(function () {
        if (jQuery(this).html() == customer.StateName)
            jQuery('select.shipping_region_id').val(jQuery(this).attr('value'));
    });
}

function setBillingInfo (customer)
{
    $('billing:street1').setValue(customer.AddressLine1);
    $('billing:street2').setValue(customer.AddressLine2);
    $('billing:city').setValue(customer.Suburb);
    $('billing:country_id').setValue(customer.CountryCode);
    billingRegionUpdater.update();
    $('billing:postcode').setValue(customer.PostCode);
    $('billing:region').setValue(customer.State);
    $('billing:telephone').setValue(customer.Phone);
    jQuery('select.billing_region_id option').each(function () {
        if (jQuery(this).html() == customer.StateName)
            jQuery('select.billing_region_id').val(jQuery(this).attr('value'));
    });
}

function emptyAddress ()
{
    $('shipping:street1').setValue('');
    $('shipping:street2').setValue('');
    $('shipping:city').setValue('');
    $('shipping:country_id').setValue('');
    $('shipping:postcode').setValue('');
    $('shipping:region').setValue('');
    $('shipping:region_id').setValue('');
}

// used in available.phtml
function loadScheduleTable (id, url, date)
{
    var type = id.split('_').pop();
    jQuery('#auspost_delivery_dates').val(type);
    jQuery('#delivery_schedule_table').html('');
    
    if (type == 1 || type == 101)
        return;
        
    if (type != 0)
    {
        jQuery('#delivery_schedule_table').show();
        jQuery('#timeslot-please-wait').show();
        jQuery.get(url+'type/'+type+'/start/'+date,
            function (data) {
                jQuery('#delivery_schedule_table').html(data);
                jQuery('#timeslot-please-wait').hide();
                
                // Handle radio group click
                if (type == 3 || type == 103 || type == 4 || type == 104) {
                    jQuery('#delivery_schedule_table input.group-radio').bind('click', function () {
                        jQuery('input.sub-checkbox').attr('checked', false);
                        jQuery('#auspost_delivery_dates').val(type);
                    });
                    jQuery('#delivery_schedule_table input.sub-checkbox').bind('click', function () {
                        var div = jQuery(this).parent('div');
                        var group = jQuery(div).attr('id');
                        var group_id = group.split('_').pop();
                        
                        if (!jQuery('#radio_'+group_id).attr('checked'))
                        {
                            jQuery('#radio_'+group_id).attr('checked', true);
                            jQuery('#auspost_delivery_dates').val(type);
                        }
                        
                        jQuery('input.sub-checkbox').each (function (){
                            var div2 = jQuery(this).parent('div');
                            var group2 = jQuery(div2).attr('id');
                            var group_id2 = group2.split('_').pop();
                            
                            if (group_id != group_id2)
                                jQuery(this).attr('checked', false);
                        });
                    });
                } 
                
                // Handle radio click
                else
                {
                    jQuery('#delivery_schedule_table input.main-radio').bind('click', function () {
                        var vals = jQuery('#auspost_delivery_dates').val();
                        var tmp = vals.split(',');
                        var tmp1 = new Array();
                        tmp1.push(tmp[0]);
                        tmp1.push(jQuery(this).val());
                        jQuery('#auspost_delivery_dates').val(tmp1.join(','));
                    });
                }
                
                // Handle checkbox click
                jQuery('#delivery_schedule_table input[type=checkbox]').bind('click', function () {
                    var vals = jQuery('#auspost_delivery_dates').val();
                    var tmp = vals.split(',');
                    if (jQuery(this).attr('checked'))
                        tmp.push(jQuery(this).val());
                    else {
                        if (vals != '')
                        {
                            for(var i=0; i<tmp.length; i++) {
                                if (tmp[i] == jQuery(this).val())
                                    tmp.splice(i, 1);
                            }
                        }
                    }
                    jQuery('#auspost_delivery_dates').val(tmp.join(','));
                });
            }
        );
    }
    else 
        jQuery('#delivery_schedule_table').hide();
}

function standardShippingMethods ()
{
    jQuery('input[name=shipping_method]').each (function () {
        var id = jQuery(this).attr('id').split('_').pop();
        var li = jQuery(this).parent('li');
        if (id>100) {
            jQuery(li).hide();
        }
        else
            jQuery(li).show();
    });
}

function expressShippingMethods ()
{
    jQuery('input[name=shipping_method]').each (function () {
        var id = jQuery(this).attr('id').split('_').pop();
        var li = jQuery(this).parent('li');
        if (id<100) {
            jQuery(li).hide();
        }
        else
            jQuery(li).show();
    });
}