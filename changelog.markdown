Official Changelog for ephFrame PHP Framework
==============================================================================

This file should record some kind of change history of the ephFrame Framework
Please check this file for hints after you updated the framework via svn to 
check some deprecated methods or new features.

# 2010-05-10

* Model hasOne/belongsTo was wrong when getting primary key values from associated models. Maybe you need to adjust your associations from belongsTo to hasOne now
* Model constructor accepts strings as primary keys now
* Positionable Behavior can be configured now
* Multiple DropDown Fields can select multiple values at once by calling select

# 2010-04-10

* Updated the default application index page and changed some default style sheets
* New Validator options available: `numeric`, `ip`, `between`, `alphaNumeric`, `hostname`
* New ModelBehavior: `Timestampable` that stores new timestamps or datetime values when updating or inserting new model data.
* Added `closeTags` to StringHelper that closes all opened tags in the passed string, also included in truncate method to close all cutted tags.
* Model->cacheQueries can turn query caching on and off
* Model->saveAll finally tries to save all associated model data form all association types like hasMany, belongsTo, hasOne and hasAndBelongsToMany
* Model->listAll now can have [modelName].[fieldname] as fieldname and also accepts array as first argument
* `DateTime` and `Date` form fields use I18n locale settings to format the date
* New Form Field for Float values: `FormFieldFloat`
* Changed all php short open tags to php echo, short open tags not on per default since php 5.3
* PHPINI::set does not fail but return false if ini_set is disabled

# 2010-02-28

## Notes
* created GIT-Repository to track little changes, hope to get a real GIT repo soon

## Enhancements
* Added Sluggable Behavior that can create slugs for every model with a various line of configuration vars. Check it out!
* I18n can translate plurals with __n, also substitution changed and bit refactored
* I18n works with real locale strings now, pass de_DE not only de
* ObjectSet now support findByProperty to search objects in Set that have a specific property value
* Model HABTM can have additional data in the join table
* Model also includes HABTM Models into default queries if field or conditions contain their name
* Router::uri() with no params returns current uri, same as url returns the whole current url
* Router replaces <P> notations as regexps too
* Controller->before[Action] now called with controllers parameters array
* Controller, Order of `beforeAction` calls changed
* Controller, before[ActionName] returns `false`, gets to 404 error page
* ArrayHelper::map maps a callback recursivly on every element

## Bugs
* Session start now just returns false and does not throw an error anymore (especially on Domainfactory Servers)
* Session.ttl now used in Session component (misspelling of config var)
* HTTPRequest now build correctly
* HTTPRequest - buildQuery now returns string including leading `?`
* Email Component can work with empty email attachments and uses theme correctly from controller
* InsertQuery quotes key names with `` (backticks)
* Model unbinds hasAndBelongsToMany Associated Models too
* Model returns also fields that have value = null not throwing an error anymore
* Model insert won't use `primaryKey = 0` if empty anymore, always insert if primary key is not set or empty
* Model fixed HABTM foreign key autocomplete when . is missing
* NestedSetBehavior won’t try to clean up children if lft and rgt are empty
* NestedSetBehavior level can be 0 and still works
* JS and CSS-Packer now create directories if they don't exist and also chmod them to 777 if not writable
* Form now consumes the row and cols attributes from model config
* Form afterConfig adds Submit button to form if missing, so it can be disabled by overwriting afterConfig in apps form classes
* HTTPRequests uses ArrayHelper::map to convert passed values to utf8 using recursive method, so form field with names like `note[23][de]` are possible
* ObjectSet could not benifit from reversed from IndexedArray because used wrong constructor, now works through copy method

## Deprecated
* I18n dropped __html method from translation, use Sanitize or own methods for this in your projects


# 2009-12-14
* Fixed usage of tablenamePrefix when using HABTM relations
* Router::uri and Router::uri merge default route config with passed $params array now
* Router: can use prefix for action
* Router: can use controllerPrefix for controller name parsed
* Router: parse action refactored
* Removed $debug var from Router::parse

# 2009-12-09
* Session.ttl can change session cookielifetime with ease now
* removed js-libs from js directory
* removed createapp.sh file and wrote a hint on how to create new apps into the README.txt file

# 2009-11-29
* Elements are called by ->element($name) instead of renderElement

