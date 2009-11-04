<!-- this is an example view for the missinglayout message -->
<h1>Theme <q><?= $theme ?></q> not found!</h1>
<p class="error">
	Sorry, ephFrame was not able to find the theme directory for the theme <q><?= $theme ?></q>. Please
	create this directory in <q><?= VIEW_THEME_DIR ?></q>.
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>