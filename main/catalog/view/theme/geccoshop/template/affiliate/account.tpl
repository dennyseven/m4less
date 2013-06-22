<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

  <h5><?php echo $text_my_account; ?></h5>
  <div class="content">
    <ul>
      <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
      <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
      <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
    </ul>
  </div>
  <h5><?php echo $text_my_tracking; ?></h5>
  <div class="content">
    <ul>
      <li><a href="<?php echo $tracking; ?>"><?php echo $text_tracking; ?></a></li>
    </ul>
  </div>
  <h5><?php echo $text_my_transactions; ?></h5>
  <div class="content">
    <ul>
      <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
    </ul>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $column_right; ?>
<div class="clear"></div>
</div><!--container ends-->

<?php echo $footer; ?>