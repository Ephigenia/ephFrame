<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo @$pageTitle ?: '[no title]' ?></title>
	<base href="<?php echo \ephFrame\core\Router::base(); ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo ephFrame\core\Router::base(); ?>/<?php echo $theme; ?>/css/app.css" />
</head>
<body>
	<div id="app">
		<div id="header">
			<h1><a href="<?php echo \ephFrame\core\Router::getInstance()->root; ?>"></a></h1>
		</div>
		<div id="content">
			<?php echo $content ?>
		</div>
	</div>
</body>
</html>