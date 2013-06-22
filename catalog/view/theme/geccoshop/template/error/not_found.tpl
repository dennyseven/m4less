<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>
    
    <div class="content"><?php echo $text_error; ?></div>
    <div class="buttons-error">
    <div class="right"><a href="<?php echo $continue; ?>" class="button" id="continue-button"><?php echo $button_continue; ?></a></div>
    </div><!--buttons-error ends-->
    <?php echo $content_bottom; ?></div><!--content ends-->
<div class="clear"> </div>
</div><!--container ends-->
<?php echo $footer; ?>