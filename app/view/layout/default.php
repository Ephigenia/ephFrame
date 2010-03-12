<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?= @$pageTitle ?></title>
		<!--[if IE 8]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
		<?php
		if (isset($MetaTags)) echo String::indent($MetaTags->render(), 2, TAB, 1);
		if (isset($CSS)) {
			$CSS->addFiles('reset', 'app', 'form');
			echo String::indent($CSS->render(), 2, TAB, 1);
		}
        ?>
		<link rel="shortcut icon" type="image/ico" href="<?= WEBROOT ?>favicon.ico" />
	</head>
	<body>
		<div id="app">
			<div id="header">
				<h1><a href="<?= WEBROOT ?>">ephFrame <?= ephFrame::VERSION ?></a></h1>
			</div>
			<?= $this->element('flashMessage') ?>
			<div id="content">
				<?= @$content ?>
			</div>
			<div id="footer">
				© 2008+ <a href="http://www.ephigenia.de/" title="Marcel Eichner // Ephigenia" rel="external">Marcel Eichner // Ephigenia</a>,
				licensed under <a href="http://www.opensource.org/licenses/mit-license.php" rel="external" title="MIT License">MIT License</a>
			</div>
		</div>
		<?php if (isset($JavaScript)) echo String::indent($JavaScript->render(), 2, TAB, 1).LF; ?>
	</body>
</html>