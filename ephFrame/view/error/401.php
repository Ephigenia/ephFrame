<h1>Page not Found, 404</h1>
<p>
	The page you requested requires user authentication. Please check your
	data and try again.
</p>
<?php if (class_exists('Registry') && Registry::get('DEBUG') > DEBUG_PRODUCTION) { ?>
<p class="hint">
	You can customize this error message by creating your own 401.php file in:<br />
	<code><?php echo VIEW_DIR ?>error/401.php</code>
</p>
<?php } ?>