ephFrame PHP Framework Readme File
==============================================================================

ephFrame is an other framework written in PHP5 and wants to be easy to use,
simple and not to heavy weighted. It focuses on
[DRY](http://en.wikipedia.org/wiki/Don't_repeat_yourself) and
[convention over configuration](http://en.wikipedia.org/wiki/Convention_over_configuration) principle. It's build like many other frameworks with some 
differences in detail and also uses the [MVC pattern](http://de.wikipedia.org/wiki/Model_View_Controller).

See the [WIKI Documentation](http://github.com/Ephigenia/ephFrame/wiki), which i just started writing. If you’re 
allready working with the framework you should check the 
[changelog.markdown](http://github.com/Ephigenia/ephFrame/blob/master/changelog.markdown) from time to time.

Features
------------------------------------------------------------------------------
So far the basic features ephFrame has to offer are listed here:

* MVC Architecture
	* Controller
		* Request / Response objects (including Headers)
		* Dynamic Callbacks
		* Scaffolding via Component
	* Model
		* Active Record & ORM
		* Query Cache
		* Behaviors
			* NestedSet (Tree-Implementation)
			* Flagable
			* HitCount (increasing numbers)
			* Positionable (Sorting)
			* Sluggable (URI-creation)
			* Timestampable (Update, Created-Auto-Columns)
			* Versionable
	* Views
		* Elements (DRY)
		* Javascript & CSS with packing/minify
		* HTML Helper for valid (X)HTML tags
		* Pagination Helpers
		* Text helpers with more, excerpt and auto-linking
* Components
	* Session
	* Cookie
	* MetaTags
	* EMail (with templates)
	* OS / Browser for client negotiation
	* Socket, Scraper and CURL wrapper
	* FileStorage (saving of files in distributes folders)
* Utils
	* Hashed & Indexed Arrays
	* Sanitizing
	* Filtering
	* Validator
	* Inflector (plural/Singular)
* File Handling
	* Images
		* Image (resize/crop)
		* Color Manipulation with Filters
	* CSV Files
* Console Tasks
	* Use all models, components and helpers in console tasks
	* Simple to use arguments and options implemented with [PHPOptParse](http://github.com/Ephigenia/PHPOptParse)
	* Advanced Progress bars with console drawing
* Logging
* I18n with gettext
	* gettext