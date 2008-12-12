<h1>Missing Database Table <q><?= $tablename ?></q></h1>
<p class="error">
	A model is missing a table named <q><?= $tablename ?></q> in the database.
	Please create that table or change the models tablename to match.
</p>
<p class="hint">
	You can edit this error message by creating your own view for it in
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>