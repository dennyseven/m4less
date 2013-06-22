<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?>:</div>
  <div class="box-content">
    <div class="box-product">
      <?php foreach ($products as $product) { ?>
      
        <?php if ($product['thumb']) { ?>
   <div class="latest">
        <a href="<?php echo $product['href']; ?>" style="background-image:url('<?php echo $product['thumb']; ?>'); background-position:center center;"></a>
   </div><!--latest ends-->    
       <?php } ?>
       
      <?php } ?>
    </div> <div class="clear"></div>
  </div>
</div>
