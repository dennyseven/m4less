<?php echo $header; ?>
<style>
.help_seo p {
    font-size: 15px;
    margin-bottom: 5px;
}
.help_seo div {
    padding: 15px;
    padding-top: 0px;
}
.bgcolor {
    font-size: 13px;
}
</style>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if (isset($success) && $success) { ?>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <div class="helper-msg">
            <?php echo $autogenerate_help; ?>
          </div>

          <table class="form">
            <tr>
                <td><?php echo $source_language;?></td>
                <td>
                    <select name="source_language_code" id="source_language_code">
                        <?php foreach ($languages as $language) {
                            echo '<option value="' . $language['code'] . '"' . ($language['code']==$source_language_code?' selected="selected"':'') . '>' . $language['name'] . '</option>';
                        }?>
                    </select>
                </td>
            </tr>
          </table>
          
          <table class="list store_seo">
            <thead>
              <tr>
                <td class="left"><?php echo $text_entity; ?></td>
                <td class="left"><?php echo $text_description; ?></td>
                <td class="left"><?php echo $text_pattern; ?></td>
                <td class="left"><?php echo $text_action; ?></td>
              </tr>
            </thead>  
            <tbody>
              <!-- Products -->
              <tr>
                <td rowspan="6" class="bgcolor"><b><?php echo $text_products;?></b></td>
                <td class="help_seo">
                  <p><b><?php echo $text_url_keyword; ?></b></p>
                  <div><?php echo $help_product_seo_description; ?></div>
                </td>
                <td><input type="text" id="products_url_template" name="products_url_template" value="<?php echo $products_url_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="products_url" value="products_url" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_title; ?></b></p>
                  <div><?php echo $help_product_title; ?></div>
                </td>
                <td><input type="text" id="products_title_template" name="products_title_template" value="<?php echo $products_title_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="products_title" value="products_title" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr>
                <td class="help_seo">
                  <p><b><?php echo $text_meta_keywords; ?></b></p>
                  <div><?php echo $help_product_keywords_description; ?></div>
                </td>
                <td>
                  <input type="text" id="product_keywords_template" name="product_keywords_template" value="<?php echo $product_keywords_template;?>" size="80" class="blueprint">
                  <?php if (in_array('curl', get_loaded_extensions())) {?>
                  <div class="seo_yahoo help_seo">
                    <input type="checkbox" name="yahoo_checkbox"<?php if ($yahoo_checkbox==1) echo 'checked="checked"';?>><?php echo $add_from_yahoo;?><br/>
                    <label for="yahoo_id"><?php echo $your_yahoo_id;?> </label><input type="text" id="yahoo_id" name="yahoo_id" value="<?php echo $yahoo_id;?>" size="30" class="blueprint"><br/>
                    <div class="help"><?php echo $get_yahoo_id;?></div><br/>
                  </div>
                  <?php } else {?>
                    <div><?php echo $curl_not_enabled;?></div>
                    <input type="hidden" id="yahoo_id" name="yahoo_id" value="">
                  <?php } ?>
                </td>
                <td><div class="buttons"><button type="submit" name="product_keywords" value="product_keywords" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr>
                <td class="help_seo">
                  <p><b><?php echo $text_meta_description; ?></b></p>
                  <div><?php echo $help_product_description; ?></div>
                  <div><?php echo $note_product_meta_description; ?></div>
                </td>
                <td><input type="text" id="product_description_template" name="product_description_template" value="<?php echo $product_description_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="product_description" value="product_description" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr>
                <td class="help_seo">
                  <p><b><?php echo $text_tags; ?></b></p>
                  <div><?php echo $help_product_tags; ?></div>
                </td>
                <td><input type="text" id="product_tags_template" name="product_tags_template" value="<?php echo $product_tags_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="product_tags" value="product_tags" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_image_name; ?></b></p>
                  <div><?php echo $help_product_image_description; ?></div>
                </td>
                <td><input type="text" id="product_image_template" name="product_image_template" value="<?php echo $product_image_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="product_image" value="product_image" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>

              <!-- Categories -->
              <tr>
                <td rowspan="4" class="bgcolor"><b><?php echo $text_categories;?></b></td>
                <td class="help_seo">
                  <p><b><?php echo $text_url_keyword; ?></b></p>
                  <div><?php echo $help_category_seo_description; ?></div>
                </td>
                <td><input type="text" id="categories_url_template" name="categories_url_template" value="<?php echo $categories_url_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="categories_url" value="categories_url" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_title; ?></b></p>
                  <div><?php echo $help_category_title; ?></div>
                </td>
                <td><input type="text" id="categories_title_template" name="categories_title_template" value="<?php echo $categories_title_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="categories_title" value="categories_title" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_meta_keywords; ?></b></p>
                  <div><?php echo $help_category_meta_keyword; ?></div>
                </td>
                <td><input type="text" id="categories_keyword_template" name="categories_keyword_template" value="<?php echo $categories_keyword_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="categories_keyword" value="categories_keyword" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_meta_description; ?></b></p>
                  <div><?php echo $help_category_description; ?></div>
                  <div><?php echo $note_category_meta_description; ?></div>
                </td>
                <td><input type="text" id="category_description_template" name="category_description_template" value="<?php echo $category_description_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="category_description" value="category_description" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>

              <!-- Manufacturers -->
              <tr class="bb2">
                <td class="bgcolor"><b><?php echo $text_manufacturers;?></b></td>
                <td class="help_seo">
                  <p><b><?php echo $text_url_keyword; ?></b></p>
                  <div><?php echo $help_manufacturer_seo_description; ?></div>
                </td>
                <td><input type="text" id="manufacturers_url_template" name="manufacturers_url_template" value="<?php echo $manufacturers_url_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="manufacturers_url" value="manufacturers_url" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>

              <!-- Information Pages -->
              <tr>
                <td rowspan="2" class="bgcolor"><b><?php echo $text_information_pages;?></b></td>
                <td class="help_seo">
                  <p><b><?php echo $text_url_keyword; ?></b></p>
                  <div><?php echo $help_information_seo_description; ?></div>
                </td>
                <td><input type="text" id="information_pages_template" name="information_pages_template" value="<?php echo $information_pages_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="information_pages" value="information_pages" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
 
              <tr class="bb2">
                <td class="help_seo">
                  <p><b><?php echo $text_title; ?></b></p>
                  <div><?php echo $help_information_title; ?></div>
                </td>
                <td><input type="text" id="information_pages_title_template" name="information_pages_title_template" value="<?php echo $information_pages_title_template;?>" size="80" class="blueprint"></td>
                <td><div class="buttons"><button type="submit" name="information_pages_title" value="information_pages_title" class="button button_adjust"><span><?php echo $generate;?></span></button></div></td>
              </tr>
 
            </tbody>
          </table>
        </form>
    </div>
</div>
</div>


<?php echo $footer; ?>
