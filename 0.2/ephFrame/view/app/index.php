<h1>Congratulations!</h1>
<p>
	You’ve made it! You successfully installed and configured ephFrame on this server!<br />
	<br />
	If you need some help: <a href="http://code.moresleep.net/project/ephFrame/" title="visit project page of ephFrame">code.moresleep.net/project/ephFrame/</a><br />
</p>
<?php
// check for writable log directory
if (!is_writable(LOG_DIR)) {
	echo $this->renderElement('errorMessage', array('message' => 'Your tmp directory is not writable.'));
}
// check for database Config
if (!file_exists(CONFIG_DIR.'db.php')) {
	echo $this->renderElement('errorMessage', array('message' => 'No database configuration file found. Please create \''.CONFIG_DIR.'db.php\''));
} elseif (!class_exists('DBConfig')) {
	echo $this->renderElement('errorMessage', array('message' => 'db.php seemes to be included but no database config found.'));
}
?>
<p class="hint">
	You can modify this view by changing the layout '/app/view/layout/default.php' template file
	or create your own content file by creating the file '/app/view/app/index.php' which is always
	the index page of ephFrame applications.
</p>