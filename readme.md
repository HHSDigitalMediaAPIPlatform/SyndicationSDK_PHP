Synidcation SDK - PHP
==============

This is a php library for communicating with the Syndication APIs.
  CTAC Syndication 3.0 API. 
  CTAC CMS_Manager 3.0 API. 

-- PHP5

PHP5
--------------
Single Class File : Syndication.class.php
```php
  require('Syndication.class.php');
  $synd = new Syndication();
  $synd->getAllLanguages();
```

API
--------------

  Client Website is registered to a CMS Manager belonging to Syndication.
  Client Website registers a callback URL for receviing updates and messages. 
  Client Webiste is assigned an Id, an API Key. 
  Client Website includes the API Key in a http header with each api call.
  
