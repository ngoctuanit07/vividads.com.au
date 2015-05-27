IWD.OPC.Plugin = {
		
		observer: {},
		
		
		dispatch: function(event, data){
			console.debug('DISPATCH::EVENT::' + event);			
			if (typeof(IWD.OPC.Plugin.observer[event]) !="undefined"){
				
				var callback = IWD.OPC.Plugin.observer[event];
				callback(data);
				
			}
		},
		
		event: function(eventName, callback){
			IWD.OPC.Plugin.observer[eventName] = callback;
		}
};

/** PAYPAL EXPRESS CHECKOUT LIGHTBOX **/
IWD.OPC.Lipp = {
		init: function(){
			if (IWD.OPC.Checkout.config.paypalLightBoxEnabled==true){
				IWD.OPC.Plugin.event('redirectPayment', IWD.OPC.Lipp.checkPaypalExpress);
			}
		},
		
		checkPaypalExpress:function(url){
			IWD.OPC.Checkout.showLoader();
			try{
				if (url.match(/paypal\/express\/start/i)){
					IWD.OPC.Checkout.xhr = true;
					IWD.OPC.Lipp.prepareToken();
				}
			
			}catch(e){
				IWD.OPC.Checkout.xhr = null;
			}
		},
		
		prepareToken: function(){
			$j.post(IWD.OPC.Checkout.config.baseUrl + 'onepage/express/start',{"redirect":'onepage'}, IWD.OPC.Lipp.prepareTokenResponse,'json');
		},
		
		prepareTokenResponse: function(response){
			if (typeof(response.error)!="undefined"){
				if (response.error==false){
					IWD.OPC.Checkout.hideLoader();
					PAYPAL.apps.Checkout.startFlow(IWD.OPC.Checkout.config.paypalexpress + response.token);
				}
				
				if (response.error==true){
					alert(response.message);
				}
			}
		}
		
		
}

$j(document).ready(function(){
	IWD.OPC.Lipp.init(); 
});
