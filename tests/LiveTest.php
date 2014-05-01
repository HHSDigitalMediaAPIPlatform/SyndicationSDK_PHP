<?php
ini_set('display_errors',1);error_reporting(E_ALL);

/*
define('INSIGHT_CONFIG_PATH', './package.json');
require_once('./lib/firephp/lib/FirePHP/Init.php');
$firephp = FirePHP::getInstance(true);
$firephp->log('Hello World');
*/

require_once('src/Syndication.class.php');

class LiveTest extends PHPUnit_Framework_TestCase
{

  protected static $tmp;
  protected static $api;  

  protected static $syn_base;  
  protected static $syn_url;  
  protected static $cms_base;  
  protected static $cms_url;  

  protected static $syndication;   
  protected static $http_methods;

  protected static $published_media;  
  protected static $existing_media;  
  protected static $existing_sources;
  protected static $existing_campaigns;

  public function __construct()
  {
    self::$tmp = realpath( dirname(__FILE__).'/../tmp/' );

    self::$api = include dirname(__FILE__).'/config.php';

    self::$syn_base = self::$api['syndication_base'];
    self::$syn_url  = self::$api['syndication_url'];
    self::$cms_base = self::$api['cms_manager_base'];
    self::$cms_url  = self::$api['cms_manager_url'];

    self::$http_methods = array('get','post','delete');

    self::$published_media  = null;
    self::$existing_media   = array();
    self::$existing_sources = array();
    self::$existing_campaigns = array();

  } 
  public static function setUpBeforeClass()
  {
    self::$syndication = new Syndication(self::$api);
  }
  public static function tearDownAfterClass()
  {
  }

  /// TEST CORE FUNCTIONALITY

  public function testInitialization ()
  { 
    $this->assertInstanceOf('Syndication',self::$syndication);

    $file_php  = fopen( self::$tmp.'/config.php',  'w' );
    fwrite( $file_php, "<?php return array(\n" );
    $file_ini  = fopen( self::$tmp.'/config.ini',  'w' );
    $file_json = fopen( self::$tmp.'/config.json', 'w' );
    fwrite( $file_json, "{\n" );
    $c = '';
    foreach ( self::$api as $k=>$v )
    {
        fwrite( $file_php,  "   {$c}\"{$k}\" => \"{$v}\"\n" );
        fwrite( $file_ini,  "{$k} = \"{$v}\"\r\n" );
        fwrite( $file_json, "   {$c}\"{$k}\" : \"{$v}\"\n" );
        $c = ',';
    }
    fwrite( $file_php, "\n);\n?>" );
    fclose( $file_php );
    fclose( $file_ini );
    fwrite( $file_json, "\n}" );
    fclose( $file_json );

    $synd_php  = new Syndication( self::$tmp.'/config.php' );
    $this->assertInstanceOf('Syndication',$synd_php);
    $this->assertEquals(  self::$syndication->api, $synd_php->api, 'Config should be loadable from a php file' );

    $synd_ini  = new Syndication( self::$tmp.'/config.ini' );
    $this->assertInstanceOf('Syndication',$synd_ini);
    $this->assertEquals(  self::$syndication->api, $synd_ini->api, 'Config should be loadable from a ini file' );

    $synd_json = new Syndication( self::$tmp.'/config.json' );
    $this->assertInstanceOf('Syndication',$synd_json);
    $this->assertEquals(  self::$syndication->api, $synd_json->api, 'Config should be loadable from a json file' );

  }

  public function testCurlRequest ()
  {
    /// must be able to generate a valid curl requests
    $url     = 'htp://www.dogs.com';
    $params  = http_build_query(array('a'=>'1'),'','&');
    $headers = array();
    $format  = 'json';
    
    foreach ( self::$http_methods as $http_method )
    {
        $curl = self::$syndication->apiBuildCurlRequest( $http_method, $url, $params, $headers, $format );
        $this->assertNotEmpty($curl);
        $this->assertInternalType('resource',$curl,strtoupper($http_method).' requsts must produce a resource');
        $this->assertEquals('curl',get_resource_type($curl),strtoupper($http_method).' requests Must produce a curl resource');
    }
  }

