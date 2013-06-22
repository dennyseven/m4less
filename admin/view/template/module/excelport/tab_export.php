<table class="form">
    <tr>
        <td>
            <div class="question"><?php echo $text_question_data; ?></div>
            <div class="option">
            	<input id="DataTypeProductsOption" type="radio" name="ExcelPort[Export][DataType]" value="Products" checked="checked" /><label for="DataTypeProductsOption"><?php echo $text_datatype_option_products; ?></label>
            </div>
        
            <div class="question"><?php echo $text_question_store; ?></div>
            <div class="option">
            	<input id="Store0Option" type="radio" name="ExcelPort[Export][Store]" value="0" checked="checked" /><label for="Store0Option"><?php echo $this->config->get('config_name') . $this->language->get('text_default'); ?></label>
            </div>
            <?php foreach ($stores as $store) : ?>
            <div class="option">
            	<input id="Store<?php echo $store['store_id']; ?>Option" type="radio" name="ExcelPort[Export][Store]" value="<?php echo $store['store_id']; ?>" /><label for="Store<?php echo $store['store_id']; ?>Option"><?php echo $store['name']; ?></label>
            </div>
            <?php endforeach; ?>
       
            <div class="question"><?php echo $text_question_language; ?></div>
            <?php foreach ($languages as $index => $language) : ?>
            <div class="option">
            	<input id="Language<?php echo $language['language_id']; ?>Option" type="radio" name="ExcelPort[Export][Language]" value="<?php echo $language['language_id']; ?>"<?php echo $index == 0 ? ' checked="checked"' : ''; ?> /><label for="Language<?php echo $language['language_id']; ?>Option"><?php echo $language['name']; ?></label>
            </div>
            <?php endforeach; ?>
        </td>  
    </tr>
    <tr>
        <td>
        	<div>
        		<a data-action="export" class="continueAction ExcelPortSubmitButton"><?php echo $button_export; ?></a>
            </div>
			<div class="help"><strong><?php echo $text_note; ?></strong> <?php echo $text_supported_in_oc1541; ?> <a class='needMoreSize' href="javascript:void(0)"><?php echo $text_learn_to_increase; ?></a></div>
        </td>  
    </tr>
</table>

<div id="progress-dialog">
	<!--<img src="./view/image/ExcelPort/ajax-loader.gif" id="emptytablesLoading" class="loadingImage" style="display: none;"/>-->
	<div id="progressbar"></div>
    <div id="progressinfo"></div>
    <button class="finishActionButton" disabled="disabled" style="display: none;"><img src="./view/image/ExcelPort/ajax-loader-2.gif" class="loadingImage2"/></button>
</div>

<script type="text/javascript">
var xhr;
var pageTitle = $('title').html();
$('.finishActionButton').click(function() {
	//xhr.abort();
	loopXHR.abort();
	clearTimeout(updateTimeout);
	$(this).html('<img src="./view/image/ExcelPort/ajax-loader-2.gif" class="loadingImage2"/>');
	$(this).attr('disabled', 'disabled');
	$( "#progress-dialog" ).dialog('close');
	$( "#progress-dialog" ).dialog('destroy');
	$('#progressbar').progressbar('destroy');
    $('#progressinfo').empty();
	$('.finishActionButton').hide();
	$('title').html(pageTitle);
	initDialog();
});

var initDialog = function () {
	$( "#progress-dialog" ).dialog({
		autoOpen: false,
		width: 680,
		show: "fade",
		modal: true,
		resizable: false,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar").hide(); }
	});
};

initDialog();
</script>