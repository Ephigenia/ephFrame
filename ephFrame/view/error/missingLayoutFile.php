<!-- this is an example view for the missinglayout message -->
<h1>Layout <q><?php echo basename($layout) ?></q> not found!</h1>
<p class="error">
	The layout file for the layout called <q><?php echo basename($filename) ?></q> seemes to be missing.
	Please create the layout file at:<br />
	<code><?php echo $filename ?></code>
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