  public function testApiConnectivity ()
  {
    foreach ( self::$http_methods as $http_method )
    {
        $resp = self::$syndication->apiCall($http_method,self::$api['syndication_base'].'/api/v2/resources/media.json?max=1');

        /// all api calls must return an api_response array
        $this->assertNotEmpty($resp);
        $this->assertArrayHasKey( 'content',   $resp, strtoupper($http_method).' Response has "content" key holding actual response content'); 
        $this->assertArrayHasKey( 'format',    $resp, strtoupper($http_method).' Response has "format" key holding content format, if known'); 
        $this->assertArrayHasKey( 'http',      $resp, strtoupper($http_method).' Response has "http" key holding curl_info');

        /// good calls must return successful http status
        $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good '.strtoupper($http_method).' Response has "http_code" key holding http status code'); 
        $this->assertEquals('200',$resp['http']['http_code'], 'Good '.strtoupper($http_method).' Response has http code of 200');

        /// bad pathed calls must return notFound http status
        $resp = self::$syndication->apiCall(strtoupper($http_method),self::$syn_url.'/404');
        $this->assertNotEmpty($resp);
        $this->assertArrayHasKey( 'http',      $resp,            'Bad '.strtoupper($http_method).' Response has "http" key holding curl_info'); 
        $this->assertArrayHasKey( 'http_code', $resp['http'],    'Bad '.strtoupper($http_method).' Response has "http_code" key holding http status code'); 
        $this->assertNotEquals('200',$resp['http']['http_code'], 'Bad '.strtoupper($http_method).' Response does not have an http code of 200');
    }
  }
 
  public function testApiVersion ()
  {
      $serverApi = self::$syndication->getServerApiVersion();
      $this->assertNotEmpty($serverApi);
      $clientApis = self::$syndication->getClientApiVersions();
      $can_speak = in_array( $serverApi, $clientApis );
      $this->assertTrue( $can_speak, 'Client api must list the server\'s api version, to indicate they can speak together' );
  }

  public function testApiKey()
  {
      $resp = self::$syndication->apiCall('get',self::$api['cms_manager_url'].'/debug/secure/resource');

      /// good response must be 200 
      $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
      $this->assertEquals('200',$resp['http']['http_code'], 'Good Response has http code of 200');
  }

  public function testPublish ()
  {
    $random_id = mt_rand(1000,9999);
    $params = array(
        'mediaType' => 'Html', 
        'name'      => 'PHPClient Test Publish '.$random_id, 
        'sourceUrl' => 'http://www.stopmedicarefraud.gov/newsroom/your-state/texas/index.html#'.$random_id,
        'language'  => '1',   
        'source'    => '5'
    );

    $resp = self::$syndication->publishMedia($params);

    /// good publish
    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key '); 
    $this->assertEquals(                     1, count($resp->results),  'Results should only have one result');
    $this->assertArrayHasKey(                0, $resp->results,         'Response[results] requires "0" key'); 
    $this->assertArrayHasKey(             'id', $resp->results[0],      'Results[0] requires "id" key'); 
    $this->assertTrue(             is_numeric($resp->results[0]['id']), 'Results[0][id] is numeric');

    self::$published_media = $resp->results[0];

    /// bad publish - missing required parameter
    unset($params['language']);
    $resp = self::$syndication->publishMedia($params);
    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute(   'status', $resp,                  'Response requires "status" attribute'); 
    $this->assertEquals(                  '400', $resp->status );
    $this->assertObjectHasAttribute( 'messages', $resp,                  'Response->meta requires "message" attribute'); 
    $this->assertGreaterThan(                 1, count($resp->messages), 'Response->meta has a message'); 
    $this->assertArrayHasKey(    'errorMessage', $resp->messages[1],     'Response->messages[1] requires "errorMessage" attribute'); 
    $this->assertEquals( 'Field Constraint Violation', $resp->messages[1]['errorMessage'], 'Response->messages[1][errorMessage] is "Field Contraint Violation"'); 
  } 

