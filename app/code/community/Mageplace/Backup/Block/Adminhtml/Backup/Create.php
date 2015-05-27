<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Block_Adminhtml_Backup_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
	const PROGRESS_AREA_NAME	= 'progressarea';
	const START_BUTTON_ID		= 'mpbackupstartbutton';
	const BACK_BUTTON_ID		= 'mpbackupbackbutton';
	const FORM_ID				= 'mpbackupcreateform';
	
	protected $_objectId	= 'backup_id';
	protected $_blockGroup	= 'mpbackup';
	protected $_controller	= 'adminhtml_backup';
	protected $_mode		= 'create';
	
	
	/**
	 * Constructor for the category edit form
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_removeButton('reset');
		
		if(!$backupId = Mage::registry('mpbackup_backup')->getId()) {
			$this->_removeButton('delete');
			$this->_removeButton('save');
			$this->_addButton('start',
				array(
					'label'		=> $this->__('Backup Now!'), 
					'onclick'	=> $this->getStartJSFunction(), 
					'class'		=> 'save',
					'id'		=> $this->getStartButtonId()
				),
				-100
			);
		} else {
			$this->_updateButton('delete', 'label', $this->__('Delete record and files'));

			$this->_addButton('deleteRecord',
				array(
					'label' => $this->__('Delete record'),
					'onclick' => 'setLocation(\'' . $this->getUrl('*/*/deleteRecord', array('backup_id'=>$backupId)) . '\')',
					'class' => 'delete',
				)
			);
		}
		
		$this->_updateButton('back', 'id', $this->getBackButtonId());
		
		$this->_updateButton('back', 'after_html', $this->getBackButtonAfterHtml());
		
		$this->_formScripts[] = $this->_getJsScript();
	}
	
	public function getHeaderText()
	{
		return $this->__('Create Backup');
	}

	public function getHeaderCssClass()
	{
		return '';
	}
	
	public function getStartJSFunction()
	{		
		/*if ($this->getProfile()->getProfile_multiprocess_enable()){
			return 'mpbackup.startMultiBackup()';
		}*/
		return 'mpbackup.startBackup()';
	}

	public function getFormId()
	{
		return self::FORM_ID;
	}
	
	public function getStartButtonId()
	{
		return self::START_BUTTON_ID;
	}
	
	public function getBackButtonId()
	{
		return self::BACK_BUTTON_ID;
	}
	
	public function getProgressAreaName()
	{
		return self::PROGRESS_AREA_NAME;
	}

	public function getStartBackupUrl($ajax = false)
	{
		return $this->getUrl('*/*/start', array('ajax'=>$ajax));
	}

	public function getProcessBackupUrl($ajax = false)
	{
		return $this->getUrl('*/*/backup', array('ajax'=>$ajax));
	}

	public function getFinishBackupProcessUrl($ajax = false)
	{
		return $this->getUrl('*/*/finishBackup', array('ajax'=>$ajax));
	}

	public function getStageBackupUrl($ajax = true)
	{
		if (!Mage::getModel('core/url')->getSecure() && !Mage::app()->getStore()->isCurrentlySecure()) {			
			return Mage::getUrl('mpbackup/backup/stage', array('ajax' => $ajax));
		} else {			
			return Mage::getUrl('mpbackup/backup/stage', array('ajax' => $ajax, '_secure' => 1));
		}		
	}

	public function getStartFrontendBackupProcessUrl($ajax = false)
	{
		if (!Mage::getModel('core/url')->getSecure() && !Mage::app()->getStore()->isCurrentlySecure()) {			
			return $this->getUrl('mpbackup/backup/start', array('ajax'=>$ajax));
		} else {
			return $this->getUrl('mpbackup/backup/start', array('ajax'=>$ajax, '_secure' => 1));
		}
	}
	
	public function getMultiStartFrontendBackupProcessUrl($ajax = false)
	{
		if (!Mage::getModel('core/url')->getSecure() && !Mage::app()->getStore()->isCurrentlySecure()) {			
			return $this->getUrl('mpbackup/backup/multistart', array('ajax'=>$ajax));
		} else {
			return $this->getUrl('mpbackup/backup/multistart', array('ajax'=>$ajax, '_secure' => 1));
		}
	}
	
	public function getProfile()
	{
		return Mage::registry('mpbackup_profile');
	}
	
	public function getLogLevel()
	{
		$profile = $this->getProfile();
		if($profile && $profile->getProfileLogLevel()) {
			$logLevel = $profile->getProfileLogLevel();
		} else {
			$logLevel = 'ALL';
		}			

		return $logLevel;
	}
	
	public function isLogDisable()
	{
		return ($this->getLogLevel() == 'OFF');
	}
	
	public function getBackButtonAfterHtml()
	{
		ob_start();
?>
<style>
.backup-loader {width:300px !important; margin-left:-200px !important; padding: 15px 15px !important;}
.warning-loader {color:red; font-size: 1.2em;}
</style>
<?php 
		$html = ob_get_clean();
		
		return $html;
	}
	
	protected function _getJsScript()
	{
		ob_start();
?>
<script>
editForm = new varienForm('<?php echo $this->getFormId(); ?>', '');
var mpbackup = function() {
	return {
		requestPeriod: 5000,
		startedRequests: 0,
		backupFinished: false,
		stagesFinished: false,
		backupID: 0,		
		logFile: '',
		backupErrors: [],
		
		startBackup: function() {
			if(mpbackup.backupFinished == true) {
				return true;
			}
			
			$("loading_mask_loader").addClassName("backup-loader");
			var loading_mask_loader_html = document.getElementById("loading_mask_loader").innerHTML;
			document.getElementById("loading_mask_loader").innerHTML = loading_mask_loader_html
				+ '<br />'
				+ '<br />'
				+ "<p class='warning-loader'>"
				+ "<?php echo str_replace('"', '\\"', Mage::helper('mpbackup')->__('WARNING!')); ?>"
				+ "&nbsp;"
				+ "<?php echo str_replace('"', '\\"', Mage::helper('mpbackup')->__('Do not reload or close the page during backup process.')); ?>"
				+ "</p>";
			
			if(!document.getElementById('backup_name').value) {
				document.getElementById('backup_name').value = "<?php echo $this->__('Backup - %s', Mage::app()->getLocale()->storeDate(null, null, true)); ?>";
			}

			new Ajax.Request('<?php echo $this->getStartBackupUrl(true); ?>', {
				parameters: $('<?php echo $this->getFormId(); ?>').serialize(),
				evalScripts: true,

				onLoading: function(transport) {
					mpbackup.backupFinished = false;
					$("<?php echo $this->getProgressAreaName(); ?>").update('');

					mpbackup.setLoader(1);

					Ajax.activeRequestCount++; /* Don't remove this row */

					mpbackup.startedRequests++;
				},

				onComplete: function(transport) {
					if(transport.responseText == "") {
//						$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">Backup model error</b>'+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
						mpbackup.backupErrors[mpbackup.backupErrors.length] = "Backup model error";
						mpbackup.setLoader(0);
						return; 
					} else {
						try {
							var backup = transport.responseText.evalJSON();
							mpbackup.backupID = new Number(backup.backupId).valueOf();
							mpbackup.logFile = new String(backup.logFile).valueOf();
						} catch(e) {
							alert(e.getMessage());
						}
					}
					
					if(!mpbackup.backupID) {
//						$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">Backup ID error</b>'+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
						mpbackup.backupErrors[mpbackup.backupErrors.length] = "Backup ID error";
						mpbackup.setLoader(0);
						return; 
					}
					
					
					new Ajax.Request('<?php echo $this->getProcessBackupUrl(true); ?>'+'bid/'+mpbackup.backupID, {
						parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile},
						method: 'POST',
						evalScripts: true,

						onLoading: function(transport) {
							<?php if(!$this->isLogDisable()) : ?>
							new Ajax.Request('<?php echo $this->getStartFrontendBackupProcessUrl(true); ?>'+'bid/'+mpbackup.backupID, {
								parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile},
								
								onComplete: function(transport) {
									mpbackup.checkStage();
								}
							});
							<?php endif; ?>
						},
						
						onComplete: function(transport) {
							if(transport.status != 200) {
								if(transport.status == 500) {
									var errMessage = "<?php echo Mage::helper('mpbackup')->__("It's not enough time to run backup. To eliminate this error try to split the content (files and DB tables) of this backup profile into smaller parts (profiles which will include the half or less from the original profile content)."); ?>";									
								} else {
/*									var errMessage = transport.responseText;
									if(errMessage == '1') {
										if(typeof(transport.statusText) != 'undefined' && transport.statusText != '') {
											errMessage = transport.statusText;
										}
									}
*/
									mpbackup.backupFinished = true;

									if($('<?php echo $this->getStartButtonId(); ?>')) {
										$('<?php echo $this->getStartButtonId(); ?>').remove();
									}
									<?php if($this->isLogDisable()) : ?>
									mpbackup.setLoader(0);
									mpbackup.backupFinished = false;
									<?php endif; ?>
								}
								mpbackup.backupErrors[mpbackup.backupErrors.length] = errMessage;

								mpbackup.finishErrorBackup(errMessage);
								
							} else if(transport.responseText != "1") {
//								$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">'+transport.responseText+"</b>"+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
								mpbackup.backupErrors[mpbackup.backupErrors.length] = transport.responseText;
								
								mpbackup.finishErrorBackup(transport.responseText);
							} else {
								mpbackup.backupFinished = true;

								if($('<?php echo $this->getStartButtonId(); ?>')) {
									$('<?php echo $this->getStartButtonId(); ?>').remove();
								}
								<?php if($this->isLogDisable()) : ?>
								mpbackup.setLoader(0);
								mpbackup.backupFinished = false;
								<?php endif; ?>
							}
						}
					});
				}
			});


			return false;
		},
		
		finishErrorBackup: function(error) {
			new Ajax.Request('<?php echo $this->getFinishBackupProcessUrl(true); ?>', {
				parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile, 'error':error},
				method: 'POST',
				onComplete: function(transport) {
					mpbackup.backupFinished = true;

					if(transport.status != 200 || transport.responseText != "1") {
						mpbackup.backupErrors[mpbackup.backupErrors.length] = transport.responseText;						
					} else {
						if($('<?php echo $this->getStartButtonId(); ?>')) {
							$('<?php echo $this->getStartButtonId(); ?>').remove();
						}
						<?php if($this->isLogDisable()) : ?>
						mpbackup.setLoader(0);
						mpbackup.backupFinished = false;
						<?php endif; ?>
					}
				}
			});

			return false;
		},
		
		startMultiBackup: function() {
			if(mpbackup.backupFinished == true) {
				return true;
			}
			
			$("loading_mask_loader").addClassName("backup-loader");
			var loading_mask_loader_html = document.getElementById("loading_mask_loader").innerHTML;
			document.getElementById("loading_mask_loader").innerHTML = loading_mask_loader_html
				+ '<br />'
				+ '<br />'
				+ "<p class='warning-loader'>"
				+ "<?php echo str_replace('"', '\\"', Mage::helper('mpbackup')->__('WARNING!')); ?>"
				+ "&nbsp;"
				+ "<?php echo str_replace('"', '\\"', Mage::helper('mpbackup')->__('Do not reload or close the page during backup process.')); ?>"
				+ "</p>";
			
			if(!document.getElementById('backup_name').value) {
				document.getElementById('backup_name').value = "<?php echo $this->__('Backup - %s', Mage::app()->getLocale()->storeDate(null, null, true)); ?>";
			}

			new Ajax.Request('<?php echo $this->getStartBackupUrl(true); ?>', {
				parameters: $('<?php echo $this->getFormId(); ?>').serialize(),
				evalScripts: true,

				onLoading: function(transport) {
					mpbackup.backupFinished = false;
					$("<?php echo $this->getProgressAreaName(); ?>").update('');

					mpbackup.setLoader(1);

					Ajax.activeRequestCount++; /* Don't remove this row */

					mpbackup.startedRequests++;
				},

				onComplete: function(transport) {
					if(transport.responseText == "") {
//						$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">Backup model error</b>'+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
						mpbackup.backupErrors[mpbackup.backupErrors.length] = "Backup model error";
						mpbackup.setLoader(0);
						return; 
					} else {
						try {
							var backup = transport.responseText.evalJSON();
							mpbackup.backupID = new Number(backup.backupId).valueOf();
							mpbackup.logFile = new String(backup.logFile).valueOf();
						} catch(e) {
							alert(e.getMessage());
						}
					}
					
					if(!mpbackup.backupID) {
//						$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">Backup ID error</b>'+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
						mpbackup.backupErrors[mpbackup.backupErrors.length] = "Backup ID error";
						mpbackup.setLoader(0);
						return; 
					}
					
					var multiParams = new Array(0,0);
					var filename = '';
					mpbackup.backup(multiParams[0], multiParams[1], filename);					
				}
			});

			return false;
		},
		
		backup: function(startTable, startRow, filename, skipDb, startPoint, toCompress) {
			new Ajax.Request('<?php echo $this->getProcessBackupUrl(true); ?>'+'bid/'+mpbackup.backupID, {
				parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile, 'table':startTable, 'row':startRow, 'multiproc':true, 'filename':filename, 'skipDb': skipDb, 'startPoint': startPoint, 'toCompress': toCompress},
				evalScripts: true,	
				
				onLoading: function(transport) {
							<?php if(!$this->isLogDisable()) : ?>
							new Ajax.Request('<?php echo $this->getStartFrontendBackupProcessUrl(true); ?>', {
								parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile},
								
								onSuccess: function(transport) {
									mpbackup.checkStage();
								}
							});
							<?php endif; ?>
						},
														
				onComplete: function(transport) {
					mpbackup.backupFinished = true;
					var arr = (transport.responseText).split('|');
					if(arr.length == 3) {											
						mpbackup.backup(arr[0], arr[1], arr[2]);
					} else if(arr.length == 6) {
						mpbackup.backup(arr[0], arr[1], arr[2], arr[3], arr[4], arr[5]);
					} else if (transport.responseText == "1") {						
						if($('<?php echo $this->getStartButtonId(); ?>')) {
							$('<?php echo $this->getStartButtonId(); ?>').remove();
						}
						if($('<?php echo $this->getStartButtonId(); ?>')) {
							$('<?php echo $this->getStartButtonId(); ?>').remove();
						}
						//mpbackup.backupFinished = false;
						new Ajax.Request('<?php echo $this->getMultiStartFrontendBackupProcessUrl(true); ?>'+'bid/'+mpbackup.backupID, {
								parameters: {'backup_id':mpbackup.backupID, 'log_file':mpbackup.logFile},
								
								onSuccess: function(transport) {
									mpbackup.checkStage();
									mpbackup.setLoader(0);										
								}
							});					
						
					} else {
//						$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">'+transport.responseText+"</b>"+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
						mpbackup.backupErrors[mpbackup.backupErrors.length] = transport.responseText;
					}
						
				}
			});
		},

		disableButtons: function() {
			$('<?php echo $this->getStartButtonId(); ?>').disabled = !($('<?php echo $this->getStartButtonId(); ?>').disabled);
			$('<?php echo $this->getBackButtonId(); ?>').disabled = !($('<?php echo $this->getBackButtonId(); ?>').disabled);
		},

		checkStage: function() {
			new Ajax.Request('<?php echo $this->getStageBackupUrl(true); ?>'+'bid/'+mpbackup.backupID, {
				parameters: {'backup_id':mpbackup.backupID, 'finish':mpbackup.backupFinished, 'log_file':mpbackup.logFile},

				onComplete: function(transport) {
					mpbackup.startedRequests--;
					
					var row = '';
					if (transport.responseText != "") {
						if(transport.responseText.indexOf("!!!FINISH!!!") < 0) {
							var responseText = transport.responseText;
						} else {
							var responseText = transport.responseText.replace("!!!FINISH!!!", '');
							mpbackup.stagesFinished = true;
						}
						
						$("<?php echo $this->getProgressAreaName(); ?>").update(
							responseText + $("<?php echo $this->getProgressAreaName(); ?>").innerHTML
						);
					}
					
					if(!mpbackup.stagesFinished) {
						mpbackup.startedRequests++;
						setTimeout('mpbackup.checkStage()', mpbackup.requestPeriod);
					} 

					if(mpbackup.stagesFinished && (mpbackup.startedRequests == 0)) {
						if($('<?php echo $this->getStartButtonId(); ?>')) {
							$('<?php echo $this->getStartButtonId(); ?>').remove();
						}
						if($('<?php echo $this->getStartButtonId(); ?>')) {
							$('<?php echo $this->getStartButtonId(); ?>').remove();
						}
						mpbackup.setLoader(0);
						mpbackup.backupFinished = false;
					}
				}
			});
		},


		setLoader: function(show) {
			if(show) {
				loaderArea = $$('#html-body .wrapper')[0]; // Blocks all page
				if(loaderArea){
					Element.clonePosition($('loading-mask'), $(loaderArea), {offsetLeft:-2})
					toggleSelectsUnderBlock($('loading-mask'), false);
					Element.show('loading-mask');
					setLoaderPosition();
				}
			} else {
				toggleSelectsUnderBlock($('loading-mask'), true);
				Element.hide('loading-mask');
				if(mpbackup.backupErrors.length) {
					$("<?php echo $this->getProgressAreaName(); ?>").update('<b style="color:red">'+mpbackup.backupErrors.join("<br />")+'</b>'+"<br />"+$("<?php echo $this->getProgressAreaName(); ?>").innerHTML);
					mpbackup.backupErrors = new Array();
				}
			}
		},
		
		changeProfile: function() {
			var profileId = new Number($('profile_id').getValue()).valueOf();
			if(profileId<1) {
				return;
			}
			mpbackup.setLoader(1);
			var href = '<?php echo $this->getUrl('*/*/create', array('profile_id'=>'0')); ?>';
			location.href = href.replace('profile_id/0', 'profile_id/'+profileId);
		}
	}
}();
</script>
<?php 
		//TODO: Remove script tags after development will be complited 
		$js = str_replace(array('<script>', '</script>'), '', ob_get_clean());
		
		return $js;
	}
}