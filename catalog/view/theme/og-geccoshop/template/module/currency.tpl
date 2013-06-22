<?php if (count($currencies) > 1) { ?>
<div id="panel-currencies">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
  <div id="currency">
    <?php foreach ($currencies as $currency) { ?>
    <?php if ($currency['code'] == $currency_code) { ?>
    <?php if ($currency['symbol_left']) { ?>
    <a title="<?php echo $currency['title']; ?>"><b><?php echo $currency['code']; ?></b></a>
    <?php } else { ?>
    <a title="<?php echo $currency['title']; ?>"><b><?php echo $currency['code']; ?></b></a>
    <?php } ?>
    <?php } else { ?>
    <?php if ($currency['symbol_left']) { ?>
    <a title="<?php echo $currency['title']; ?>" onClick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency['code']; ?>').submit(); $(this).parent().parent().submit();"><?php echo $currency['code']; ?></a>
    <?php } else { ?>
    <a title="<?php echo $currency['title']; ?>" onClick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency['code']; ?>').submit(); $(this).parent().parent().submit();"><?php echo $currency['code']; ?></a>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <input type="hidden" name="currency_code" value="" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  </div>
</form>
</div>
<?php } ?>
