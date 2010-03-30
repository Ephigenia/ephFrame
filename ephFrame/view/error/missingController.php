<?php if (empty($controllerName)) $controllerName = '[noname]'; ?>
<!-- this is an example view for the missing controller error message -->
<h1>Missing Controller <q><?php echo $controllerName ?></q></h1>
<p class="error">
	Please create the controller class in
	<q><?php echo APP_LIB_DIR.'controller/'.$controllerName.'.php' ?></q>
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__) ?></q>
</p>