# 2009-11-24
* Form Fields that are mandatory have 'mandatory' as class
* Form not having name="[classname]" anymore because was invalid
* View and layout use contentType set in controller
* HTTPHeader renders array of headers when passed to send
* Mandatory fields that are not from a model get mandatory = false now

# 2009-11-18 #
* changed code.ephigeniad.de to code.marceleichner.de
* CURL uses CONNECTTIMEOUT now
* I18n sets locale for usage in dates with LC_ALL as default
* I18n default language is now defined with I18n.language
* Session name is also stored in Session.name in config now
* Model->findAllby adds modelname to fieldname if field is column of model

# 2009-11-04 #
* Form->addField accepts label = false in the attributes to disable label usage
* String::substitue does not use \p{L} unicode class for replacement anymore
* Rendering and View filename finding in View Class refactored
* Elements that are missing throwing exceptions now
* Removed Bug where view is rendered twice!
* Changed code.moresleep.net back to code.marceleichner.de
* Javascript helper loads external urls before local files
* Fixed bug in IndexedArray->rand when returning 1 random element

# 2009-10-21 #
* added beforeRedirect callback that is called on every component before redirect headers are send in the controller, note that afterRender is _not_ called when a redirect is called
* removed $glue parameter from File->append
* Model->toArray accepts first parameter with fieldnames in it that will be used in array creation

# 2009-10-11 #
* ->unbind('all') removes all bindings from a model
* Directories can be created by simply calling ->create on them
* Image->resizeTo renamed to resize and Image->stretchResizeTo will be renamed to resizeCrop
* Dir->delete can delete directories recursivly

# 2009-10-08 #
* Fixed function call with reference in HTTPRequest and Cookie Class
* Added Paginator Helper that makes pagintion easier to use!
* Added and changed docu for HTML Helper
* Fixed String::prepend to use conditional string correctly
* moved ephFrame path decision from index.php ino ephFrame.php
* Added AppHelper class with various callbacks
* All basic components extend from AppComponent now
+ Added /console/console.php script that loads console tasks
* Helpers now can access controller by $this->controller if set
* HTML Helper uses $theme from controller to link images right

# 2009-09-17 #
* Dynamic Binding with bind() works again with aliases
* Some Email and CSV class related fixes

# 2009-09-11 #
* now php 5.3 compatible
  removed depreciated function calls (split -> explode)
  set_magic_quotes_runtime
* fixed bug where email validation fails on emails like l.name@host.com

# 2009-09-03 #
* Model COnditions like this array('User.id' => array('1','2','5')) will create a IN (1,2,5) query (no properly quoting yet!)
* Added Email Component from ephFrame 0.1 refactored
* Email Component logs emails to log directory if $delivery is set to 'debug' for testing
* Email can send UTF8 subjects now including german umlauts! (tested on OSX Mail and Webmail mobileme.com and iPhone)
* Email Component attach files that are not there:->attach($filename, $content)

# 2009-08-29 #
* added up and low as alias for upper and lower in String helper

# 2009-08-21 #
* moves isEmpty to core.php file so that the method is available everywhere
 also changed logic to it returns the first argument passed that is not empty() and renamed it to coalesce (removed old coalesce method)
* added whitelist filter to Hash class that removes all keys that are not in the whitelist of keys
* added uri and url method to router class
* added default HitCount Behavior to Behaviors that can increase single model fields when used (very usefull for increasing view + 1 f.e.)
* added Time::nice and niceShort method to replace timeAgoInWords soon
* styles of simpletest changed a bit

# 2009-08-13 #
* added Text Helper that replaces URLs and Emails in Text automatically with links
* added Security Component that limits actions to specific HTTP Methods
* dramatically decreased memory and compile time usage in model class that makes everything up to 50% faster

