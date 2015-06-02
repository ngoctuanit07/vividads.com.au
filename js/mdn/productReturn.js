//******************************************************************************
//display action
function AppearBlock(element,idBlock)
{
	if(element.checked == 1)
		document.getElementById(idBlock).style.display = 'block';
	else
	{
		document.getElementById(idBlock).style.display = 'none';			
		document.getElementById('data[rma_action]').value = '';			
	}
}

//******************************************************************************
//validate form
function validateProductReturnForm()
{
	var validated = false;
	
	//check if there is processed products
	var hasProductToProcess = false;
	var hasProductToProcessWithoutDestination = false;
	var radios = document.getElementsByTagName('input');
	var hasProductQty = false;
	var hasProductQtyField
	for (i=0; i < radios.length; i++) 
	{
		if (radios[i] && radios[i].id != null)
		{
			if ((radios[i].id.indexOf('rad_action_') != -1) && (radios[i].checked))
			{
				if(radios[i].value != 'noaction')
				{
					hasProductToProcess = true;
					
					//check if product destination is set
					var rpId = radios[i].id.replace('rad_action_', '');
					var destiniationSelectName = 'dest_' + rpId;
					if (document.getElementById(destiniationSelectName).selectedIndex == 0)
						hasProductToProcessWithoutDestination = true;
				}
			}
			
			if ((radios[i].id.indexOf('data[rp_qty_') != -1))
			{
				hasProductQtyField = true;
				if(radios[i].value > 0)
					hasProductQty = true;
			}
		}
	}
	
	if (rmaCreationMode && (!hasProductQty))
	{
		alert(Translator.translate('Please select at least on product'));
		return false;
	}
	
	//require destiniation for products to process
	if (hasProductToProcessWithoutDestination)
	{
		alert(Translator.translate('Please select destination for products to process'));
		return false;
	}
	
	//ask for confirmation if there are products to process
	if (hasProductToProcess)
	{
		if (!confirm(Translator.translate('Please confirm products processing')))
			return false;
	}

	//submit form
	document.getElementById('edit_form').submit();
}

//******************************************************************************
//select action for product change
function actionChanged(productId, radiobutton)
{
	var exchangeSpanName = 'span_exchange_' + productId;
	var destinationComboBoxName = 'dest_' + productId;
	switch(radiobutton.value)
	{
		case 'exchange':
			document.getElementById(exchangeSpanName).style.display = '';
			document.getElementById(destinationComboBoxName).style.display = '';
			selectValueInSelect(destinationComboBoxName, 'Back to stock');
			break;
		case 'refund':
			document.getElementById(exchangeSpanName).style.display = 'none';
			document.getElementById(destinationComboBoxName).style.display = '';
			selectValueInSelect(destinationComboBoxName, 'Back to stock');
			break;
		case 'return':
			document.getElementById(exchangeSpanName).style.display = 'none';
			document.getElementById(destinationComboBoxName).style.display = '';
			selectValueInSelect(destinationComboBoxName, 'Back to customer');
			break;
		default:
			document.getElementById(exchangeSpanName).style.display = 'none';
			document.getElementById(destinationComboBoxName).style.display = 'none';
			selectValueInSelect(destinationComboBoxName, '');
			break;
	}
}

//******************************************************************************
//show popup to select product for product exchange
function selectExchangeProduct(productId)
{
	//display a popup window to select product 
	var url = ProductExchangeSelectionPopupUrl;
	url = url.replace('XXX', productId);
	window.open(url);
}

//******************************************************************************
//select product for exhange
function selectProductForExchange(productId, rpId, name)
{
	//fill product id in hidden field
	var hiddenFieldName = 'hidden_exchange_' + rpId;
	window.opener.document.getElementById(hiddenFieldName).value = productId;
	
	//change product substitution name
	var spanProductName = 'span_exchange_product_name_' + rpId;
	window.opener.document.getElementById(spanProductName).innerHTML = name;
	
	//close window
	window.close();
}

//******************************************************************************
//
function selectValueInSelect(selectName, value)
{
	var mySelect = document.getElementById(selectName);
	var i = 0;
	for (i=0;i<mySelect.options.length;i++)
	{
		if (mySelect.options[i].value == value)
		{
			mySelect.selectedIndex = i;
			return true;
		}
	}
	
	return false;
}

//******************************************************************************
//
function mailToCustomer()
{
	var mail = document.getElementById('data[rma_customer_email]').value;
	var url = 'mailto:' + mail;
	document.location.href = url;
}

//******************************************************************************
//reserve product
function reserveProduct(productId)
{
	var qty = document.getElementById('rr_qty_' + productId).value
	var url = reservationUrl + 'qty/' + qty + '/product_id/' + productId;
	document.location.href = url;
}