<!-- this is an example view for the missinglayout message -->
<h1>Layout <q><?php echo basename($layout) ?></q> not found!</h1>
<p class="error">
	The layout file for the layout called <q><?php echo $layout ?></q> seemes to be missing.
	Please create the layout file at:<br />
	<code><?php echo $filename ?></code>
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: <code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>