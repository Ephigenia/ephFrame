<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?= (isset($pageTitle) ? $pageTitle : ''); ?></title>
		<?php
		if (isset($MetaTags)) echo String::indent($MetaTags->render(), 2);
		if (isset($CSS)) echo String::indent($CSS->render());
		if (file_exists('./favicon.ico')) {
			echo TAB.TAB.'<link rel="shortcut icon" type="image/ico" href="'.WEBROOT.'favicon.ico" />'.LF;
		}
		if (file_exists('./apple-touch-icon.png')) {
			echo TAB.TAB.'<link rel="apple-touch-icon" type="image/x-icon" href="'.WEBROOT.'apple-touch-icon.png" />'.LF;
		}
        ?>
        <!--[if IE 8]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
	</head>
	<body>
		<div id="app">
			<div id="header">
				<a href="<?= WEBROOT ?>">ephFrame <?= ephFrame::VERSION ?></a>
			</div>
			<div id="content">
				<?= (isset($content)) ? $content : '' ?>
			</div>
			<div id="footer">
				© 2008+ <a href="http://www.nomoresleep.net/" title="nomoresleep">NMS</a>,
				licensed under <a href="http://www.opensource.org/licenses/mit-license.php">MIT License</a>
			</div>
		</div>
		<?php if (isset($JavaScript)) echo String::indent($JavaScript->render(), 2); ?>
	</body>
</html>