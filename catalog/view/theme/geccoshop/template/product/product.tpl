<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>
 
<div class="product-info">
    <?php if ($thumb || $images) { ?>
    

<div class="imagecol">
      
<?php if ($thumb) { ?>
      <div class="image"><a href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="colorbox" style="display:block; background-image:url('<?php echo $thumb; ?>'); background-position:center center;"></a></div>

<?php } ?>
      <?php if ($images) { ?>
      <div class="image-additional">
        <?php foreach ($images as $image) { ?>
 <div class="small-image"><a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox" style="display:block; background-image:url('<?php echo $image['thumb']; ?>'); background-position:center center;"></a></div>
 <?php } ?>
<div class="clear"></div>
      </div>
      <?php } ?>
    </div>
<?php } ?>
    



<div class="productcol">
      
<div class="product-details">
<ul>
        <?php if ($manufacturer) { ?>
        <li><span><?php echo $text_manufacturer; ?></span> <b><a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></b></li>
        <?php } ?>
        <li><span><?php echo $text_model; ?></span> <b><?php echo $model; ?></b></li>
        <li><span><?php echo $text_stock; ?></span> <b><?php echo $stock; ?></b></li>
   <?php if ($tags) { ?>
  <li><span><?php echo $text_tags; ?></span>
    <?php for ($i = 0; $i < count($tags); $i++) { ?>
    <?php if ($i < (count($tags) - 1)) { ?>
    <b><a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a></b>,
    <?php } else { ?>
    <b><a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a></b>
    <?php } ?>
    <?php } ?>
  </li>
  <?php } ?>
</ul>
</div>
     




<?php if ($price) { ?>
     <div class="price-container">
     <div class="price">
        <?php if (!$special) { ?>
        <div class="price-amount"><?php echo $price; ?></div>
        <?php if ($tax) { ?>
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $tax; ?></span>
        <?php } ?>

        <?php } else { ?>
        <div class="price-amount"><?php echo $special; ?></div>
        <span class="price-old"><?php echo $price; ?></span> 
        <?php } ?>
     </div><!--price ends-->

        <?php if ($points) { ?>
        <span class="reward"><small><?php echo $text_points; ?> <?php echo $points; ?></small></span> <br />
        <?php } ?>
        <?php if ($discounts) { ?>
        <br />
        <div class="discount">
          <?php foreach ($discounts as $discount) { ?>
          <?php echo sprintf($text_discount, $discount['quantity'], $discount['price']); ?><br />
          <?php } ?>
        </div>
        <?php } ?>

     </div><!--price-container ends--> 
<?php } ?>
<div class="clear"></div>

<?php if ($review_status) { ?>
      <div class="review">
          <div class="reviews-stars"><img src="catalog/view/theme/geccoshop/image/stars-<?php echo $rating; ?>.png" alt="<?php echo $reviews; ?>" /></div> <div class="reviews-number"><?php echo $reviews; ?></div>
      </div>
<div class="clear"></div>
      <?php } ?>


       <?php if ($options) { ?>
      <div class="options">
       
        <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'select') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <select class="styled-select" name="option[<?php echo $option['product_option_id']; ?>]">
            <option value=""><?php echo $text_select; ?></option>
            <?php foreach ($option['option_value'] as $option_value) { ?>
            <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
            <?php } ?>
            </option>
            <?php } ?>
          </select>
        </div>
        <?php } ?>

        <?php if ($option['type'] == 'radio') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<br />
            <?php } ?>
          </label>
          <?php } ?>
        </div>
        <br />
        <?php } ?>

        <?php if ($option['type'] == 'checkbox') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <?php foreach ($option['option_value'] as $option_value) { ?>
          <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
          <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"> <?php echo $option_value['name']; ?>
            <?php if ($option_value['price']) { ?>
            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<br />
            <?php } ?>
          </label>
          <?php } ?>
        </div>
        <br />
        <?php } ?>

        <?php if ($option['type'] == 'text') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" />
        </div>
        <br />
        <?php } ?>


        <?php if ($option['type'] == 'image') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <table class="option-image">
            <?php foreach ($option['option_value'] as $option_value) { ?>
            <tr>
              <td style="width: 1px;"><input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" /></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" /></label></td>
              <td><label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                  <?php if ($option_value['price']) { ?>
                  (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                  <?php } ?>
                </label></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <br />
        <?php } ?>

        <?php if ($option['type'] == 'textarea') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="29" rows="5"><?php echo $option['option_value']; ?></textarea>
        </div>
        <?php } ?>

        <?php if ($option['type'] == 'file') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="button" value="<?php echo $button_upload; ?>" id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
          <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" />
        </div>
        <?php } ?>

        <?php if ($option['type'] == 'date') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="date" />
        </div>
        <?php } ?>

        <?php if ($option['type'] == 'datetime') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="datetime" />
        </div>
        <?php } ?>

        <?php if ($option['type'] == 'time') { ?>
        <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
          <?php if ($option['required']) { ?>
          <span class="required">*</span>
          <?php } ?>
          <b><?php echo $option['name']; ?>:</b><br />
          <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['option_value']; ?>" class="time" />
        </div>
        <?php } ?>

        <?php } ?>
      </div><div class="clear"></div>
      <?php } ?>
      

