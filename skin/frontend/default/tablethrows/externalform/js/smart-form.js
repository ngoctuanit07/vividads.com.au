	$=jQuery.noConflict();
	$(function() {
			   
				var bar = $('.bar');
				var percent = $('.percent');
				
				/* @reload captcha
				------------------------------------------- */			   
			//	function reloadCaptcha(){
//					$("#captcha").attr("src","php/captcha.php?r=" + Math.random());
//				}
				function reloadCaptcha(){
					/*
					$(".captcha").attr("src","<?php echo Mage::getBaseUrl(); ?>externalform/captcha.php?ts=<?php echo time(); ?>&text=<?php echo $text; ?>");
					*/
					updateCaptcha();
				}
				
				$('.captcode').click(function(e){
					e.preventDefault();
					reloadCaptcha();
				});
				
				/***Contact Form Code***/
				$("#shabbircontactForm").validate({
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
				
				
				
				
					$( "#cus_ordfrm" ).validate({
				
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
								$(element).closest('.field').addClass(errorClass).removeClass(validClass);
						},
						unhighlight: function(element, errorClass, validClass) {
								$(element).closest('.field').removeClass(errorClass).addClass(validClass);
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
							
							$(form).ajaxSubmit({
									target:'.result',
									type: "POST",
									dataType:"JSON",					
								//	data: jQuery("#cus_ordfrm").serialize(),						
												   
									beforeSubmit:function(){
										 
										
										var percentVal = '0%';
										bar.width(percentVal);
										percent.html(percentVal);
										$( ".progress-section" ).show();
										$('.form-footer').addClass('progress');
										
									},
									uploadProgress: function(event, position, total, percentComplete) {
										var percentVal = percentComplete + '%';
										bar.width(percentVal);
										percent.html(percentVal);
									},								
									error:function(){
										$( ".progress-section" ).hide(500);
										$('.form-footer').removeClass('progress');
									},
									 success:function(data){
										 
										var percentVal = '100%';
										bar.width(percentVal);
										percent.html(percentVal);
										$('.progress-section').show().delay(5000).fadeOut();											
										$('.form-footer').removeClass('progress');
										
										//placing values to the display
										$('#quote_id').html(data.id);
										$('#hlink').attr('href',data.hlink);
										$('.alert-success').show();
										//$('.alert-success').show().delay(7000).fadeOut();
										
										
										$('.field').removeClass("state-error, state-success");
										if( $('.alert-error').length == 0){
										 $('#sub_cusord').hide().delay(1000).fadeOut();
										 window.top.location.href=data.hlink;
										 //	$('#cus_ordfrm').resetForm();
										 //	reloadCaptcha();
										}
											
									 }
							  });
						}
						
				});
				

					$( "#contactForm" ).validate({
				
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
								$(element).closest('.field').addClass(errorClass).removeClass(validClass);
						},
						unhighlight: function(element, errorClass, validClass) {
								$(element).closest('.field').removeClass(errorClass).addClass(validClass);
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
							
							$(form).ajaxSubmit({
									target:'.result',
									type: "POST",
									dataType:"JSON",					
								//	data: jQuery("#cus_ordfrm").serialize(),						
												   
									beforeSubmit:function(){
										 
										
										var percentVal = '0%';
										bar.width(percentVal);
										percent.html(percentVal);
										$( ".progress-section" ).show();
										$('.form-footer').addClass('progress');
										
									},
									uploadProgress: function(event, position, total, percentComplete) {
										var percentVal = percentComplete + '%';
										bar.width(percentVal);
										percent.html(percentVal);
									},								
									error:function(){
										$( ".progress-section" ).hide(500);
										$('.form-footer').removeClass('progress');
									},
									 success:function(data){
										$('.sButton').hide().delay(5000).fadeOut();																																									 										var percentVal = '100%';
										bar.width(percentVal);
										percent.html(percentVal);
										$('.progress-section').show().delay(5000).fadeOut();
										$('.alert-success').show().delay(7000).fadeIn();										
										$('.form-footer').removeClass('progress');
										$('.smart-forms').removeClass('form-body');										
										$('.form-body').hide().delay(5000).fadeOut();																					

										
										//placing values to the display
//										$('#quote_id').html(data.id);
//										$('#hlink').attr('href',data.hlink);
											
									 }
							  });
						}
						
				});
					
					
		
	});				
    