  public function testBrowse()
  {
    
    $params = array(
        'max' => 1
    );
    $resp = self::$syndication->getMedia( $params );

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute');
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key ');
    $this->assertEquals(                     1, count($resp->results),  'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,         'Response[results] requires "0" key');
    $this->assertArrayHasKey       (      'id', $resp->results[0],      'Results[0] requires "id" key');
    $this->assertNotEmpty          (      'id', $resp->results[0],      'Results[0][id] is not empty');

    self::$existing_media = $resp->results;

   }

  public function testLookup()
  {
    $this->assertArrayHasKey( 0, self::$existing_media, 'Existing_Media should not be empty, requires "0" key');

    $resp = self::$syndication->getMediaById( self::$existing_media[0]['id'] );
 
    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key '); 
    $this->assertEquals(                     1, count($resp->results),  'Results should only have one result');
    $this->assertArrayHasKey       (         0, $resp->results,         'Response[results] requires "0" key'); 
    $this->assertArrayHasKey       (      'id', $resp->results[0],      'Results[0] requires "id" key'); 
    $this->assertEquals( self::$existing_media[0]['id'], $resp->results[0]['id'], 'Results[0][id] should match requested id');
  }

  public function testSearch()
  {
    $resp = self::$syndication->getMedia( array('q'=>'the') );

    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key '); 
    $this->assertGreaterThan(                0, count($resp->results),  'Results should have at least one result');

    $resp = self::$syndication->getMedia( array('descriptionContains'=>'health') );

    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key '); 
    $this->assertGreaterThan(                0, count($resp->results),  'Results should have at least one result');

  }

 /// test each API call

  public function testGetMediaTypes()
  {
    
    $resp = self::$syndication->getMediaTypes();

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key '); 
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key'); 
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key'); 
    $this->assertNotEmpty          (            $resp->results[0]['name'], 'Results[0][name] is not empty');

  }
  
  public function testGetSources()
  {
    
    $resp = self::$syndication->getSources();

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute');
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key');
    $this->assertArrayHasKey       (      'id', $resp->results[0],         'Results[0] requires "id" key');
    $this->assertNotEmpty          (            $resp->results[0]['id'],   'Results[0][id] is not empty');
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key');
    $this->assertNotEmpty          (            $resp->results[0]['name'], 'Results[0][name] is not empty');

    self::$existing_sources = $resp->results;

  }

  public function testLimitOffsetSort()
  {
    /// LIMIT TEST
    $params = array(
        'max' => '2'
    );
    $resp = self::$syndication->getSources($params);
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertEquals(                     2, count($resp->results),     'Results should contain two items');

    /// OFFSET TEST
    $params = array(
        'max'    => '1',
        'offset' => '1'
    );
    $resp = self::$syndication->getSources($params);
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');
    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key');
    $this->assertEquals( self::$existing_sources[1]['id'], $resp->results[0]['id'],   'Results should contain second item');

    /// SORTING TEST
    $params1 = array(
        'sort' => 'id',
    );
    $resp1 = self::$syndication->getSources($params1);
    $this->assertEquals(                 '200', $resp1->status );
    $this->assertObjectHasAttribute( 'results', $resp1,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp1->results,            'Results should only have one result');
    $this->assertArrayHasKey       (         0, $resp1->results,            'Response[results] requires "0" key');
 
    $params2 = array(
        'sort' => '-id',
    );
    $resp2 = self::$syndication->getSources($params2);
    $this->assertEquals(                 '200', $resp2->status );
    $this->assertObjectHasAttribute( 'results', $resp2,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp2->results,            'Results should only have one result');
    $this->assertArrayHasKey       (         0, $resp2->results,            'Response[results] requires "0" key');

    $sort1 = array_pop(   $resp1->results ); /// last item here
    $sort2 = array_shift( $resp2->results ); /// first item here
    $this->assertEquals( $sort1, $sort2, 'Sorting should put same element at beginning of ascending list and end of descending list' );

  }

  public function testGetSourceById()
  {
    if ( empty(self::$existing_sources) ) { return; }

    $resp = self::$syndication->getSourceById( self::$existing_sources[0]['id'] );

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key '); 
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key'); 
    $this->assertArrayHasKey       (      'id', $resp->results[0],         'Results[0] requires "id" key'); 
    $this->assertEquals            ( self::$existing_sources[0]['id'],   $resp->results[0]['id'],   'Results[0][id] is not empty');
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key'); 
    $this->assertEquals            ( self::$existing_sources[0]['name'], $resp->results[0]['name'], 'Results[0][name] is not empty');

  }

  public function testGetCampaigns()
  {
    
    $resp = self::$syndication->getCampaigns();

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute');
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key');
    $this->assertArrayHasKey       (      'id', $resp->results[0],         'Results[0] requires "id" key');
    $this->assertNotEmpty          (            $resp->results[0]['id'],   'Results[0][id] is not empty');
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key');
    $this->assertNotEmpty          (            $resp->results[0]['name'], 'Results[0][name] is not empty');

    self::$existing_campaigns = $resp->results;

  }
 
  public function testGetCampaignById()
  {
    if ( empty(self::$existing_campaigns) ) { return; }

    $resp = self::$syndication->getCampaignById( self::$existing_campaigns[0]['id'] );

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key '); 
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key'); 
    $this->assertArrayHasKey       (      'id', $resp->results[0],         'Results[0] requires "id" key'); 
    $this->assertEquals            ( self::$existing_campaigns[0]['id'],   $resp->results[0]['id'],   'Results[0][id] is not empty');
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key'); 
    $this->assertEquals            ( self::$existing_campaigns[0]['name'], $resp->results[0]['name'], 'Results[0][name] is not empty');

  }

  public function testGetMostPopularMedia() 
  { 
    $resp = self::$syndication->getMostPopularMedia();

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute');
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp->results,            'Results should only have one result');

    $this->assertArrayHasKey       (         0, $resp->results,            'Response[results] requires "0" key');
    $this->assertArrayHasKey       (      'id', $resp->results[0],         'Results[0] requires "id" key');
    $this->assertNotEmpty          (            $resp->results[0]['id'],   'Results[0][id] is not empty');
    $this->assertArrayHasKey       (    'name', $resp->results[0],         'Results[0] requires "name" key');
    $this->assertNotEmpty          (            $resp->results[0]['name'], 'Results[0][name] is not empty');
  }

