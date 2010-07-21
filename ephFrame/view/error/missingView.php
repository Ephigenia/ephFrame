<!-- this is an example view for the missingview error message -->
<h1>Missing View File</h1>
<p class="error">
	There seemes to be one view file missing. Please create a view file in:<br />
	<code><?php echo $filename; ?></code>
</p>
<p class="hint">
	You’re seeing this message because your in debugging mode. If you see this
	message in any live environment set the DEBUG level to equal or lower than
	<code>DEBUG_PRODUCTION</code>.
</p>
<p class="hint">
	You can modify this message by editing or creating this file: 
	<code><?php echo VIEW_DIR.'error/'.basename(__FILE__) ?></code>
</p>