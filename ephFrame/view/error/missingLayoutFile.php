<!-- this is an example view for the missinglayout message -->
<h1>Layout <q><?= basename($layout) ?></q> not found!</h1>
<p class="error">
	The layout file for the layout called <q><?= $layout ?></q> seemes to be missing.
	Please create the file at:<br />
	<q><?= $filename ?></q>
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>