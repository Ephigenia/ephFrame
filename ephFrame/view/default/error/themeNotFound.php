<!-- this is an example view for the missinglayout message -->
<h1>Theme <q><?php echo $theme ?></q> not found!</h1>
<p class="error">
	Sorry, ephFrame was not able to find the theme directory for the theme <q><?php echo $theme ?></q>.
	Create the theme directory at <code><?php echo VIEW_THEME_DIR ?></code>.
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