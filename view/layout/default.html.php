<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo @$pageTitle ?: '[no title]' ?></title>
	<base href="<?php echo \ephFrame\core\Router::base(); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo ephFrame\core\Router::base(); ?>/css/app.css">
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