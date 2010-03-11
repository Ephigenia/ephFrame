<?php if (empty($controllerName)) $controllerName = '[noname]'; ?>
<!-- this is an example view for the missing controller error message -->
<h1>Missing Controller <q><?= $controllerName ?></q></h1>
<p class="error">
	Please create the controller class in
	<q><?= APP_LIB_DIR.'controller/'.$controllerName.'.php' ?></q>
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>