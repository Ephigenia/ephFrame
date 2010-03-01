<?php if (empty($flashMessage)) return false; ?>
<p id="flashMessage" class="<?= $flashMessage['type'] ?>">
	<?= @$flashMessage['message'] ?>
</p>