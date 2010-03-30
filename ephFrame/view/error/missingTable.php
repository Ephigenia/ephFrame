<!-- this is an example view for the missing database table error message -->
<h1>Missing Database Table</h1>
<p class="error">
	There seemes to be a missing table: <q><?php echo $tablename ?></q>. Please
	create this table an continue.
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<q><?php echo VIEW_DIR ?>error/<?php echo basename(__FILE__) ?></q>
</p>