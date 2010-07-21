<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en_EN">
<head>
	<title><?php
		if (empty($pageTitle)) {
			echo $pageTitle;
		} else {
			echo '[no title]';
		}
	?></title>
</head>
<body class="en_EN">
	<div id="app">
		<div id="header">
			<h1><a href="<?php echo WEBROOT ?>" rel="index">ephFrame <?php echo ephFrame::VERSION ?></a></h1>
		</div>
		<div id="content">
			<?php echo @$content ?>
		</div>
		<div id="footer">
			© 2008+ <a href="http://code.marceleichner.de/projects/ephFrame/" rel="external" title="ephFrame">ephFrame <?php echo ephFrame::VERSION ?></a>
			by <a href="http://www.ephigenia.de/" title="Ephigenia M. Eichner" rel="external">Ephigenia M. Eichner</a>,
			licensed under <a href="http://www.opensource.org/licenses/mit-license.php" rel="external" title="MIT License">MIT License</a>
		</div>
	</div>
	<?php if (isset($JavaScript)) echo String::indent($JavaScript->render(), 2, TAB, 1).LF; ?>
</body>
</html>