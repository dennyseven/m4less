<table class="form">
    <tr>
        <td>
            <div class="question"><?php echo $text_question_data_import; ?></div>
            <div class="option">
            	<input id="DataTypeProductsOptionImport" type="radio" name="ExcelPort[Import][DataType]" value="Products" checked="checked" /><label for="DataTypeProductsOptionImport"><?php echo $text_datatype_option_products; ?></label>
            </div>
            <div class="question"><?php echo $text_question_language_import; ?></div>
            <?php foreach ($languages as $index => $language) : ?>
            <div class="option">
            	<input id="Language<?php echo $language['language_id']; ?>OptionImport" type="radio" name="ExcelPort[Import][Language]" value="<?php echo $language['language_id']; ?>"<?php echo $index == 0 ? ' checked="checked"' : ''; ?> /><label for="Language<?php echo $language['language_id']; ?>OptionImport"><?php echo $language['name']; ?></label>
            </div>
            <?php endforeach; ?>
            <div class="question"><?php echo $text_question_file_import; ?></div>
            <div class="option">
            	<input type="file" name="ExcelPort[Import][File]" />
            </div>
            <div class="question"><input id="checkboxDelete" type="checkbox" name="ExcelPort[Import][Delete]" value="1" /><?php echo $text_question_delete_other; ?></div>
        </td>  
    </tr>
    <tr>
        <td>
        	<div>
        		<a data-action="import" class="continueAction ExcelPortSubmitButton"><?php echo $button_import; ?></a>
            </div>
			<div class="help"><strong><?php echo $text_note; ?></strong> <?php echo $text_supported_in_oc1541; ?> <a class='needMoreSize' href="javascript:void(0)"><?php echo $text_learn_to_increase; ?></a></div>
        </td>  
    </tr>
</table>