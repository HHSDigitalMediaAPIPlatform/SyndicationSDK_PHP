Synidcation SDK - PHP
==============

This is a php5 library for communicating with the Syndication APIs.
	
    CTAC Syndication 3.0 API. 
    CTAC CMS_Manager 3.0 API. 

Features
--------------

RESTful: All communication with a Syndication Server happens over Http using REST protocols.
	
cURL: Outgoing requests are generated with the curl library.

Media Browsing: Allows serachable, sortable, paginated access to all public data hosted by the syndication server.
 
Media Publishing: Allows a site to publish new content, making it accessable for viewing and subscriptions through the syndication server.

Media Subscriptions: Allows a site to receive notifications of changes to a content item, so you can automatically keep subscribed content up to date.

Requirements
--------------
PHP5 with cURL

Downloading the SDK
--------------

SDK available from our Git Repository:

	git clone git@bitbucket.org:ctacdevteam/syndicationsdk_php.git

Installing the SDK
--------------

There are three php class files required to use Syndication. They exist for use immediately and available under the src/ directory. They must simply be moved into php's include path.

```composer test```

Testing requires access to a live Syndication Server configured in the test/config.php file

```composer install``` 

Since php is not a compiled language, the install procedure just runs the tests, and if completed successfully, copies the src/ files into dist/. So during development you can be sure the dist/ dir contains the last set of functioning code. 

Documentation
--------------
	Detailed [per-function documentation](docs/) can be found within the docs/ directory of this repository

Example Application
--------------
	A Small [example application](examples/) can be found within the examples/ directory of this repository

Configuration
--------------

The main Syndication Class accepts api-configuration settings as a constructor parameter. The constructor parameter can be either an array of key value pairs, or a filepath to a config file. Config files can be in one of three formats. An includable php file that returns an array, an INI file, or a JSON file. A second optional constructor parameter lets you define a key within your configuration array where your syndication options live.

Basic examples:

```php
	$synd = new Syndication(array( 
		'syndication_base' 		=> 'http://.../Syndication/v2/api',
		'syndication_url' 		=> 'http://.../Syndication', 
		'syndication_tinyurl'	=> 'http://...', 
		'cms_manager_base' 		=> 'http://.../CmsManager/v1/api',
		'cms_manager_url'		=> 'http://.../CmsManager/v1/api',
	));
	$synd = new Syndication('./config.php');
	$synd = new Syndication('./config.json', 'synd' );
	$synd = new Syndication('./config.ini',    'synd' );
```

./config.php

```php
<?php 
	return array( 
		'syndication_base' 		=> 'http://.../Syndication/v2/api',
		'syndication_url' 		=> 'http://.../Syndication', 
		'syndication_tinyurl'	=> 'http://...', 
		'cms_manager_base' 		=> 'http://.../CmsManager/v1/api',
		'cms_manager_url'		=> 'http://.../CmsManager/v1/api',
	);
?>
```

./config.json

```json
{
	"foo": { "bar": "baz" }, 
	"synd":	{
		"syndication_base"		: "http://.../Syndication/v2/api",
		"syndication_url"		: "http://.../Syndication", 
		"syndication_tinyurl	: "http://...", 
		"cms_manager_base"		: "http://.../CmsManager/v1/api",
		"cms_manager_url"		: "http://.../CmsManager/v1/api",
	}
}
```

./config.ini

```ini
[foo]
bar:baz

;Syndication config options
[synd]
syndication_base	= http://.../Syndication/v2/api
syndication_url		= http://.../Syndication 
syndication_tinyurl	= http://... 
cms_manager_base	= http://.../CmsManager/v1/api
cms_manager_url		= http://.../CmsManager/v1/api
```

PHP Usage
--------------

Have Three Class Files : Syndication.class.php, SyndicationResponse.class.php,
Include Single Class File : Syndication.class.php

```php
  require('Syndication.class.php');
  $synd = new Syndication();
```

Common Return Class: SyndicationResponse.

All API calls return a common object. This object has a fixed set of properties. 

```php
  $resp = $synd->getMediaTypes();
    
  $resp->success;    // Boolean: was action completed
  $resp->results;    // Array containing returned data items
  
  $resp->pagination; // Details about paginated datasets
  $resp->status;     // Http status code of response
  $resp->format;     // Http content-type of response
  $resp->messages;   // Messages from server (mostly errors)
  $resp->raw;        // Content body of http response (incl. json)
```
Expected Usage

```php  
  $resp = $synd->getMediaTypes();

  if ( $resp->success )
  {
  	foreach ( $resp->results as $mediaType )
  	{
		echo $mediaType."\n";
  	} 
  } else {
  }
```

Application Mapping
--------------

Once data is retrieved, it is up to the user to map to a locally desired format.
SyndicationResponse results are always returned as a list of associative arrays.

```php
  $resp = $synd->getMediaTypes();
    
  foreach ( $resp->results as $type )
  {
  	echo "Type Name: {$type['name']}.<br />";
  	echo "Type Desc: {$type['description']}.<br />";  }
```
