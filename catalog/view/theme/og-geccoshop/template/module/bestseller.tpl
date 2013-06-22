<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="box-product">
     <ol>
      <?php foreach ($products as $product) { ?>
	<li><a href="<?php echo $product['href']; ?>"><?php if( strlen( $product['name'] ) < 35 ) { echo $product['name']; } else { echo substr( $product['name'],0,32 )."..."; } ?></a></li>

     <?php } ?>
     </ol>
    </div>
  </div>
</div>
