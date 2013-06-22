<?php if (empty($data['ExcelPort']['Activated'])){ ?>
	<style type="text/css" rel="stylesheet">
        .ExcelPortContent .box .content { display:none }
    </style>
	<script type="text/javascript">
		$('.submitButton').html('<?php echo $text_activate; ?>');
    </script>
	<div class="notActivatedContent">
        <h1><?php echo $text_not_activated; ?></h1>
        <a href="javascript:void(0)" onclick="$('#form').attr('action',$('#form').attr('action')+'&activate=true'); $('#form').submit();" class="ExcelPortActivateButton"><?php echo $text_click_activate; ?></a>
	</div>
<?php } ?>