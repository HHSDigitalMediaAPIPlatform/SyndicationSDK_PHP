Synidcation SDK - PHP
==============

This is a php5 library for communicating with the Syndication APIs.
	CTAC Syndication 3.0 API. 
	CTAC CMS_Manager 3.0 API. 

Features
--------------

Rest: All communication with a Syndication Server happens over Http using REST protocols.
	
Curl: Outgoing requests are generated with the curl library.

Media Browsing: 

Media Publishing

Media Subscriptions

Requirements
--------------
PHP5
Curl
	

Downloading the SDK
--------------

Configuration
--------------

Application Mapping
--------------



PHP
--------------
Single Class File : Syndication.class.php

```php
  require('Syndication.class.php');
  $synd = new Syndication();
```

Common Return Class: SyndicationResponse

```php
  $resp = $synd->getMediaTypes();
  $resp = $synd->getLanguages();
  $resp = $synd->getTags();
    
  $resp->success;    // Boolean: was action completed
  $resp->results;    // Array containing returned data items
  
  $resp->pagination; // Details about paginated datasets
  $resp->status;     // Http status code of response
  $resp->format;     // Http content-type of response
  $resp->messages;   // Messages from server (mostly errors)
  $resp->raw;        // Content body of http response (incl. json)
```


API
--------------

  Client Website is registered to a CMS Manager belonging to Syndication.
  Client Website registers a callback URL for receviing updates and messages. 
  Client Webiste is assigned an Id, an API Key. 
  Client Website includes the API Key in a http header with each api call.
  
