<h1>Missing View for action <q><?= $missingAction ?></q></h1>
<p class="error">
	Please create a view file for the <q><?= $missingController ?></q> action
	<q><?= $missingAction ?></q> in the applications view directory
	<q><?= VIEW_DIR ?></q>
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>