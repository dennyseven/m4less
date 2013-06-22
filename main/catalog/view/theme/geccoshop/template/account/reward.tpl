<?php echo $header; ?>
<div class="page-title"><?php echo $heading_title; ?></div>
</div><!--header-background ends-->

<div id="container">

<div id="content">
<?php echo $content_top; ?>
  <p><?php echo $text_total; ?><b> <?php echo $total; ?></b>.</p>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?php echo $column_date_added; ?></td>
        <td class="left"><?php echo $column_description; ?></td>
        <td class="right"><?php echo $column_points; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php if ($rewards) { ?>
      <?php foreach ($rewards  as $reward) { ?>
      <tr>
        <td class="left"><?php echo $reward['date_added']; ?></td>
        <td class="left"><?php if ($reward['order_id']) { ?>
          <a href="<?php echo $reward['href']; ?>"><?php echo $reward['description']; ?></a>
          <?php } else { ?>
          <?php echo $reward['description']; ?>
          <?php } ?></td>
        <td class="right"><?php echo $reward['points']; ?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="center" colspan="5"><?php echo $text_empty; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <div class="pagination"><?php echo $pagination; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $column_right; ?>
<div class="clear"></div>
</div><!--container ends-->

<?php echo $footer; ?>