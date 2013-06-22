<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>

  <?php if ($products) { ?>
  <div class="product-filter">
  
    <div class="limit"><?php echo $text_limit; ?>
      <select class="styled-select" onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><?php echo $text_sort; ?>
      <select class="styled-select" onchange="location = this.value;">
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
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php }?>
  <?php echo $content_bottom; ?></div>
<div class="clear"></div>

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
</div><!--container ends-->
<div class="clear"> </div>
<?php echo $footer; ?>