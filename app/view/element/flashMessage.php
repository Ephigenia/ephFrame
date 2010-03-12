<?php if (empty($flashMessage)) return false; ?>
<div id="flashMessage" class="<?= @$flashMessage['type'] ?>">
	<?= @$flashMessage['message'] ?>
</div>