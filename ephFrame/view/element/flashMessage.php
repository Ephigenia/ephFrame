<?php if (empty($flashMessage)) return false; ?>
<div id="flashMessage" class="<?php echo @$flashMessage['type'] ?>">
	<?php echo @$flashMessage['message'] ?>
</div>