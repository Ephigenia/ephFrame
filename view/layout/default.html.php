<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo @$pageTitle ?: '[no title]' ?></title>
	<base href="<?php echo \ephFrame\core\Router::base(); ?>">
	<style type="text/css" media="screen">
		body {
			font: normal 18px/28px Baskerville, Garamond, "Lucida Sans Unicode",Arial,Verdana,sans-serif;
			color: #4C4C4C;
			padding: 1em;
			background-color: #E6E6E6;
		}

		a, a:visited {
			color: #0080FF;
		}

		h1 {
			text-shadow: 0px 1px 1px rgba(0,0,0,0.25), 0px -1px 1px rgba(255,255,255,0.25);
			color: #000;
			font-weight: bold;
			font-size: 3em;
		}
	</style>
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