<?php if (empty($flashMessage)) return false; ?>
<p id="flashMessage" class="<?= $flashMessage['type'] ?>">
	<?= @$flashMessage['message'] ?>
</p>
<?php
$JavaScript->jQuery("
	$('#flashMessage').hide().fadeIn('slow');	
	window.setTimeout(\"$(\'#flashMessage\').fadeOut('slow');\", 5000);
");

?>
