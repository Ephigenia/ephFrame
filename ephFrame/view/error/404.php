<h1>Page not Found, 404</h1>
<p>
	The page you requested could not be found. Please check the url and try again.
</p>
<?php if (class_exists('Registry') && Registry::get('DEBUG') > DEBUG_PRODUCTION) { ?>
<p class="hint">
	You can customize this error message by creating your own 404.php file in:<br />
	<code><?php echo VIEW_DIR ?>error/404.php</code>
</p>
<?php } ?>