<div class="misc-links1">
<a onclick="addToWishList('<?php echo $product_id; ?>');"><?php echo $button_wishlist; ?></a> &nbsp;&nbsp;&nbsp;&nbsp; <a onclick="addToCompare('<?php echo $product_id; ?>');"><?php echo $button_compare; ?></a>
</div>


<div class="cart">

<div class="quantity"><?php echo $text_qty; ?>
          <input type="text" name="quantity" size="2" value="<?php echo $minimum; ?>" />
          <input type="hidden" name="product_id" size="2" value="<?php echo $product_id; ?>" /><br />        
</div>

<input type="button" value=" " id="button-cart" class="button" />
        <?php if ($minimum > 1) { ?>
        <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?>
</div>
      



    </div><!--productcol ends-->
  </div><!--product-info ends-->



<div class="product-description">
<div class="trigger-title"><a href="#"><?php echo trim(strtolower($tab_description)) == 'related products' ? 'Accessories' : $tab_description ?></a></div>
<?php echo $description; ?>
</div>





<?php if ($attribute_groups) { ?>
<div class="trigger-title"><a href="#"><?php echo $tab_attribute; ?></a></div>
<div ">
    <table class="attribute">
      <?php foreach ($attribute_groups as $attribute_group) { ?>
      <thead>
        <tr>
          <td colspan="2"><?php echo $attribute_group['name']; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
        <tr>
          <td><?php echo $attribute['name']; ?></td>
          <td><?php echo $attribute['text']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <?php } ?>
    </table>
</div>
  <?php } ?>





  <?php if ($products) { ?>
  <div id="related">
    <div class="trigger-title"><a href="#"><?php echo $tab_related; ?></a></div>

      <?php foreach ($products as $product) { ?>
<div class="box-container">

<div class="box-title"><a href="<?php echo $product['href']; ?>"><?php if( strlen( $product['name'] ) < 28 ) { echo $product['name']; } else { echo substr( $product['name'],0,25 )."..."; } ?></a></div><!--box-title ends-->

  <div class="box-preloader">
   <div class="boxgrid captionfull">
     <a class="box-image" href="<?php echo $product['href']; ?>" style="background-image:url('<?php echo $product['thumb']; ?>'); background-position:center center;">

          <?php if ($product['special']) { ?>
          <img class="special-tag" src="catalog/view/theme/geccoshop/image/sale.png" alt="Sale" />
          <?php } ?>

          <span class="box-price">
          <?php if (!$product['special']) { ?>
          <span class="box-price-amount">&nbsp; <?php echo $product['price']; ?></span>
          <?php } else { ?>
          <span class="box-price-amount">&nbsp; <?php echo $product['special']; ?></span>
          <?php } ?>
         </span><!-- box-price ends-->

     </a><!--box-image ends-->
   </div><!--boxgrid captionfull ends-->
  </div><!--box-preloader -->

 <div class="box-bottom">
          <span class="box-add-to-cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');"> </a></span>
          <div class="box-rating"><img class="rating-stars" src="catalog/view/theme/geccoshop/image/stars-<?php echo $product['rating'] . 'a.png'; ?>" alt="Rating" /></div>          
 <div class="clear"></div>
 </div><!--box-bottom ends-->
</div><!--box-container ends-->

<?php } ?>

    </div>
  </div><!--related ends-->
<div class="clear"></div>
  <?php } ?>





<?php if ($review_status) { ?>
 <div id="reviews-container">
 <div class="trigger-title"><a href="#"><?php echo $tab_review; ?></a></div>
 <div class="toggle_container">
    <div id="review"></div>
    <h6 id="review-title"><?php echo $text_write; ?></h6>
    <small><?php echo $entry_name; ?></small><br />
    <input type="text" name="name" value="" />
    <br />
    <br />
    <small><?php echo $entry_review; ?></small><br />
    <textarea name="text" cols="40" rows="4" style="width: 60%;"></textarea><br />
    <span style="font-size: 11px;"><?php echo $text_note; ?></span><br />
    <br />
    <b><?php echo $entry_rating; ?></b> <span><?php echo $entry_bad; ?></span>&nbsp;
    <input type="radio" name="rating" value="1" />
    &nbsp;
    <input type="radio" name="rating" value="2" />
    &nbsp;
    <input type="radio" name="rating" value="3" />
    &nbsp;
    <input type="radio" name="rating" value="4" />
    &nbsp;
    <input type="radio" name="rating" value="5" />
    &nbsp; <span><?php echo $entry_good; ?></span><br />
    <br />
    <small><?php echo $entry_captcha; ?></small><br />
    <input type="text" name="captcha" value="" />
    <br />
    <img src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
    <br />
    <div class="buttons">
      <div class="right"><a id="button-review" class="button"><span><?php echo $button_continue; ?></span></a></div>
    </div>
    </div>
</div><!--reviews-container ends-->
  <?php } ?>





<?php echo $content_bottom; ?>
</div><!--content ends-->

<script type="text/javascript"><!--
$('#button-cart').bind('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
		success: function(json) {
			$('.success, .warning, .attention, information, .error').remove();
			
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
					}
				}
			} 
			
			if (json['success']) {
				$('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/geccoshop/image/close.png" alt="" class="close" /></div>');
					
				$('.success').fadeIn('slow');
					
				$('#cart-total').html(json['total']);
				
				$('html, body').animate({ scrollTop: 0 }, 'slow'); 
			}	
		}
	});
});
//--></script>
<?php if ($options) { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
<?php foreach ($options as $option) { ?>
<?php if ($option['type'] == 'file') { ?>
<script type="text/javascript"><!--
new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
	action: 'index.php?route=product/product/upload',
	name: 'file',
	autoSubmit: true,
	responseType: 'json',
	onSubmit: function(file, extension) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
	},
	onComplete: function(file, json) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
		
		$('.error').remove();
		
		if (json['success']) {
			alert(json['success']);
			
			$('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
		}
		
		if (json['error']) {
			$('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
		}
		
		$('.loading').remove();	
	}
});
//--></script>
<?php } ?>
<?php } ?>
<?php } ?>
<script type="text/javascript"><!--
$('#review .pagination a').live('click', function() {
	$('#review').fadeOut('slow');
		
	$('#review').load(this.href);
	
	$('#review').fadeIn('slow');
	
	return false;
});			

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').bind('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-review').attr('disabled', true);
			$('#review-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-review').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(data) {
			if (data['error']) {
				$('#review-title').after('<div class="warning">' + data['error'] + '</div>');
			}
			
			if (data['success']) {
				$('#review-title').after('<div class="success">' + data['success'] + '</div>');
								
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').attr('checked', '');
				$('input[name=\'captcha\']').val('');
			}
		}
	});
});
//--></script> 
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript"><!--
if ($.browser.msie && $.browser.version == 6) {
	$('.date, .datetime, .time').bgIframe();
}

$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m'
});
$('.time').timepicker({timeFormat: 'h:m'});
//--></script> 


<?php echo $column_right; ?>
<div class="clear"></div>
</div><!--container ends-->
<?php echo $footer; ?>