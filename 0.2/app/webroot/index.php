<?php

/**
 *	This is the application main script
 * 
 * 	This is were everything about the application and the ephFrame Framework
 * 	is created. From the Request a dispatcher is initialized that creates
 * 	a controller from the url (if matched), that will create a view after
 * 	running the action that is requested and outputs this view as response.
 * 
 * 	@package ephFrame
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 03.12.2007
 */

/**
 * 	modify this path to point at the ephFrame root (where the startup.php
 * 	script is located, by default '../ephFrame')
 */
switch(get_current_user()) {
	default:
		require '../../ephFrame/startup.php';
		break;
}

/**
 *	Create the dispatcher that creates the controller ...
 */
ephFrame::loadClass('app.lib.AppDispatcher');
$dispatcher = new AppDispatcher();
$dispatcher->dispatch(new HTTPRequest(true));
exit;

?>