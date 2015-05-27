/**
 * Mico Solutions EULA
 * http://www.micosolutions.com/
 * Read the license at http://www.micosolutions.com/license.txt
 *
 * Do not edit or add to this file, please refer to http://www.micosolutions.com for more information.
 *
 * @author		Pham Tri Cong <phtcong@micosolutions.com>
 * @copyright   Copyright (c) 2011 Mico Solutions (http://www.micosolutions.com)
 * @license     http://www.micosolutions.com/license.txt
 *
 */
var micoCartEditChangeLinkShow=0;var micoUploadCartButton=".product-view .add-to-cart button";var micoUploadUltimateHasUploader=0;var micoUploadUltimateTranslate=0;var micoUploadUltimateUpload="/skin/frontend/base/default/mico/js/uploader/mupload.php";var micoUploadUltimateFlash="/skin/frontend/base/default/mico/js/uploader/mupload.flash.swf";var micoUploadUltimateSilverlight="/skin/frontend/base/default/mico/js/uploader/mupload.silverlight.xap";var micoUploadUltimateCom=null;var micoUploadUltimateOptionFilelist=new Object();var micoUploadUltimateUploaderList=new Object();MicoUploadUltimate=Class.create();MicoUploadUltimate.prototype={initialize:function(a){this._sizeMin=new Array();this._sizeMax=new Array();this._fileExt=new Array();this._processing=0;if(!a){return 0}micoUploadUltimateCom=this;this._config=a;this._removedFile=0;this._uploadedItems=new Object();this._queues=new Object();this._hashValues=0;this._uploadedValues=new Object();this._countValues=new Object();this._processClass="";micoUploadUltimateCom._uploader=new Object();if(this._config.uploader.auto){this._processClass=" mico-mupload-progress-auto "}if(this._config.folder.fileMin){this._config.folder.fileMinInBytes=plupload.parseSize(this._config.folder.fileMin)*1}else{this._config.folder.fileMinInBytes=0}this._sizeMax=this._config._sizeMax;this._sizeMin=this._config._sizeMin;this._fileExt=this._config._fileExt;this.initOptions()},isMultilOption:function(a){if(this._config.uploader.multiFileGlobal||this._config.uploader.multiFileOptions[a]){return true}return false},initOptions:function(){$$(".product-custom-option").each(function(a){var b=0;a.name.sub(/[0-9]+/,function(c){b=c[0]});if(a.type=="file"){this.initOption(a,b)}}.bind(this));if(!micoCartEditChangeLinkShow){$$(".mico-mupload-uploaded-old-list").each(function(a){var b=0;a.id.sub(/[0-9]+/,function(c){b=c[0]});if(this.isMultilOption(b)){this.hideLinkChange(b,a)}}.bind(this))}},optionInputItemId:function(a){return"mico-mupload-uploader-item-"+a},initOption:function(e,c){var b=this._config.templateItem;b=b.replace(/{optionId}/gi,c);var d=e.up();var a=e.getAttribute("class");e.remove();new Insertion.Top(d,b);this._uploadedItems[c]=$(this.optionInputItemId(c));this._uploadedItems[c].setAttribute("class",a);this.initOptionUpload(e,c)},initOptionUpload:function(i,f){var k=this._config.uploader.runtimes;var d="mico-mupload-uploader-pickfiles-"+f;var j="mico-mupload-uploader-uploadfiles-"+f;var a="mico-mupload-uploader-container-"+f;var e="mico-mupload-uploader-filelist-"+f;micoUploadUltimateOptionFilelist[f]=e;var c=this.isMultilOption(f);var b=this._sizeMin[f]?this._sizeMin[f]:this._config.folder.fileMin;var g=this._sizeMax[f]?this._sizeMax[f]:this._config.folder.fileMax;var l=this._fileExt[f]?this._fileExt[f]:this._config.folder.fileExt;var h=new plupload.Uploader({multipart_params:{productId:this._config.productId,optionId:f,sizeMin:plupload.parseSize(b),sizeMax:plupload.parseSize(g),okey:this._config.okey},runtimes:k,browse_button:d,container:a,max_file_size:g,chunk_size:this._config.folder.fileChunk,unique_names:true,multi_selection:c,url:micoUploadUltimateUpload,flash_swf_url:micoUploadUltimateFlash,silverlight_xap_url:micoUploadUltimateSilverlight,filters:[{title:this._config.folder.fileFilter,extensions:l}]});h.bind("Init",function(m,n){$(e).update('<span id="mico-mupload-item-error-'+f+'" class="mico-mupload-item-error"></span><div class="clear-both"></div>')}.bind(this));h.init();h.bind("BeforeUpload",function(m,n){m.settings.multipart_params.oname=n.name});h.bind("FilesAdded",function(m,p){var n=0;for(var o=0;o<p.length;o++){n+=this.addToQueue(p[o],f)}if(n){if(this._config.uploader.auto){this.startUploader(f)}else{this.refreshStartButton(f)}}}.bind(this));h.bind("UploadProgress",function(m,n){$("mico-mupload-progress-percent-"+n.id).update(n.percent+"%");$("mico-mupload-progress-area-"+n.id).style.display="block";$("mico-mupload-progress-bar-"+n.id).style.width=n.percent+"%"}.bind(this));h.bind("Error",function(m,o){var n=o.message;if(n=="File size error."){n=this.errorMax(f)}else{n=this.translate(n)}this.showMuploadError(f,n)}.bind(this));h.bind("FileUploaded",function(m,o,n){this.uploadComplete(o,n,f)}.bind(this));$(j).hide();$(j).onclick=function(){this.startUploader(f);return false}.bind(this);micoUploadUltimateUploaderList[f]=h},startUploader:function(b){var c=micoUploadUltimateUploaderList[b];if(c.files&&c.files.length){if(c.state!=plupload.STARTED){for(var a=0;a<c.files.length;a++){if(c.files[a].status==plupload.QUEUED){c.start();this.refreshCartButton()}}}}$("mico-mupload-uploader-uploadfiles-"+b).hide()},addToQueue:function(a,c){if(!this.validateMinSize(a.size)){_uploader=micoUploadUltimateUploaderList[c];if(a){if(_uploader.state==plupload.STARTED){_uploader.stop();_uploader.removeFile(a);this.startUploader(c)}else{_uploader.removeFile(a)}}this.showMuploadError(c,this.errorMin(c));return 0}this.clearErrorById(c);this._queues[a.id]=a;var d=a.size?" ("+plupload.formatSize(a.size)+")":"";var b='<div id="mico-mupload-uploader-item-'+a.id+'" class="mico-mupload-uploader-item">';b+='<div id="mico-mupload-uploader-queue-'+a.id+'" class="mico-mupload-file">'+a.name.escapeHTML()+d+' <b><span id="mico-mupload-progress-percent-'+a.id+'">0%</span></b></div>';b+='<div id="mico-mupload-progress-area-'+a.id+'" class="mico-mupload-progress '+this._processClass+' mico-mupload-progress-right"><div class="mico-mupload-progress-container"><div id="mico-mupload-progress-bar-'+a.id+'" class="mico-mupload-progress-bar"></div></div></div>';b+='<a class="mico-mupload-uploader-item-cancel" href="#" onclick="return micoUploadUltimate.removeFromQueue(\''+a.id+"',"+c+');">Cancel</a>';b+="</div><!-- end of item -->";b+='<div class="mico-mupload-fix"></div>';$(micoUploadUltimateOptionFilelist[c]).insert({bottom:b});return 1},removeFromQueue:function(c,b){_uploader=micoUploadUltimateUploaderList[b];var a=_uploader.getFile(c);if(a){if(_uploader.state==plupload.STARTED){_uploader.stop();_uploader.removeFile(a);this.startUploader(b)}else{_uploader.removeFile(a);this.refreshStartButton(b)}$("mico-mupload-uploader-item-"+c).remove();delete this._queues[c]}this.refreshCartButton();return false},uploadComplete:function(b,e,d){if((b.size===undefined)||(b.size)){delete this._queues[b.id];var c=$("mico-mupload-uploader-item-"+b.id).down("a");$("mico-mupload-uploader-item-"+b.id).addClassName("mico-meditor-uploader-item-uploaded");c.remove();if(e.response){var a=jQuery.parseJSON(e.response);this.uploadedValues(a,b,d);this.showPreview(a,b,d)}this.refreshCartButton()}},uploadedValues:function(a,b,d){if(a.error){return 0}if(!this.validateMinSize(b.size)){return 0}this.clearErrorById(d);if(!this._uploadedValues[d]){this._uploadedValues[d]=new Object();this._countValues[d]=1}else{this._countValues[d]+=1}if(!this._hashValues){this._hashValues=new Object()}if(this.isMultilOption(d)){if(!this._hashValues[d]){this._hashValues[d]=new Object()}}else{this._hashValues[d]=new Object()}var c=new Object();c.name=a.value;c.oname=b.name;this._hashValues[d][a.hash]=c;this._uploadedValues[d][a.hash]=c;this.setUploadedItemValue(d,this._uploadedItems[d],jQuery.toJSON(this._hashValues[d]));data=new Object();data.optionId=d;data.file=b;data.json=a;this._waitingCrop=1;jQuery(document).trigger("MuploadEventUploadComplete",data)},checkProcessing:function(){if(this._processing){this.showMessage("Processing");return 0}return 1},translate:function(a){if(!micoUploadUltimateTranslate){return a}if(micoUploadUltimateTranslate[a]){return micoUploadUltimateTranslate[a]}return a},downloadLink:function(a){return this._config.uploadedFileUrl+a},thumbnailLink:function(a){return this.downloadLink(a)},isImage:function(a){if(!a){return false}if(/(jpg|png|jpeg|gif|bmp)$/i.test(a)){return true}return false},showPreview:function(b,c,f){if(b.error){if(b.error.message){this.showMuploadError(f,b.error.message)}else{this.showMuploadError(f,b.error)}return 0}var a=this.isMultilOption(f);var g="";var e="";var d="";optionLink=this.downloadLink(b.value);optionName=c.name?c.name.escapeHTML():"";optionThumbnail="";optionHash=b.hash;if(b.thumbnail){optionThumbnail=this.thumbnailLink(b.thumbnail);g=this._config.templatePreviewThumbnail}else{g=this._config.templatePreviewNoThumnail}if(a){g=g.replace(/{optionSelect}/gi,this._config.templatePreviewCheckbox);g=this._config.templatePreviewMulti.replace(/{content}/gi,g)}else{g=g.replace(/{optionSelect}/gi,"")}if(b.resolution&&b.resolution.quality){d=this._config.resolution.template;d=d.replace(/{quality}/gi,b.resolution.quality);d=d.replace(/{qualityType}/gi,b.resolution.qualityType)}g=g.replace(/{optionResolution}/gi,d);g=g.replace(/{optionThumbnail}/gi,optionThumbnail);g=g.replace(/{optionLink}/gi,optionLink);g=g.replace(/{optionName}/gi,optionName);g=g.replace(/{optionHash}/gi,optionHash);g=g.replace(/{optionId}/gi,f);if(a){$("mico-mupload-preview-"+f).insert({bottom:g})}else{$("mico-mupload-preview-"+f).update(g)}},onSelectUploadedItem:function(b,d,f){if(!this._uploadedItems[d]||!this._uploadedValues||!this._uploadedValues[d]){return 0}if(b.checked){if(this._countValues[d]==0){if(!this._hashValues){this._hashValues=new Object()}this._hashValues[d]=new Object()}this._hashValues[d][f]=this._uploadedValues[d][f];this._countValues[d]+=1}else{var c=this.checkUploadedFileInputSelected(d);if(!c&&(this._countValues[d]==1)&&this._uploadedItems[d].hasClassName("required-entry")){b.checked=true;return 0}this._countValues[d]-=1;if(this._countValues[d]<0){this._countValues[d]=0}if(this._hashValues[d]&&this._hashValues[d][f]){delete this._hashValues[d][f]}}var a=this._hashValues[d];var e=a?jQuery.toJSON(a):"";this.setUploadedItemValue(d,this._uploadedItems[d],e)},showMessage:function(a){alert(this.translate(a))},validateMinSize:function(a){if((a===undefined)||!a){return 1}if(this._config.folder.fileMinInBytes&&(a<this._config.folder.fileMinInBytes)){return 0}return 1},errorMax:function(b){var a=this.translate("File Max Size {fileMax}");var c=this._sizeMax[b];if(c){a=a.replace(/{fileMax}/gi,plupload.formatSize(c))}else{a=a.replace(/{fileMax}/gi,this._config.folder.fileMax)}return a},errorMin:function(b){var a=this.translate("File Min Size {fileMin}");var c=this._sizeMin[b];if(c&&(c>this._config.folder.fileMinInBytes)){a=a.replace(/{fileMin}/gi,plupload.formatSize(c))}else{a=a.replace(/{fileMin}/gi,this._config.folder.fileMin)}return a},showMuploadError:function(b,a){$("mico-mupload-item-error-"+b).update(a);$("mico-mupload-item-error-"+b).show()},showError:function(d,b,c){var a=Validation.getAdvice(b,d);if(a==null){a=Validation.createAdvice(b,d,false,c)}else{a.update(c)}Validation.showAdvice(d,a,b);d.addClassName("validation-failed")},clearError:function(c,b){return"";var a=Validation.getAdvice(b,c);Validation.hideAdvice(c,a);c.removeClassName("validation-failed")},clearErrorById:function(a){$("mico-mupload-item-error-"+a).hide()},refreshStartButton:function(c){var a="mico-mupload-uploader-uploadfiles-"+c;if(this._config.uploader.auto){return 0}var d=micoUploadUltimateUploaderList[c];var b=false;if(d&&(d.state==plupload.STARTED)){b=true}if(b){$(a).hide()}else{$(a).show()}},refreshCartButton:function(){var a=false;jQuery.each(micoUploadUltimateUploaderList,function(c,d){if(c&&d&&d.files&&(d.state==plupload.STARTED)){for(var b=0;b<d.files.length;b++){if(d.files[b].status==plupload.UPLOADING){a=true}}}}.bind(this));this.disableButton(micoUploadCartButton,a)},disableCartButton:function(){this.disableButton(micoUploadCartButton,true)},enableCartButton:function(){this.disableButton(micoUploadCartButton,false)},disableButton:function(b,a){$$(b).each(function(c){c.disabled=a;if(a){c.addClassName("mico-mupload-btn-none")}else{c.removeClassName("mico-mupload-btn-none")}}.bind(this))},log:function(a){$("mico-mupload-log").insert({top:a+"<br/>"})},onSelectUploadedOldItem:function(b,e,a){if(!this.isMultilOption(e)){return 0}if(!this._uploadedItems[e]){return 0}var d=this._uploadedItems[e];var f=d.getValue();if(f&&(f!=="{}")){return 0}var c=this.checkUploadedFileInputSelected(e);if(c){d.setValue(c);return 0}if(this._uploadedItems[e].hasClassName("required-entry")){b.checked=true;return 0}},showUploadedFileInput:function(b){if(!this.isMultilOption(b)){return 0}var a=this.getUploadedFileInputFilter(b);$$(a).each(function(c){c.checked=true;c.removeClassName("mico_custom_file_uploaded_none")}.bind(this))},hideUploadedFileInput:function(b){if(!this.isMultilOption(b)){return 0}var a=this.getUploadedFileInputFilter(b);$$(a).each(function(c){c.checked=false;c.addClassName("mico_custom_file_uploaded_none")}.bind(this))},getUploadedFileInputFilter:function(a){return"input.mico_custom_file_uploaded_"+a},checkUploadedFileInputSelected:function(c){var b=this.getUploadedFileInputFilter(c);var a="";$$(b+":checked").each(function(d){a="{}"}.bind(this));return a},getNewUploadedFileSelected:function(d){var c=this.getUploadedFileInputFilter(d);var a=new Object();var b=0;$$("input.mico-mupload-uploaded-select-"+d+":checked").each(function(e){a[e.value]=this._uploadedValues[d][e.value];b=1}.bind(this));if(b){return a}return 0},setUploadedItemValue:function(c,b,a){if(!a||(a=="{}")){var d=this.checkUploadedFileInputSelected(c);b.setValue(d)}else{b.setValue(a)}},hideLinkChange:function(c,a){var e="options_"+c+"_file";var d=e+"_action";var h=a.up("div.options_"+c+"_file_name");var i=h.next("a");var b=h.next("div.input-box");var f=b.select('input[name="'+d+'"]')[0];var g=b.select('input[name="'+e+'"]')[0];f.value="save_new";g.disabled=false;b.toggle();i.setStyle({display:"none"});this.moveOldPreviewToNewList(c);this.showUploadedFileInput(c)},moveOldPreviewToNewList:function(b){var c=$("mico-mupload-uploaded-old-list-"+b);var a=$("mico-mupload-preview-"+b);a.update(c.innerHTML);c.update("")}};