# 2009-08-12 #
* fixed missing created update field when inserting new model entries with multiple behaviors
* added theming, which includes js/css search in theme directory
* fixed js packer to append line-feed at end of packed js files
* added after[Action] and before[Action] callbacks in the controller
* added subversion keywords and new formatted copyright message on every file
* SGMLAttributes don’t render attributes with empty values anymore
* DBDSN in db.php can be an array, enabling socket connections with it
* missing afterAction callback for controller added (called after components)
* empty cookie values return false now
* fixed object->__mergeParentProperty to merge correctly using ->{$varname}
* File->basename(true) will return with extension and false without extension
* Dir->newFile can create new Files on the fly
* CSS/JS Packer use Dir class to create Packed files now
* added default view for missingtable error message
* form->configure uses varchar length from model fields as maxLength for input fields
* renamed Set class to IndexedArray!!!
* fixed issue with String::substr() on php 5.2.6
* imageconvolution added if missing in ImageSharpenFilter (needs high  performance!)
* image uses imageantialias only if function exists
* replaced %1% placeholders to :[name] in string::substitute
* added alias for string::substitute - string::insert
* new formating for files skipping ?> in files and no tabs in PHPDocBlocks
* Sanitizer::filename sanitizes filenames
* Controller->redirect added optional beforeRedirect method
* added String::prepend / append to add optional parts to a string
* router can have theme, layout as parameter to change them in controller
* added error message for layout missing exception
* view adds http content-type header only if missing
* model supports uniqueId method now (custom length)
* model supports hasAny now that returns true if conditions met that are passed to it
* httprequest->hostname now optional (saving performance if not needed)
* before/afterAction will not get the action parameter anymore (can be read 	 by $this->controller->action or $this->action)
* form: renamed configureModel to configure
* form $configure can contain form fields
* httpheader parses status code now, httpresponse variable name fixed
* model query and DBConnectionManager usage bit optimized (saving some RAM)
* added error message for not writable directores in Dispatcher
* added String::ifEmpty for default echo
* model name which is alias used in every query possible

# 2009-05-25 #
* added model behaviors callable from every model
* controller actions can return false to render 404 page
* added NestedSetBehavior, FlagableBehavior and PositionableBehavior
* added Form Field Classes and Form with configure
* Log Levels now used correctly
* Model can return random sets
* Model can return simple list of fields from table
* Model->findBy supports depth parameter
* Model query class caches query and results
* Model can now unbind and bind other models on the fly
* Image->stretchResizeTo fixed
* javascript and css component can include files from http://...
* optimized model - bind, create methods
* added CURL and Scraper Class
* app/config/paths.php included before frameworks paths.php
* Routes reusable with their names and parameters

# 2008-10-26 #
* changed the controller order actions to create components again
* created ModelStructureCache to handle the save and load stuff of a model (refactoring)
* modelfieldinfo now stores length (also float) and types correctly
* router copies same route names data from allready existing routes
* view renders all variables (to sad that the Hash seemes to have a bug on foreach sometimes?)
* added message to application default index if model cache dir does not 	
	exists or not writable
* enabled Log again
* controller now loads all components, then the models and then startups all components
* removed some log messages from controller and component
* model saved structure files are now read correctly (still not very cool implementation)
* Session loads up correctly (removed double load in controller)
* query history now skips the results part if there was just one

# 2008-10-24 #
* form adds default action now, WEBROOT+current url
* form fields now save their validate status (error message in formfield->error)
* added form field password 
* html helper now can create simple P-Elements
* new sequence of component, model init, controller sends startup to components now after model init and after all components are attached to controller
* validator can now replace some wilcard names (%name%) in the error messages
* set now supports isEmpty for checking if a index is empty or the hole set is "empty"
* missing view now renders the hole path to the missing view file
* introducing appForm standrad form class for applications
* javascript addFile now acts like addFiles with multiple arguments

# 2008-10-23 #
* controller send now content type from view class
* added xml view class
* added simple rss action for controller
* Validator fixed access to $validatorObject callback
* db-query values now not escaped (should be done by model now)
* added readme.txt dummy file
* added absolute url to registry

# 2008-10-21 #
* started rewrite of Form and FormField classes (new validator class)
* model uses the new validator class now
* added structure.sql file to /app/config/ to store table structures
* changed url parameter to __url in /webroot/.htaccess
* changed HTTPRequest to handle POST / GET Vars depending on the Request-Method
* fixed bug in model cache loading

# 2008-10-20 #
* added validation array that validates model data before saving
* added third parameter for save to save only specific fields of the model
* fixed/enhanced creation of resulting model set lists and single returns when retreiving model data
* added ModelBehavior to models that can act like plugins in model logic supporting callbacks for beforeSave, afterSave …