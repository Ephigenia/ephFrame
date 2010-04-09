<?php if (empty($controllerName)) $controllerName = '[noname]'; ?>
<!-- this is an example view for the missing controller error message -->
<h1>Missing Controller <q><?php echo $controllerName ?></q></h1>
<p class="error">
	Please create the controller class in
	<code><?php echo APP_LIB_DIR.'controller/'.$controllerName.'.php' ?></code>
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>