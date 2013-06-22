<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="box-product">
      <?php foreach ($products as $product) { ?>

<div class="special-item">
<div class="special-box-container">
   <a class="special-box-image" href="<?php echo $product['href']; ?>" style="background-image:url('<?php echo $product['thumb']; ?>'); background-position:center center;">
   </a><!--special-box-image ends-->
 </div><!--special-box-container ends-->

<div class="special-box-title"> 
<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <span class="special-box-price"><?php if (!$product['special']) { ?>
          <span class="special-box-price-child"><?php echo $product['price']; ?></span>
          <?php } else { ?>
          <span style="text-decoration:line-through; color:#ef0000; font-size:10px;"><?php echo $product['price']; ?></span> &rarr; <span class="price-new"><?php echo $product['special']; ?></span>
          <?php } ?></span>
      </div><!--special-box-title ends-->

<div class="clear"></div>
</div><!--special-item ends-->
      <?php } ?>
    </div>

  </div>
</div>
