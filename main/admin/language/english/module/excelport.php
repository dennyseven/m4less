<?php
// Heading
$_['heading_title']						= 'ExcelPort 1.1.2';

// Text
$_['text_module']         				= 'Modules';
$_['text_success']						= 'Success: You have modified module ExcelPort!';
$_['text_activate']						= 'Activate';
$_['text_not_activated']				= 'ExcelPort is not activated.';
$_['text_click_activate']				= 'Activate ExcelPort';
$_['text_success_activation']			= 'ACTIVATED: You have successfully activated ExcelPort!';
$_['text_content_top']					= 'Content Top';
$_['text_content_bottom']				= 'Content Bottom';
$_['text_column_left']					= 'Column Left';
$_['text_column_right']					= 'Column Right';
$_['text_datatype_option_products']		= 'Products';
$_['text_question_data']				= 'What data do you wish to export?';
$_['text_question_store']				= 'Which store do you wish to export?';
$_['text_question_language']			= 'Which language do you wish to export?';
$_['text_note']							= 'Note:';
$_['text_supported_in_oc1541']			= 'This feature is available only for OpenCart 1.5.4.x and 1.5.5.x. Please mind that your server can have a low memory limit.';
$_['text_learn_to_increase']			= 'Learn how to increase it.';
$_['text_feature_unsupported']			= 'This feature is supported only for OpenCart version {VERSION}';
$_['text_question_data_import']			= 'What data do you wish to import?';
$_['text_question_store_import']		= 'In which store do you wish to import?';
$_['text_question_language_import']		= 'Which language do you wish to import?';
$_['text_question_file_import']			= 'Please select the .xlsx or .zip file you wish to import:';
$_['text_file_generating']				= 'Generating file. Please wait...';
$_['text_file_downloading']				= 'Downloading file...';
$_['text_import_done']					= 'Import finished. {COUNT} products were imported.';
$_['text_preparing_data']				= 'Preparing data...';
$_['text_export_product_number']		= 'Number of products per exported part<span class="help">Set this to a lower value if you experience memory issues. The lower the vlaue, the more exported files you will receive.</span>';
$_['text_import_limit']					= 'Maximum products to read on each step of the import.<span class="help">Default value is 100. Decrease it if you experience memory issues on Import.</span>';
$_['text_question_delete_other']		= 'Delete the products that are not listed in the imported file?';
$_['text_confirm_delete_other']			= 'This will delete all your products before importing. It is advised to back up your products before the import. If you are sure you wish to continue, click OK.';

// Entry
$_['entry_code']						= 'ExcelPort status:<br /><span class="help">Enable or disable ExcelPort</span>';
$_['entry_layouts_active']				= 'Activated on:<br /><span class="help">Choose on which pages ExcelPort to be active</span>';

// Error
$_['error_permission']					= 'Warning: You do not have permission to modify module ExcelPort!';
$_['error_no_file']						= 'File was not uploaded.';

// Button
$_['button_export']						= 'Export Now';
$_['button_import']						= 'Import Data';

$_['excelport_unable_cache']			= 'Could not set cache storage method.';
$_['excelport_unable_upload']			= 'Temp file was not moved to the target folder.';
$_['excelport_invalid_file']			= 'File is invalid - it is either too large, or in a wrong format.';
$_['excelport_folder_not_string']		= 'The passed variable is not a string.';
$_['excelport_file_not_exists']			= 'The file you wish to import does not exist on the server.';
$_['excelport_product_number_invalid'] 	= 'Ivalid product number per file. Please set it between 50 and 800.';
$_['excelport_invalid_import_file']		= 'The imported file does not exist in the file system!';
$_['excelport_unable_zip_file_open']	= 'Cannot open zip file. It is probably corrupt.';
$_['excelport_unable_zip_file_extract'] = 'Cannot extract the zip file.';
$_['excelport_unable_create_unzip_folder'] = 'Cannot create the unzip folder.';
$_['excelport_import_limit_invalid']	= 'Ivalid product import limit. Please set it between 10 and 800.';

$_['import_success']					= 'SUCCESS: The products have been imported.';
?>