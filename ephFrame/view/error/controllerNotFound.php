<?php if (empty($missingControllerName)) $missingControllerName = '[noname]'; ?>
<h1>Missing Controller Class <q><?php echo $missingControllerName ?></q></h1>
<p class="error">
	Please create the controller class in 
	<code><?php echo APP_CONTROLLER_DIR.$missingControllerName.'.php' ?></code>
</p>
<p class="hint">
	You can customize this error message by creating the file at:<br />
	<code><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__) ?></code>
</p>