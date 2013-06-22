<div id="featured-products">

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

</div><!--featured-products ends-->