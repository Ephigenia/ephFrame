<!-- this is an example view for the missinglayout message -->
<h1>Layout <q><?php echo basename($layout) ?></q> not found!</h1>
<p class="error">
	The layout file for the layout called <q><?php echo $layout ?></q> seemes to be missing.
	Please create the file at:<br />
	<q><?php echo $filename ?></q>
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__) ?></q>
</p>