	
	jQuery=jQuery.noConflict();
	jQuery(function() {
			   
				var bar = jQuery('.bar');
				var percent = jQuery('.percent');
				
				/* @reload captcha
				------------------------------------------- */			   
			//	function reloadCaptcha(){
//					jQuery("#captcha").attr("src","php/captcha.php?r=" + Math.random());
//				}
				function reloadCaptcha(){
					/*
					jQuery(".captcha").attr("src","<?php echo Mage::getBaseUrl(); ?>externalform/captcha.php?ts=<?php echo time(); ?>&text=<?php echo jQuerytext; ?>");
					*/
					updateCaptcha();
				}
				
				jQuery('.captcode').click(function(e){
					e.preventDefault();
					reloadCaptcha();
				});
				
				/***Contact Form Code***/
				jQuery("#shabbircontactForm").validate({
						/* @validation states + elements 
						------------------------------------------- */
						errorClass: "state-error",
						validClass: "state-success",
						errorElement: "em",
						onkeyup: false,
						onclick: false,						
						
						/* @validation rules 
						------------------------------------------ */
						rules:{
							name:{
								required:true,
								minlength: 2
								},
							email:{
								required:true,
								email: true
								},
							comment:{
								required:true,
								minlength: true
								},
								
							captcha:{
									required:true,																	
									equalTo: '[name="captchavalue"]',								}
		
							},
						messages:{
							required:'Enter Your Name',
							minlength: 'Name must be at least 2 characters'
							
						},
						email:{
							 required: 'Enter your email address',
							 email: 'Enter a VALID email address'
							},
						
						comment:{
							required:'Please Enter Your Message',
							minlength:true,
							},
						captcha:{
								 required: 'Please enter proper security code',
										 
								}
								
								
								
						
					}
				
				)
				
				
				
				
					jQuery( "#cus_ordfrm" ).validate({
				
						/* @validation states + elements 
						------------------------------------------- */
						errorClass: "state-error",
						validClass: "state-success",
						errorElement: "em",
						onkeyup: false,
						onclick: false,						
						
						/* @validation rules 
						------------------------------------------ */
						rules: {
								fnamebill: {
										required: true,
										minlength: 2
								},		
								lnamebill: {
										required: true,
										minlength: true
								},
								phonebill: {
										required: true,
										number:true
								},
								emailbill: {
										required: true,
										email:true
								},
								conemailbill:{
									  required:true,
									  equalTo: '[name="emailbill"]',
									  email:true
									},
								captcha:{
										required:true,																	
										equalTo: '[name="captchavalue"]',
								},
								
						},
						
						/* @validation error messages 
						---------------------------------------------- */
						messages:{
								fnamebill: {
										required: 'Enter your name',
										minlength: 'Name must be at least 2 characters'
								},				
								lnamebill: {
										required: 'Enter your last name',
										minlength: 'Last Name must be at least 2 characters'
								},
								phonebill: {
										required: 'Please Enter your phone number',
										digits:'Please enter only digits'
								},
								emailbill: {
										required: 'Enter your email address',
										email: 'Enter a VALID email address'
								},
								conemailbill:{
										required:'Enter your email again',
									 	email: 'Enter your email again',
									},
							
								captcha:{
										required: 'Please enter proper security code',
										 
								}
						},

						/* @validation highlighting + error placement  
						---------------------------------------------------- */	
						highlight: function(element, errorClass, validClass) {
								jQuery(element).closest('.field').addClass(errorClass).removeClass(validClass);
						},
						unhighlight: function(element, errorClass, validClass) {
								jQuery(element).closest('.field').removeClass(errorClass).addClass(validClass);
						},
						errorPlacement: function(error, element) {
						   if (element.is(":radio") || element.is(":checkbox")) {
									element.closest('.option-group').after(error);
						   } else {
									error.insertAfter(element.parent());
						   }
						},
						
						/* @ajax form submition 
						---------------------------------------------------- */						
						submitHandler:function(form) {
							
							jQuery(form).ajaxSubmit({
									target:'.result',
									type: "POST",
									dataType:"JSON",					
								//	data: jQuery("#cus_ordfrm").serialize(),						
												   
									beforeSubmit:function(){
										 
										
										var percentVal = '0%';
										bar.width(percentVal);
										percent.html(percentVal);
										jQuery( ".progress-section" ).show();
										jQuery('.form-footer').addClass('progress');
										
									},
									uploadProgress: function(event, position, total, percentComplete) {
										var percentVal = percentComplete + '%';
										bar.width(percentVal);
										percent.html(percentVal);
									},								
									error:function(){
										jQuery( ".progress-section" ).hide(500);
										jQuery('.form-footer').removeClass('progress');
									},
									 success:function(data){
										 
										var percentVal = '100%';
										bar.width(percentVal);
										percent.html(percentVal);
										jQuery('.progress-section').show().delay(5000).fadeOut();											
										jQuery('.form-footer').removeClass('progress');
										
										//placing values to the display
										jQuery('#quote_id').html(data.id);
										jQuery('#hlink').attr('href',data.hlink);
										jQuery('.alert-success').show();
										//jQuery('.alert-success').show().delay(7000).fadeOut();
										
										
										jQuery('.field').removeClass("state-error, state-success");
										if( jQuery('.alert-error').length == 0){
										 jQuery('#sub_cusord').hide().delay(1000).fadeOut();
										 window.top.location.href=data.hlink;
										 //	jQuery('#cus_ordfrm').resetForm();
										 //	reloadCaptcha();
										}
											
									 }
							  });
						}
						
				});
				

					jQuery( "#contactForm" ).validate({
				
						/* @validation states + elements 
						------------------------------------------- */
						errorClass: "state-error",
						validClass: "state-success",
						errorElement: "em",
						onkeyup: false,
						onclick: false,						
						
						/* @validation rules 
						------------------------------------------ */
						rules: {
							name:{
								required:true,
								minlength: 2
								},
							email:{
								required:true,
								email: true
								},
							comment:{
								required:true,
								minlength: true
								},
								
							captcha:{
									required:true,																	
									equalTo: '[name="captchavalue"]',								}
		
							},
						
						/* @validation error messages 
						---------------------------------------------- */
						messages:{
								name: {
										required: 'Enter your name',
										minlength: 'Name must be at least 2 characters'
								},				
								email: {
										required: 'Enter your email address',
										email: 'Enter a VALID email address'
								},
								comment:{
										required:'Enter your comment again',
									 	email: 'Enter your comment again',
									},
							
								captcha:{
										required: 'Please enter proper security code',
										 
								}
						},

						/* @validation highlighting + error placement  
						---------------------------------------------------- */	
						highlight: function(element, errorClass, validClass) {
								jQuery(element).closest('.field').addClass(errorClass).removeClass(validClass);
						},
						unhighlight: function(element, errorClass, validClass) {
								jQuery(element).closest('.field').removeClass(errorClass).addClass(validClass);
						},
						errorPlacement: function(error, element) {
						   if (element.is(":radio") || element.is(":checkbox")) {
									element.closest('.option-group').after(error);
						   } else {
									error.insertAfter(element.parent());
						   }
						},
						
						/* @ajax form submition 
						---------------------------------------------------- */						
						submitHandler:function(form) {
							
							jQuery(form).ajaxSubmit({
									target:'.result',
									type: "POST",
									dataType:"JSON",					
								//	data: jQuery("#cus_ordfrm").serialize(),						
												   
									beforeSubmit:function(){
										 
										
										var percentVal = '0%';
										bar.width(percentVal);
										percent.html(percentVal);
										jQuery( ".progress-section" ).show();
										jQuery('.form-footer').addClass('progress');
										
									},
									uploadProgress: function(event, position, total, percentComplete) {
										var percentVal = percentComplete + '%';
										bar.width(percentVal);
										percent.html(percentVal);
									},								
									error:function(){
										jQuery( ".progress-section" ).hide(500);
										jQuery('.form-footer').removeClass('progress');
									},
									 success:function(data){
										jQuery('.sButton').hide().delay(5000).fadeOut();																																									 										var percentVal = '100%';
										bar.width(percentVal);
										percent.html(percentVal);
										jQuery('.progress-section').show().delay(5000).fadeOut();
										jQuery('.alert-success').show().delay(7000).fadeIn();										
										jQuery('.form-footer').removeClass('progress');
										jQuery('.smart-forms').removeClass('form-body');										
										jQuery('.form-body').hide().delay(5000).fadeOut();																					

										
										//placing values to the display
//										jQuery('#quote_id').html(data.id);
//										jQuery('#hlink').attr('href',data.hlink);
											
									 }
							  });
						}
						
				});
				
					
					
		
	});				
    