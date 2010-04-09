<!-- this is an example view for the missing database table error message -->
<h1>Missing Database Table</h1>
<p class="error">
	There seemes to be a missing table: <q><?php echo $tablename ?></q>. Please
	create this table in your database an continue.
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>