<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo @$pageTitle ?></title>
	<?php
	if (isset($MetaTags)) echo String::indent($MetaTags->render(), 2, TAB, 1);
	if (isset($CSS)) {
		$CSS->addFiles(array(
			'reset',
			'app', 
			'debug',
		));
		echo $CSS->render();
	}
       ?>
	<link rel="shortcut icon" type="image/ico" href="<?php echo WEBROOT ?>favicon.ico" />
</head>
<body>
	<div id="app">
		<div id="header">
			<h1><a href="<?php echo WEBROOT ?>">ephFrame <?php echo ephFrame::VERSION ?></a></h1>
		</div>
		<?php echo $this->element('flashMessage') ?>
		<div id="content">
			<?php echo @$content ?>
		</div>
		<div id="footer">
			© 2008+ <a href="http://code.marceleichner.de/projects/ephFrame/" rel="external" title="ephFrame">ephFrame <?php echo ephFrame::VERSION ?></a>
			by <a href="http://www.ephigenia.de/" title="Ephigenia M. Eichner" rel="external">Ephigenia M. Eichner</a>,
			licensed under <a href="http://www.opensource.org/licenses/mit-license.php" rel="external" title="MIT License">MIT License</a>
		</div>
		<?php echo $this->element('debug/dump'); ?>
	</div>
	<?php
	if (isset($JavaScript)) {
		$JavaScript->addFiles(array(
			'http://code.jquery.com/jquery-1.4.2.min.js',
			'app',
		));
		echo $JavaScript->render();
	}	
	?>
</body>
</html>