<h1>Page not Found, 401</h1>
<p>
	The page you requested requires user authentication. Please check your
	data and try again.
</p>
<?php if (class_exists('Registry') && Registry::get('DEBUG') > DEBUG_PRODUCTION) { ?>
<p class="hint">
	You can customize this error message by creating your own 401 error file:<br />
	<code><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__); ?></code>
</p>
<?php } ?>