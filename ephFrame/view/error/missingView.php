<!-- this is an example view for the missingview error message -->
<h1>Missing View File</h1>
<p class="error">
	There seemes to be one view file missing. Please create a view file in:<br />
	<q><?= $filename; ?></q>
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>