<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>  
  <?php if ($error_already_exists) { ?>
  <div class="warning"><?php echo $error_already_exists; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
<div class="box">
    <div class="left"></div>
    <div class="right"></div>
    <div class="heading">
        <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
        <div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">

    <div class="helper-msg">
        <?php echo $custom_url_help; ?>
    </div>

    <div id="tabs" class="htabs"><a href="#tab_general"><?php echo $tab_general; ?></a><a href="#tab_products"><?php echo $tab_products; ?></a><a href="#tab_categories"><?php echo $tab_categories; ?></a><a href="#tab_manufacturers"><?php echo $tab_manufacturers; ?></a><a href="#tab_information_pages"><?php echo $tab_information_pages; ?></a></div>
    
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_general">
        <div id="tab_general">
            <div class="buttons button_tabs" style="float:left;">
                <a href="<?php echo $autofill; ?>" class="button">
                    <span><?php echo $button_autofill; ?></span>
                </a>
            </div>
            <div class="buttons button_tabs"><a onclick="$('#form_general').submit();" class="button"><span><?php echo $button_save_general; ?></span></a></div>
            <?php foreach ($languages as $language) { ?>
                <div class="buttons button_tabs">
                    <a value="custom_url_store_language<?php echo $language['language_id']; ?>" class="button custom_url_store_language_button" onclick="showLang('custom_url_store_language<?php echo $language['language_id']; ?>','custom_url_store_language');"><span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><?php echo $language['name']; ?></span></a>
                </div>
            <?php } ?>
            <div class="clear"></div>
            <table id="custom_url_store" class="list">
                <thead>
                    <tr>
                        <td class="left"><?php echo $column_url; ?></td>
                        <td class="left"><?php echo $column_keyword; ?></td>
                        <td class="center brt"><?php echo $column_title; ?><a class="help_icon" title="<?php echo $title_help ?>"></a></td>
                        <td class="center brt"><?php echo $column_meta_keyword; ?><a class="help_icon" title="<?php echo $keywords_help ?>"></a></td>
                        <td class="center"><?php echo $column_meta_description; ?><a class="help_icon" title="<?php echo $description_help ?>"></a></td>
                        <td align="center"><?php echo 'Action'; ?></td>
                    </tr>
                </thead>    
                <?php $custom_url_store_row = 0; ?>
                <?php foreach($custom_url_store_data as $url_alias_id => $value) { ?>   
                    <tbody id="custom_url_store_row<?php echo $custom_url_store_row; ?>">
                        <tr>
                            <?php foreach($value as $row => $custom_url_store_top){ ?>
                                <?php if(isset($row) && $row == 'keyword_query') { ?>
                                    <td><?php echo $domain; ?>index.php?route=<input class="blueprint" type="text" size="40" name="custom_url_store[<?php echo $custom_url_store_row; ?>][id][query]" value="<?php echo substr($custom_url_store_top['query'],6); ?>" /></td>
                                    <td><?php echo $domain; ?><input type="text" size="15" name="custom_url_store[<?php echo $custom_url_store_row; ?>][id][keyword]" value="<?php echo $custom_url_store_top['keyword']; ?>" /></td>
                                <?php } ?>
                                <?php if(isset($row) && $row == 'custom_url_store_description') { ?>
                                    <td class="brt center">
                                        <?php foreach ($languages as $language) { ?>
                                            <textarea class="custom_url_store_language<?php echo $language['language_id']; ?>" name="custom_url_store[<?php echo $custom_url_store_row; ?>][custom_url_store_description][<?php echo $language['language_id']; ?>][name]" cols="20" rows="2"><?php echo isset($value[$row][$language['language_id']]) ? $value[$row][$language['language_id']]['name'] : ''; ?></textarea>
                                            <?php if (isset(${'error_name_custom_url_store_'.$custom_url_store_row.'_'.$language['language_id']})) { ?>
                                                <span class="custom_url_store_language<?php echo $language_id; ?> error"><?php echo ${'error_name_custom_url_store_'.$custom_url_store_row.'_'.$language['language_id']}; ?></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td class="brt center">
                                        <?php foreach ($languages as $language) { ?>
                                            <textarea class="custom_url_store_language<?php echo $language['language_id']; ?>" name="custom_url_store[<?php echo $custom_url_store_row; ?>][custom_url_store_description][<?php echo $language['language_id']; ?>][meta_keywords]" cols="20" rows="5"><?php echo isset($value[$row][$language['language_id']]) ? $value[$row][$language['language_id']]['meta_keywords'] : ''; ?></textarea>
                                        <?php } ?>
                                    </td>
                                    <td class="center">
                                        <?php foreach ($languages as $language) { ?>
                                            <textarea class="custom_url_store_language<?php echo $language['language_id']; ?>" name="custom_url_store[<?php echo $custom_url_store_row; ?>][custom_url_store_description][<?php echo $language['language_id']; ?>][meta_description]" cols="20" rows="5"><?php echo isset($value[$row][$language['language_id']]) ? $value[$row][$language['language_id']]['meta_description'] : ''; ?></textarea>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            <?php } ?>
                            <td align="center"><a style="text-decoration: none;" onclick="$('#custom_url_store_row<?php echo $custom_url_store_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
                        </tr>
                    </tbody>
                    <?php $custom_url_store_row++; ?>
                <?php } ?>
                <tfoot>
                    <tr>
                        <td width="90%" colspan="5">&nbsp;</td>
                        <td width="10%" align="center"><a style="text-decoration: none;" onclick="addcustom_url_store();" class="button"><span><?php echo $button_add_custom_url_store; ?></span></a></td>
                    </tr>
                </tfoot>            
            </table>
            <!-- <div class="pagination"><?php //echo $pagination_general; ?></div> -->
            <textarea class="hidden" name="tab" value="tab_general">tab_general</textarea>
        </div>
    </form>

    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_products">
        <div id="tab_products">
            <div class="buttons button_tabs" style="float:left;">
                <?php echo $entry_keyword; ?><input type="text" value="<?php echo $filter_keyword; ?>" name="filter_keyword" />
                <a onclick="filter();" class="button">
                    <span><?php echo $button_filter; ?></span>
                </a>
                <a onclick="reset_filter();" class="button">
                    <span><?php echo $button_reset; ?></span>
                </a>
            </div>
            <div class="buttons button_tabs"><a onclick="$('#form_products').submit();" class="button"><span><?php echo $button_save_products; ?></span></a></div>
            <?php foreach ($languages as $language) { ?>
                <div class="buttons button_tabs">
                    <a value="product_language<?php echo $language['language_id']; ?>" class="button product_language_button" onclick="showLang('product_language<?php echo $language['language_id']; ?>','product_language');"><span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><?php echo $language['name']; ?></span></a>
                </div>
            <?php } ?>
            <div class="pagination"><?php echo $pagination_product; ?></div>
            <div class="clear" style="margin-top: 25px;"></div>
            <table class="list">
                <thead>
                    <tr>
                        <td class="center"><?php echo $column_image; ?></td>
                        <td class="left"><?php echo $column_name; ?></td>
                        <td class="left"><?php echo $column_keyword; ?></td>
                        <td class="center brt"><?php echo $column_title; ?><a class="help_icon" title="<?php echo $title_help ?>"></a></td>
                        <td class="center brt"><?php echo $column_meta_keyword; ?><a class="help_icon" title="<?php echo $keywords_help ?>"></a></td>
                        <td class="center"><?php echo $column_meta_description; ?><a class="help_icon" title="<?php echo $description_help ?>"></a></td>
                    </tr>
                </thead>    
                    
                <tbody>
                    <?php if ($products) { ?>
                        <?php foreach ($products as $product) { ?>
                            <tr>
                                <td class="center"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
                                <td class="left"><?php echo $product['name']; ?></td>
                                <td class="left"><?php echo $domain; ?><input size="30"  type="text" name="product[product_id][<?php echo $product['product_id'] ?>]" value="<?php echo $product['keyword']; ?>" /></td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="product_language<?php echo $language['language_id']; ?>" name="product[product_description][<?php echo $product['product_id']; ?>][<?php echo $language['language_id']; ?>][title]" cols="20" rows="2"><?php echo isset($product['product_description'][$language['language_id']]) ? $product['product_description'][$language['language_id']]['title'] : ''; ?></textarea>
                                        <?php if (isset(${'error_name_product_'.$product['product_id'].'_'.$language['language_id']})) { ?>
                                            <span class="product_language<?php echo $language['language_id']; ?> error"><?php echo ${'error_name_product_'.$product['product_id'].'_'.$language['language_id']}; ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="product_language<?php echo $language['language_id']; ?>" name="product[product_description][<?php echo $product['product_id']; ?>][<?php echo $language['language_id']; ?>][meta_keywords]" cols="40" rows="5"><?php echo isset($product['product_description'][$language['language_id']]) ? $product['product_description'][$language['language_id']]['meta_keywords'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="product_language<?php echo $language['language_id']; ?>" name="product[product_description][<?php echo $product['product_id']; ?>][<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="5"><?php echo isset($product['product_description'][$language['language_id']]) ? $product['product_description'][$language['language_id']]['meta_description'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                            <tr>
                                <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
                            </tr>
                    <?php } ?>
                </tbody>    
            </table>
            <textarea class="hidden" name="tab" value="tab_products">tab_products</textarea>
        </div>
    </form>

    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_categories">
        <div id="tab_categories">
            <div class="buttons button_tabs"><a onclick="$('#form_categories').submit();" class="button"><span><?php echo $button_save_categories; ?></span></a></div>
            <?php foreach ($languages as $language) { ?>
                <div class="buttons button_tabs">
                    <a value="category_language<?php echo $language['language_id']; ?>" class="button category_language_button" onclick="showLang('category_language<?php echo $language['language_id']; ?>','category_language');"><span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><?php echo $language['name']; ?></span></a>
                </div>
            <?php } ?>
            <div class="clear"></div>
            <table class="list">
                <thead>
                    <tr>
                        <td class="left"><?php echo $column_name; ?></td>
                        <td class="left"><?php echo $column_keyword; ?></td>
                        <td class="center brt"><?php echo $column_title; ?><a class="help_icon" title="<?php echo $title_help ?>"></a></td>
                        <td class="center brt"><?php echo $column_meta_keyword; ?><a class="help_icon" title="<?php echo $keywords_help ?>"></a></td>
                        <td class="center"><?php echo $column_meta_description; ?><a class="help_icon" title="<?php echo $description_help ?>"></a></td>
                    </tr>
                </thead>    
                    
                <tbody>
                    <?php if ($categories) { ?>
                        <?php foreach ($categories as $category) { ?>
                            <tr>
                                <td class="left"><?php echo $category['name']; ?></td>
                                <td class="left"><?php echo $domain; ?><input size="30"  type="text" name="category[category_id][<?php echo $category['category_id'] ?>]" value="<?php echo $category['keyword']; ?>" /></td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="category_language<?php echo $language['language_id']; ?>" name="category[category_description][<?php echo $category['category_id']; ?>][<?php echo $language['language_id']; ?>][title]" cols="20" rows="2"><?php echo isset($category['category_description'][$language['language_id']]) ? $category['category_description'][$language['language_id']]['title'] : ''; ?></textarea>
                                        <?php if (isset(${'error_name_category_'.$category['category_id'].'_'.$language['language_id']})) { ?>
                                            <span class="category_language<?php echo $language['language_id']; ?> error"><?php echo ${'error_name_category_'.$category['category_id'].'_'.$language['language_id']}; ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="category_language<?php echo $language['language_id']; ?>" name="category[category_description][<?php echo $category['category_id']; ?>][<?php echo $language['language_id']; ?>][meta_keywords]" cols="40" rows="5"><?php echo isset($category['category_description'][$language['language_id']]) ? $category['category_description'][$language['language_id']]['meta_keywords'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="category_language<?php echo $language['language_id']; ?>" name="category[category_description][<?php echo $category['category_id']; ?>][<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="5"><?php echo isset($category['category_description'][$language['language_id']]) ? $category['category_description'][$language['language_id']]['meta_description'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                            <tr>
                                <td class="center" colspan="5"><?php echo $text_no_results; ?></td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- <div class="pagination"><?php //echo $pagination_category; ?></div> -->
            <textarea class="hidden" name="tab" value="tab_categories">tab_categories</textarea>
        </div>
    </form>

    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_manufacturers">  
        <div id="tab_manufacturers">
            <div class="buttons button_tabs"><a onclick="$('#form_manufacturers').submit();" class="button"><span><?php echo $button_save_manufacturers; ?></span></a></div>
            <?php foreach ($languages as $language) { ?>
                <div class="buttons button_tabs">
                    <a value="manufacturer_language<?php echo $language['language_id']; ?>" class="button manufacturer_language_button" onclick="showLang('manufacturer_language<?php echo $language['language_id']; ?>','manufacturer_language');"><span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><?php echo $language['name']; ?></span></a>
                </div>
            <?php } ?>
            <div class="clear"></div>
            <table class="list">
                <thead>
                    <tr>
                        <td class="left"><?php echo $column_name; ?></td>
                        <td class="left"><?php echo $column_keyword; ?></td>
                        <td class="center brt"><?php echo $column_title; ?><a class="help_icon" title="<?php echo $title_help ?>"></a></td>
                        <td class="center brt"><?php echo $column_meta_keyword; ?><a class="help_icon" title="<?php echo $keywords_help ?>"></a></td>
                        <td class="center"><?php echo $column_meta_description; ?><a class="help_icon" title="<?php echo $description_help ?>"></a></td>
                    </tr>
                </thead>    
                    
                <tbody>
                    <?php if ($manufacturers) { ?>
                        <?php foreach ($manufacturers as $manufacturer) { ?>
                            <tr>
                                <td class="left"><?php echo $manufacturer['name']; ?></td>
                                <td class="left"><?php echo $domain; ?><input size="30"  type="text" name="manufacturer[manufacturer_id][<?php echo $manufacturer['manufacturer_id'] ?>]" value="<?php echo $manufacturer['keyword']; ?>" /></td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="manufacturer_language<?php echo $language['language_id']; ?>" name="manufacturer[manufacturer_description][<?php echo $manufacturer['manufacturer_id']; ?>][<?php echo $language['language_id']; ?>][title]" cols="20" rows="2"><?php echo isset($manufacturer['manufacturer_description'][$language['language_id']]) ? $manufacturer['manufacturer_description'][$language['language_id']]['title'] : ''; ?></textarea>
                                        <?php if (isset(${'error_name_manufacturer_'.$manufacturer['manufacturer_id'].'_'.$language['language_id']})) { ?>
                                            <span class="manufacturer_language<?php echo $language['language_id']; ?> error"><?php echo ${'error_name_manufacturer_'.$manufacturer['manufacturer_id'].'_'.$language['language_id']}; ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="manufacturer_language<?php echo $language['language_id']; ?>" name="manufacturer[manufacturer_description][<?php echo $manufacturer['manufacturer_id']; ?>][<?php echo $language['language_id']; ?>][meta_keywords]" cols="40" rows="5"><?php echo isset($manufacturer['manufacturer_description'][$language['language_id']]) ? $manufacturer['manufacturer_description'][$language['language_id']]['meta_keywords'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="manufacturer_language<?php echo $language['language_id']; ?>" name="manufacturer[manufacturer_description][<?php echo $manufacturer['manufacturer_id']; ?>][<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="5"><?php echo isset($manufacturer['manufacturer_description'][$language['language_id']]) ? $manufacturer['manufacturer_description'][$language['language_id']]['meta_description'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                            <tr>
                                <td class="center" colspan="5"><?php echo $text_no_results; ?></td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- <div class="pagination"><?php //echo $pagination_manufacturer; ?></div> -->
            <textarea class="hidden" name="tab" value="tab_manufacturers">tab_manufacturers</textarea>
        </div>
    </form>

    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_information_pages">
        <div id="tab_information_pages">
            <div class="buttons button_tabs"><a onclick="$('#form_information_pages').submit();" class="button"><span><?php echo $button_save_information_pages; ?></span></a></div>
            <?php foreach ($languages as $language) { ?>
                <div class="buttons button_tabs">
                    <a value="information_language<?php echo $language['language_id']; ?>" class="button information_language_button" onclick="showLang('information_language<?php echo $language['language_id']; ?>','information_language');"><span><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><?php echo $language['name']; ?></span></a>
                </div>
            <?php } ?>
            <div class="clear"></div>
            <table class="list">
                <thead>
                    <tr>
                        <td class="left"><?php echo $column_name; ?></td>
                        <td class="left"><?php echo $column_keyword; ?></td>
                        <td class="center brt"><?php echo $column_title; ?><a class="help_icon" title="<?php echo $title_help ?>"></a></td>
                        <td class="center brt"><?php echo $column_meta_keyword; ?><a class="help_icon" title="<?php echo $keywords_help ?>"></a></td>
                        <td class="center"><?php echo $column_meta_description; ?><a class="help_icon" title="<?php echo $description_help ?>"></a></td>
                    </tr>
                </thead>    
                    
                <tbody>
                    <?php if ($informations) { ?>
                        <?php foreach ($informations as $information) { ?>
                            <tr>
                                <td class="left"><?php echo $information['name']; ?></td>
                                <td class="left"><?php echo $domain; ?><input size="30" type="text" name="information[information_id][<?php echo $information['information_id'] ?>]" value="<?php echo $information['keyword']; ?>" /></td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="information_language<?php echo $language['language_id']; ?>" name="information[information_description][<?php echo $information['information_id']; ?>][<?php echo $language['language_id']; ?>][title]" cols="20" rows="2"><?php echo isset($information['information_description'][$language['language_id']]) ? $information['information_description'][$language['language_id']]['title'] : ''; ?></textarea>
                                        <?php if (isset(${'error_name_information_'.$information['information_id'].'_'.$language['language_id']})) { ?>
                                            <span class="information_language<?php echo $language['language_id']; ?> error"><?php echo ${'error_name_information_'.$information['information_id'].'_'.$language['language_id']}; ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                <td class="brt center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea class="information_language<?php echo $language['language_id']; ?>" name="information[information_description][<?php echo $information['information_id']; ?>][<?php echo $language['language_id']; ?>][meta_keywords]" cols="40" rows="5"><?php echo isset($information['information_description'][$language['language_id']]) ? $information['information_description'][$language['language_id']]['meta_keywords'] : ''; ?></textarea>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php foreach ($languages as $language) { ?>
                                        <textarea name="information[information_description][<?php echo $information['information_id']; ?>][<?php echo $language['language_id']; ?>][meta_description]" cols="40" rows="5" class="information_language<?php echo $language['language_id']; ?>"><?php echo isset($information['information_description'][$language['language_id']]) ? $information['information_description'][$language['language_id']]['meta_description'] : ''; ?></textarea>                                      
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                            <tr>
                                <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- <div class="pagination"><?php //echo $pagination_information; ?></div> -->
            <textarea class="hidden" name="tab" value="tab_information_pages">tab_information_pages</textarea>
        </div>
    </form>
  </div>
</div>
<script type="text/javascript"><!--
var custom_url_store_row = <?php echo $custom_url_store_row; ?>;
var currency = "<?php echo $currency; ?>";
var edited = false;
function addcustom_url_store() {
    html  = '<tbody id="custom_url_store_row' + custom_url_store_row + '">';
    html += '<tr>'; 
    html += '<td class="left"><?php echo $domain; ?>index.php?route=<input type="text" size="18" name="custom_url_store['+custom_url_store_row+'][id][query]" value="" /></td>';
    html += '<td class="left"><?php echo $domain; ?><input type="text" size="15" name="custom_url_store['+custom_url_store_row+'][id][keyword]" value="" /></td>';
    html += '<td class="brt center">'
    <?php foreach ($languages as $language) { ?>
        var language_id = "<?php echo $language['language_id']; ?>";
        html += '<textarea class="custom_url_store_language'+language_id+'" name="custom_url_store['+custom_url_store_row+'][custom_url_store_description]['+language_id+'][name]" cols="20" rows="2"></textarea>';
    <?php } ?>
    html += '</td>';
    html += '<td class="brt center">';
    <?php foreach ($languages as $language) { ?>
        var language_id = "<?php echo $language['language_id']; ?>";
        html += '<textarea class="custom_url_store_language'+language_id+'" name="custom_url_store['+custom_url_store_row+'][custom_url_store_description]['+language_id+'][meta_keywords]" cols="20" rows="5"></textarea>';
    <?php } ?>
    html += '</td>';
    html += '<td class="center">';
    <?php foreach ($languages as $language) { ?>
        var language_id = "<?php echo $language['language_id']; ?>";
        html += '<textarea class="custom_url_store_language'+language_id+'" name="custom_url_store['+custom_url_store_row+'][custom_url_store_description]['+language_id+'][meta_description]" cols="20" rows="5"></textarea>';
    <?php } ?>
    html += '</td>';
    html += '<td align="center"><a style="text-decoration: none;" onclick="$(\'#custom_url_store_row' + custom_url_store_row + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
    html += '</tr>';
    html += '</tbody>';
    
    $('#custom_url_store tfoot').before(html);
    
    custom_url_store_row++;
    showLang('custom_url_store_language'+currency,'custom_url_store_language');
}

function showLang(selected,button){
    $('.'+selected).siblings().hide();
    $('.'+selected).show();
    $('.button_tabs .'+button+'_button').css('opacity','1');
    $('.button_tabs a[value="'+selected+'"]').css('opacity','0.8');
}

showLang('custom_url_store_language'+currency,'custom_url_store_language');
showLang('product_language'+currency,'product_language');
showLang('category_language'+currency,'category_language');
showLang('manufacturer_language'+currency,'manufacturer_language');
showLang('information_language'+currency,'information_language');

$(document).ready(function() {
    var click_tab = 'a[href=#'+"<?php echo $tab; ?>"+']';
    $(click_tab).click();
    $("#tabs a").unbind('click');
    $("#tabs a").each(function() {
        tab = $(this).attr('href').substr(1);
        $(this).attr('href','<?php echo $action ?>&tab=' + tab);
        
    });
    $("input, textarea").keypress(function() {
        edited = true;
    });
    $("form").submit(function(e) {
        edited = false;
    });
    
});

$(function(){
    $(".help_icon").tipTip({
        maxWidth: "275px", 
        defaultPosition: "top",
        delay: 100
    });
});

<?php if(isset($existing_keyword) && $existing_keyword){ ?>
    var existing_keyword = "<?php echo $existing_keyword; ?>";
    var selector = $('input[value*="'+existing_keyword+'"]');
    var trimmed;
    $(selector).each(function() {
        trimmed = $.trim($(this).val());
        if(trimmed == existing_keyword){
            $(this).addClass('existing_keyword');
        }
    });
    $(selector).keyup(function() {
        $(this).removeClass('existing_keyword');
    });
<?php } ?>

//--></script>

<script type="text/javascript"><!--
$('#tabs a').tabs();

window.onbeforeunload = confirmExit;

function confirmExit()  {
    if(edited) {
        var click_tab = 'a[href=#'+"<?php echo $tab; ?>"+']';
        $(click_tab).click();
        return "Save the changes, page refresh will result to loose unsaved data";
    } 
}

function filter(){
    url = '<?php echo $filter; ?>';
	
    var filter_keyword = $('input[name=\'filter_keyword\']').attr('value');
	
	if (filter_keyword) {
		url += '&filter_keyword=' + encodeURIComponent(filter_keyword);
	}

    location = url;
}

function reset_filter() {
    url = '<?php echo $filter; ?>';
    location = url;
}

/*
$('input[name=filter_keyword]').keydown(function(e) {
	if (e.keyCode == 13) {
		filter();
	}
});
*/
    
//--></script>

<?php echo $footer; ?>
