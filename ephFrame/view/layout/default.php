<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo @$pageTitle ?></title>
</head>
<body>
	<div id="app">
		<div id="header">
			<h1><a href="<?php echo WEBROOT ?>">ephFrame <?php echo ephFrame::VERSION ?></a></h1>
		</div>
		<div id="content">
			<?php echo @$content ?>
		</div>
		<div id="footer">
			© 2008+	<a href="http://www.ephigenia.de/" title="Marcel Eichner // Ephigenia" rel="external">Marcel Eichner // Ephigenia</a>,
			licensed under <a href="http://www.opensource.org/licenses/mit-license.php" rel="external" title="MIT License">MIT License</a>
		</div>
	</div>
	<?php if (isset($JavaScript)) echo String::indent($JavaScript->render(), 2, TAB, 1).LF; ?>
</body>
</html>