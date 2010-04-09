<!-- this is an example view for the missingview error message -->
<h1>Missing View File</h1>
<p class="error">
	There seemes to be one view file missing. Please create a view file in:<br />
	<code><?php echo $filename; ?></code>
</p>
<p class="hint">
	You can edit or create your own error message for missing views by editing
	this file: 
	<code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>