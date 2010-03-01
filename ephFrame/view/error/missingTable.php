<!-- this is an example view for the missing database table error message -->
<h1>Missing Database Table</h1>
<p class="error">
	There seemes to be a missing table: <q><?= $tablename ?></q>. Please
	create this table an continue.
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?= VIEW_DIR ?>error/<?= basename(__FILE__) ?></q>
</p>