<?php if (empty($missingControllerName)) $missingControllerName = '[noname]'; ?>
<h1>Missing Controller Class <q><?= $missingControllerName ?></q></h1>
<p class="error">
	Please create the controller class in 
	<q><?= APP_CONTROLLER_DIR.$missingControllerName.'.php' ?></q>
</p>
<?php if (class_exists('Registry') && Registry::get('DEBUG') > DEBUG_PRODUCTION) { ?>
<p class="hint">
	You can customize this error message by creating the file at:<br />
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>
<?php } ?>