  public function testGetMediaContentById() 
  { 
    if ( empty(self::$existing_media) ) { return; }

    $resp = self::$syndication->getMediaContentById( self::$existing_media[0]['id'] );

    $this->assertObjectHasAttribute(  'status', $resp, 'Response requires "status" attribute');
    $this->assertEquals(                 '200', $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response requires has "results" key ');
    $this->assertNotEmpty(                      $resp->results,            'Results should have results');
   
  }

  public function _testGetMediaEmbedById() { }
  public function _testGetMediaPreviewById() { }
  public function _testGetMediaRatingsById() { }
  public function _testGetRelatedMediaById() { }

  public function _testGetMediaHtmlById() { }
  public function _testGetMediaThumbnailById() { }
  public function _testGetMediaYoutubeMetaDataById() { }



/*

  public function _testSubscribe ()
  {
    $resp = self::$syndication->subscribeById(201);
    $this->assertNotEmpty($resp);

    /// 201 success
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" key'); 
    $this->assertEquals(             '201',     $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                     'Response has "results" key '); 
    $this->assertArrayHasKey       ( 'mediaId', $resp->results,            'Results has "mediaId" key'); 
    $this->assertEquals(             '201',     $resp->results['mediaId'], 'Results[mediaId] == 201');
    /// 401 unauthorized
    $resp = self::$syndication->subscribeById(401);
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" key'); 
    $this->assertEquals(             '401',     $resp->status );
    /// 404 id not found 
    $resp = self::$syndication->subscribeById(404);
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" key'); 
    $this->assertEquals(             '404',     $resp->status );
    /// 500 server error
    $resp = self::$syndication->subscribeById(500);
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" key'); 
    $this->assertEquals(             '500',     $resp->status );
  } 


  /**
   * @depends testInitialization
   * /
  public function testGetAllMediaTypes ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->getAllMediaTypes();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertArrayHasKey( 'format', $resp['meta'], 'Good Response has "format" key holding content format'); 
    $this->assertEquals('json',$resp['meta']['format']);
    $expected = array( "Collection", "SocialMedia", "Audio", "Widget", "Infographic", "Image", "Video", "Html" );
    $this->assertEquals($expected,$resp['results'],'Good response matches expected set of 8 mediaTypes');
  }


  /**
   * @depends testInitialization
   * /
  public function testGetAllOrganizations ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->getAllOrganizations();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
  }

  /**
   * @depends testInitialization
   * /
  public function testGetOrganizationById ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->getOrganizationById(1);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertArrayHasKey( 'pagination', $resp['meta'], 'Good Response has "pagination" key under meta'); 
    $this->assertArrayHasKey( 'total',      $resp['meta']['pagination'], 'Good Response has "total" key under pagination'); 
    $this->assertEquals(1,$resp['meta']['pagination']['total'],'Should only paginate one result');
    $this->assertEquals(1,count($resp['results']),'Should only return one result');
    $this->assertEquals('1',$resp['results'][0]['id'],'Should return Id 1');
    
    $resp = $syndication->getOrganizationById('missing');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertEquals(0,count($resp['results']),'Should have zero results');
  }

  /**
   * @depends testInitialization
   * /
  public function testGetAllLanguages ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->getAllLanguages();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
  }

  /**
   * @depends testInitialization
   * /
  public function testGetLanguageById ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->getLanguageById(1);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertArrayHasKey( 'pagination', $resp['meta'], 'Good Response has "pagination" key under meta'); 
    $this->assertArrayHasKey( 'total',      $resp['meta']['pagination'], 'Good Response has "total" key under pagination'); 
    $this->assertEquals(1,$resp['meta']['pagination']['total'],'Should only paginate one result');
    $this->assertEquals(1,count($resp['results']),'Should only return one result');
    $this->assertEquals('1',$resp['results'][0]['id'],'Should return Id 1');
    
    $resp = $syndication->getLanguageById('missing');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertEquals(0,count($resp['results']),'Should have zero results');
  }
  /* */
}

?>
