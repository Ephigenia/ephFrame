<?php if (empty($dir)) $dir = '[noname]'; ?>
<h1>Unwritable Directory: <q><?php echo $dir ?></q></h1>
<p class="error">
	Please make the directory <code><?php echo $dir ?></code>
	writable for the application.
</p>

<p class="hint">
	You can edit this error message by creating your own view for it in
	<code><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__) ?></code>
</p>