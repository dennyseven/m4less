<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <p><?php echo $text_email; ?></p>
    <h2><?php echo $text_your_email; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?php echo $entry_email; ?></td>
          <td><input type="text" name="email" value="" /></td>
        </tr>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $column_right; ?>
<div class="clear"></div>
</div><!--container ends-->

<?php echo $footer; ?>