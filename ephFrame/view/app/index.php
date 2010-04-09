<h2>Congratulations!</h2>
<p>
	You’ve made it! It looks like you managed to get ephFrame running!<br />
	Check out the <a href="http://code.marceleichner.de/project/ephFrame" title="visit project page of ephFrame" rel="external">ephFrame ProjectPage</a>
	if you need help.
</p>

<?php
// we run through some tests for default apps
// tmp dir, db setup and such stuff 

// check tmp directory writable
if (!is_dir(TMP_DIR)) {
	?>
	<p class="error">
		Temp directory not found: <code><?php echo TMP_DIR ?></code>
	</p>
	<?php
} else {
	if (!is_writable(TMP_DIR)) {
		?>
		<p class="error">
			Temp directory is not writable: <code><?php echo TMP_DIR ?></code>
		</p>
		<?php
	}
	// check for writable log directory
	if (!is_dir(LOG_DIR)) {
		?>
		<p class="error">
			The Logging directory was not found which is usually at: <code><?php echo LOG_DIR ?></code>.
		</p>
		<?php
	} elseif (!is_writable(LOG_DIR)) {
		?>
		<p class="error">
			The directory for logging is not writable for PHP, please make the directory
			<code><?php echo LOG_DIR ?></code> writable.
		</p>
		<?php
	}
	// check for database Config
	if (!file_exists(CONFIG_DIR.'db.php')) {
		?>
		<p class="error">
			No database configuration file found. Please create a database configuration file at
			<code><?php echo CONFIG_DIR.'db.php'; ?></code>
		</p>
		<?php
	} elseif (!class_exists('DBConfig')) {
		?>
		<p class="error">
		<code>db.php</code> seemes to be included but no database config found.
		</p>
		<?php
	}
	if (!is_dir(MODELCACHE_DIR)) {
		?>
		<p class="error">
			The model structure cache directory <code><?php echo MODELCACHE_DIR; ?></code>
			does not exist. Please create this directory and make it writable for PHP.
		</p>
		<?php
	} elseif (!is_writable(MODELCACHE_DIR)) {
		?>
		<p class="error">
			The model structure cache directory <code><?php echo MODELCACHE_DIR; ?></code>
			is not writable for PHP. Please ensure it’s writable for PHP.
		</p>
		<?php
	}
}


// check if default salt value is set
if (defined('SALT') && SALT === 'priotaseloukeadotraeuocrailaejot') {
	?>
	<p class="error">
		You haven’t change the SALT value in <code>/app/config.php</code>. Please change the value!
	</p>
	<?php
}

// Application’s production mode
if (Registry::get('DEBUG') != DEBUG_PRODUCTION) {
	?>
	<p class="hint">
		The Application is currently in development/debugging mode.<br />
		Don’t forget to set it to DEBUG_PRODUCTION before you deploy it to any life system.
	</p>
	<?php
}
?>

<p class="hint">
	You can modify this view by changing the layout <code>/app/view/layout/default.php</code> template file
	or create your own content file by creating the file <code>/app/view/app/index.php</code> which is always
	the index page of ephFrame applications.
</p>