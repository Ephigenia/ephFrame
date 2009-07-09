<?php

/**
 * This is the application main script
 * 
 * This is were everything about the application and the ephFrame Framework
 * is created. From the Request a dispatcher is initialized that creates
 * a controller from the url (if matched), that will create a view after
 * running the action that is requested and outputs this view as response.
 * 
 * @package ephFrame
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 03.12.2007
 */

/**
 * modify the require instruction to include the startup.php file from the
 * ephFrame root. (in this example it’s depending on the current user name
 * for deploying on multiple servers)
 */
switch(get_current_user()) {
	case 'Ephigenia':
		require '../../ephFrame/startup.php';
		break;
	default:
		require '../../../ephFrame/startup.php';
		break;
}

/**
 * Create the dispatcher that creates the controller ... which will
 * start the hole ephFrame MVC-Pattern.
 */
ephFrame::loadClass('app.lib.AppDispatcher');
$dispatcher = new AppDispatcher();
$dispatcher->dispatch(new HTTPRequest(true));
exit;