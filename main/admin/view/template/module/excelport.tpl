<?php echo $header; ?>
<div id="content" class="ExcelPortContent">
    <!-- START BREADCRUMB -->
    <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/breadcrumb.php'); ?>
    <!-- END BREADCRUMB -->
    <!-- START FLASHMESSAGE -->
    <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/flashmessage.php'); ?>
    <!-- END FLASHMESSAGE -->
    <div class="box">
        <div class="heading">
        	<h1>
            	<img src="view/image/imodules.png" style="margin-top: -3px;" alt="" />
                <span class="ExcelPortsTitle"><?php echo $heading_title; ?></span>
                <?php 
                	$dirname = IMODULE_ADMIN_ROOT.'view/template/module/excelport/';
                    
                	$tab_files = scandir($dirname); 
                	$tabs = array();
                	foreach ($tab_files as $key => $file) {
                		if (strpos($file,'tab_') !== false) {
                			$tabs[] = array(
                            	'file' => $dirname.$file,
                				'name' => ucwords(str_replace('.php','',str_replace('_',' ',str_replace('tab_','',$file))))
                			);
               			} 
                	}
               		foreach ($tabs as $key => $tab) {
                		if ($tab['name'] == 'Support' && $key < count($tabs) - 1) {
                			$temp = $tabs[count($tabs) - 1];
                			$tabs[count($tabs) - 1] = $tab;
                			$tabs[$key] = $temp;
                			break;
                		}
                	}
                ?>
        		<ul class="ExcelPortAdminSuperMenu">
          		<?php foreach($tabs as $tab): ?>
            		<li><?php echo $tab['name']; ?></li>
          		<?php endforeach; ?>
        		</ul>
        	</h1>
			<div class="buttons">
            	<a onclick="$('#form').submit();" class="button submitButton ExcelPortSubmitButton"><?php echo $button_save; ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
            </div>
		</div>
        <!-- START NOT ACTIVATED CHECK -->
        <?php require_once(IMODULE_ADMIN_ROOT.'view/template/module/excelport/notactivated.php'); ?>
        <!-- END NOT ACTIVATED CHECK -->
		<div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <ul class="ExcelPortAdminSuperWrappers">
                    <?php foreach($tabs as $tab): ?>
                    <li><?php require_once($tab['file']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <input type="hidden" class="selectedTab" name="selectedTab" value="<?php echo (empty($_GET['tab'])) ? 0 : $_GET['tab'] ?>" />
                <input type="hidden" name="ExcelPort[Activated]" value="true"/>
            </form>
		</div>
	</div>
</div>
<script type="text/javascript">
var updateTimeout = null;
var loopXHR = null;
var site_url = null;
var lastMemory = 0;
var unidentifiedError = false;

switch (location.protocol) {
	case 'https:': 
		site_url = '<?php echo dirname(HTTPS_SERVER); ?>';
		break;
	default:
		site_url = '<?php echo dirname(HTTP_SERVER); ?>';
		break;
}

$(document).ready(function() {
	var selectedTab = $('.selectedTab').val();
	var downloaded = false;
	var importing = false;
	var ajaxgenerate = <?php echo (empty($this->session->data['ajaxgenerate'])) ? 'false' : $this->session->data['ajaxgenerate']; unset($this->session->data['ajaxgenerate']); ?>;
	var ajaximport = <?php echo (empty($this->session->data['ajaximport']) || $hadError) ? 'false' : $this->session->data['ajaximport']; unset($this->session->data['ajaximport']); ?>;
	var token = '';
	var vars = window.location.search.split('&');
	for (var i = 0; i < vars.length; i++) {
		var parts = vars[i].split('=');
		if (parts[0] == 'token') token = parts[1];	
	}
	var timer = null;
	var seconds;
	
	var zeroPad = function (num, places) {
	  var zero = places - num.toString().length + 1;
	  return Array(+(zero > 0 && zero)).join("0") + num;
	}
	
	var progress = function(message, isError) {
		if (isError !== false) {
			$('#progressbar').progressbar({value: message.percent, disabled:false});
			if ((message.current === message.all && !importing) || message.finishedImport) {
				$('.finishActionButton').html('Finish');
				$('.finishActionButton').removeAttr('disabled');
				clearInterval(timer);
				clearTimeout(updateTimeout);
				loopXHR.abort();
				if (!downloaded) {
					$('#progressinfo').html('<?php echo $text_file_downloading; ?>');
					document.location.href = "index.php?token=" + token + "&route=module/excelport/download";
					downloaded = true;
				}
				if (importing) {
					$('#progressinfo').html('<?php echo $text_import_done; ?>'.replace('{COUNT}', message.current));
				}
			} else if (importing) {
				if (message.current > 0) {
					var pps = Math.round((message.current)/seconds);
					$('#progressinfo').html('Importing. Please wait...<br />Reading from: ' + message.importingFile + '<br />Products per second: ' + pps + "<br />Imported: " + message.current);
				} else {
					$('#progressbar').progressbar({value: 100, disabled:false});
					$('#progressinfo').html('<?php echo $text_preparing_data; ?>');	
				}
			} else {
				if (message.current > 0) {
					if (message.percent != 100) {
						var pps = message.current/seconds;
						var allSecondsRemaining = Math.round((message.all - message.current)/pps);
						var hoursRemaining =  zeroPad(Math.floor(allSecondsRemaining/3600), 2);
						var minutesRemaining = zeroPad(Math.floor((allSecondsRemaining%3600)/60), 2);
						var secondsRemaining = zeroPad(Math.floor((allSecondsRemaining%60)), 2);
						$('#progressinfo').html("Progress: " + message.percent + "%<br />" + message.current + " products were " + (importing ? "imported" : "exported") + "...<br />" + Math.ceil(pps) + " products per second<br />" + "Estimated time left: " + hoursRemaining + ':' + minutesRemaining + ':' + secondsRemaining);
					} else {
						$('#progressinfo').html('<?php echo $text_file_generating; ?>');	
					}
				} else {
					$('#progressinfo').html('<?php echo $text_preparing_data; ?>');		
				}
			}
		} else {
			$('.finishActionButton').html('Finish');
			$('.finishActionButton').removeAttr('disabled');
			$('#progressbar').progressbar({ disabled: true, value: 0 });
			$('#progressinfo').html(message);
			clearInterval(timer);
			clearTimeout(updateTimeout);
		}
	}
	
	var countSeconds = function() {
		seconds++;
	}
	
	var updateProgressBar = function(site_root, countinueChecking) {
		countinueChecking = typeof countinueChecking == 'undefined' ? true : countinueChecking;
		
		loopXHR = $.ajax({
			url: site_root+'/temp/excelport_progress.pro',
			type: 'GET',
			timeout: null,
			dataType: 'json',
			cache: false,
			success: function(returnData, textStatus, jqXHR) {
				if ($( "#progress-dialog" ).dialog('isOpen')) {
					if (returnData != null && returnData.error == false) {
						if (lastMemory == returnData.memory_get_usage && unidentifiedError) {
							var megabytes = Math.round(parseInt(returnData.memory_get_usage)/1048576);
							var errorMessage = 'Error: The server may be out of memory. Currently, the script is using ' + megabytes + ' MB';
							progress(errorMessage, false);
							return;
						} else {
							lastMemory = returnData.memory_get_usage;
						}
						progress(returnData, true);
						
						if (!importing) document.title = returnData.percent + '% ' + pageTitle;
						
						if ((returnData != null && returnData.current !== returnData.all && !importing) || (!returnData.finishedImport && importing)) {
							if (!countinueChecking) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);
						}
					} else {
						if (returnData != null) {
							progress(returnData.message, false);
							if (!countinueChecking || (returnData.current == returnData.all && !importing)) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);	
						} else {
							if (!countinueChecking) {
								return;
							}
							
							updateTimeout = setTimeout(function (){
								
								updateProgressBar(site_root);
							}, 1000);
						}
					}
				} else {
					
					clearTimeout(updateTimeout);
				}
			},
			error: function() {
				if (!countinueChecking) {
					
					return;
				}
				
				updateTimeout = setTimeout(function (){
					
					updateProgressBar(site_root);
				}, 1000);
			}
		});
	}
	
	var startAjaxGenerate = function(path, data) {
		downloaded = false;
		importing = false;
		unidentifiedError = false;
		if (!$( "#progress-dialog" ).dialog('isOpen')) {
			$( "#progress-dialog" ).dialog( "open" );
			$('.loadingImage').show();	
			$('.finishActionButton').show();
		}
		if (timer == null) {
			seconds = 1;
			timer = setInterval(countSeconds, 1000);
		}
		
		xhr = $.ajax({
			url: path,
			data: data,
			async: true,
			type: 'POST',
			timeout: null,
			dataType: 'json',
			cache: false,
			statusCode: {
				500: function(){
					progress('Server error 500 has occured.', false);
				}
			},
			success: function(successData) {
				if (successData == null) {
					unidentifiedError = true;
				} else {
					if (successData.current < successData.all && successData.done) {
						startAjaxGenerate(path, data);	
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				clearTimeout(updateTimeout);
				error = true;
				
				if (textStatus == 'timeout') {
					progress('A server timeout has occured.', false);
				} else if (textStatus == 'error') {
					console.log('A server error has occured.');	
				} else if (textStatus == 'parsererror') {
					progress(jqXHR.responseText.replace("<br />", ''), false);
				}
			}
		});
	}
	
	var startAjaxImport = function(path, data) {
		importing = true;
		downloaded = true;
		unidentifiedError = false;
		if (!$( "#progress-dialog" ).dialog('isOpen')) {
			$( "#progress-dialog" ).dialog( "open" );
			$('.loadingImage').show();	
			$('.finishActionButton').show();
		}
		if (timer == null) {
			seconds = 1;
			timer = setInterval(countSeconds, 1000);
		}
		
		xhr = $.ajax({
			url: path,
			data: data,
			async: true,
			type: 'POST',
			timeout: null,
			dataType: 'json',
			cache: false,
			statusCode: {
				500: function(){
					progress('Server error 500 has occured.', false);
				}
			},
			success: function(successData) {
				if (successData == null) {
					unidentifiedError = true;
				} else {
					if (successData.error) {
						progress(successData.message, false);
					} else {
						if (successData.done && !successData.finishedImport) {
							startAjaxImport(path, data);	
						}
					}
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				clearTimeout(updateTimeout);
				error = true;
				
				if (textStatus == 'timeout') {
					progress('A server timeout has occured.', false);
				} else if (textStatus == 'error') {
					console.log('A server error has occured.');	
				} else if (textStatus == 'parsererror') {
					progress(jqXHR.responseText.replace("<br />", ''), false);
				}
			}
		});
	}
	
	if (ajaxgenerate) {
		$('#generateLoading').show();
		startAjaxGenerate('index.php?token='+token+'&route=module/excelport/ajaxgenerate&_=' + (new Date()).getTime(), {
			ExcelPort : {
				Export : {
					DataType : $('input[name="ExcelPort[Export][DataType]"]').val(),
					Store : $('input[name="ExcelPort[Export][Store]"]').val(),
					Language : $('input[name="ExcelPort[Export][Language]"]').val()
				},
				Settings : {
					ExportProductNumber : $('input[name="ExcelPort[Settings][ExportProductNumber]"]').val()
				}
			}
		});
		updateProgressBar(site_url);
	}
	
	if (ajaximport) {
		$('#generateLoading').show();
		startAjaxImport('index.php?token='+token+'&route=module/excelport/ajaximport&_=' + Date.now(), {
			ExcelPort : {
				Import : {
					DataType : $('input[name="ExcelPort[Import][DataType]"]').val(),
					Language : $('input[name="ExcelPort[Import][Language]"]').val(),
					Delete : $('input[name="ExcelPort[Import][Delete]"]').val(),
				},
				Settings : {
					ImportLimit : $('input[name="ExcelPort[Settings][ImportLimit]"]').val()
				}
			}
		});
		updateProgressBar(site_url);
	}
	
	$('.ExcelPortAdminSuperMenu li').removeClass('selected').eq(selectedTab).addClass('selected');
	$('.ExcelPortAdminSuperWrappers > li').hide().eq(selectedTab).show();
	
	$('.ExcelPortAdminMenu li').click(function() {
		$('.ExcelPortAdminMenu li',$(this).parents('li')).removeClass('selected');
		$(this).addClass('selected');
		$($('.ExcelPortAdminWrappers li',$(this).parents('li')).hide().get($(this).index())).fadeIn(200);
	});
	
	$('.ExcelPortAdminSuperMenu li').click(function() {
		$('.ExcelPortAdminSuperMenu > li',$(this).parents('h1')).removeClass('selected');
		$(this).addClass('selected');
		$($('.ExcelPortAdminSuperWrappers > li',$(this).parents('#content')).hide().get($(this).index())).fadeIn(200);
		$('.selectedTab').val($(this).index());
	});
	
	$('.needMoreSize').click(function() {
		window.open('../vendors/excelport/help_increase_size.php', '_blank', 'location=no,width=830,height=580,resizable=no');
	});
	
	$('.ExcelPortSubmitButton').click(function(e) {
		var action = $(this).attr('data-action');
		if (action == 'import' && $('#checkboxDelete').is(':checked')) {
			if (!confirm('<?php echo $text_confirm_delete_other; ?>')) return;	
		}
		$('#form').attr('action',$('#form').attr('action').replace(/&submitAction=.*($|&)/g, ''));
		if (action != undefined && action != '') {
			$('#form').attr('action',$('#form').attr('action')+'&submitAction='+action);
		}
		$('#form').submit();
	});
});
</script>
<?php echo $footer; ?>
