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
 * load ephFrame
 */
require dirname(__FILE__).'/ephFrame.php';

/**
 * Create the dispatcher that creates the controller ... which will
 * start the hole ephFrame MVC-Pattern.
 */
$dispatcher = Library::create('app.lib.AppDispatcher');
$dispatcher->dispatch(new HTTPRequest(true));
exit;