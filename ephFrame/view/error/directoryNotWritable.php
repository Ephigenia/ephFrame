<?php if (empty($dir)) $dir = '[noname]'; ?>
<h1>Unwritable Directory: <q><?= $dir ?></q></h1>
<p class="error">
	Please make the directory <q><?= APP_CONTROLLER_DIR.$missingControllerName.'.php' ?></q>
	writable for the application.
</p>

<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>