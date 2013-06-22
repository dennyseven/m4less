<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>

  <?php if ($thumb || $description) { ?>
  <div class="category-info">

    <?php if ($thumb) { ?>
    <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>

    <?php if ($description) { ?>
    <?php echo $description; ?>
    <?php } ?>
  </div>
  <?php } ?>


<?php if ($categories) { ?>
 <div class="general-title"><span><?php echo $text_refine; ?></span></div>
  <div class="category-list">
    <?php if (count($categories) <= 5) { ?>
    <ul>
      <?php foreach ($categories as $category) { ?>
      <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
      <?php } ?>
    </ul>
    <?php } else { ?>
    <?php for ($i = 0; $i < count($categories);) { ?>
    <ul>
      <?php $j = $i + ceil(count($categories) / 4); ?>
      <?php for (; $i < $j; $i++) { ?>
      <?php if (isset($categories[$i])) { ?>
      <li><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
    </ul>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?> 
<div class="clear"></div>


  <?php if ($products) { ?>
  <div class="product-filter">

    <div class="limit"><b><?php echo $text_limit; ?></b>
      <select class="styled-select" onchange="location = this.value;" style="z-index:10;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><b><?php echo $text_sort; ?></b>
      <select class="styled-select" onchange="location = this.value;" style="z-index:10;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>

<div id="products-listing">
<?php foreach ($products as $product) { ?>
<div class="box-container">

<div class="box-title"><a href="<?php echo $product['href']; ?>"><?php if( strlen( $product['name'] ) < 28 ) { echo $product['name']; } else { echo substr( $product['name'],0,25 )."..."; } ?></a></div><!--box-title ends-->

  <div class="box-preloader">
   <div class="boxgrid captionfull">
     <a class="box-image" href="<?php echo $product['href']; ?>" style="background-image:url('<?php echo $product['thumb']; ?>'); background-position:center center;">

         <span class="cover boxcaption">
	<span class="boxcaption-child">
	     <?php echo mb_substr(strip_tags(html_entity_decode($product['description'])), 0, 112)."..." ?><br />	     
	 </span>
          </span>

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
</div><!--products-listing ends-->

<div class="clear"></div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><span><?php echo $button_continue; ?></span></a></div>
  </div><!--buttons ends-->
  <?php } ?>

  <?php echo $content_bottom; ?>
</div><!--content ends-->

<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			html  = '<div class="right">';
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';			
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
					
			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
				
			html += '</div>';

						
			$(element).html(html);
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		$.cookie('display', 'list'); 
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			
			$(element).html(html);
		});	
					
		$('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');
		
		$.cookie('display', 'grid');
	}
}

view = $.cookie('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 


<?php echo $column_right; ?>

<!--NEW-->
<div style="width:200px;padding-top:100px;text-align:center;float:right;">
<h2 style="color:#222222;font-size:18px;"><u>Testimonials</u></h2><br/>
<hr/>
<p style="color:#adadad;text-align:left;padding-top:25px;padding-bottom:25px;">"<i>Gave excellent service on defective item long after purchase.</i>"<br/><span style="color:#222222">~ Steven T. Newport Beach</span></p>
<hr/>
<p style="color:#adadad;text-align:left;padding-top:25px;padding-bottom:25px;">"<i>Great price on the Garmin GPS I purchased. ME4less doesn't show up on many of the price search engines. Free shipping on the unit I ordered and it came one day before scheduled. Great return policy.</i>"<br/><span style="color:#222222">~ Micheal A. Bridgeport</span></p>
<hr/>
</div>
<!--NEW-->

<div class="clear"></div>
</div><!--container ends-->
<?php echo $footer; ?>