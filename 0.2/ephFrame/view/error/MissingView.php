<h1>Missing View File for action <q><?= $missingAction ?></q></h1>
<p class="error">
	There seemes to be one view file missing. Please create a view file in:<br />
	<q><?= VIEW_DIR.lcFirst($missingAction) ?>.php</q>
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>