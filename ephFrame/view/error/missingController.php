<?php if (empty($controllerName)) $controllerName = '[noname]'; ?>
<h1>Missing Controller <q><?php echo $controllerName ?></q></h1>
<p class="error">
	Please create a <q><?php echo $controllerName ?></q> class that extends
	from <q>AppController</q> in
	<code><?php echo APP_LIB_DIR.'controller/'.$controllerName.'.php' ?></code>
</p>
<p class="hint">
	You’re seeing this message because your in debugging mode. If you see this
	message in any live environment set the DEBUG level to equal or lower than
	<code>DEBUG_PRODUCTION</code>.
</p>
<p class="hint">
	You can modify this message by editing or creating this file: 
	<code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>