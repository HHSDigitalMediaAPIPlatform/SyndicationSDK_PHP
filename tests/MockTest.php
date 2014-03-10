<?php
ini_set('display_errors',1);error_reporting(E_ALL);

require_once('src/Syndication.class.php');

class BGProcess
{
    public $cmd;
    public $run;
    public $dir;
    public $pid;
    public function __construct($cmd,$dir=null){$this->cmd=$cmd;$this->dir=$dir;}
    public function start($out='/dev/null')
    {
        /// check if command is already running - return true and skip pid 
        // shell_exec('ps auxwww | grep "'.$this->cmd.'"');
        if($this->dir){$cwd=getcwd();chdir($this->dir);}
        //$this->run=sprintf('%s > %s 2>&1 & echo $!',$this->cmd,$out);
        $this->run=sprintf('nohup %s > %s 2>&1 & echo $!',$this->cmd,$out);
        $this->pid=trim(shell_exec($this->run));
        if($this->dir){chdir($cwd);}
    }
    public function isRunning(){try{$r=shell_exec(sprintf('ps %d',$this->pid));return(count(explode("\n",$r))>2);}catch(Exception $e){};return false;}
    public function stop(){if(!$this->pid){return true;}shell_exec(sprintf('kill %d',$this->pid));return $this->running();}
}


class SyndicationMockTest extends PHPUnit_Framework_TestCase
{

  protected static $api;  

  protected static $syn_url;  
  protected static $cms_url;  
  protected static $pub_url;

  protected static $syndication;   
  protected static $http_methods;  

  public function __construct()
  {
    self::$syn_url = 'http://localhost:3000';
    self::$cms_url = 'http://localhost:3000';

    self::$api = array(
        'syndication_url'     => self::$syn_url,
        'syndication_tinyurl' => '',
        'cms_manager_url'     => self::$cms_url,
        'cms_manager_id'      => 'client',
        'key_shared'  => "Os4LfYrbPucQIyyhHv+A8o120BHb+nVNKJolgPtnrhQRwdW8jj4oDmV446222ZjLNxaLwYrEQ9qXLPQuqhwCYA==",
        'key_public'  => "TZa2AxkrMMHiSYIuHiaUOIBkoVl2fAmldxD3BV6sSGMShiApQYhASxxYlhEFJx2XCYOZ1x/tgYsqpa2E+4Yq7g==", 
        'key_private' => "aEoiQjg+qu7C9Nx1UUT6SRLfi13a6IEcjcGz82VZD0eJB0pE23knOnWHQ/YC5ZpFgTQa92iVDLrLUVcLfi8V5A==",
        /* 
        'key_shared'  => 'xjY3i4AnsZ9wWuDKboD1XbAdtX1hgOh2tYMnwCWnXhweO94IKrbVJuPZIQsyO5Sa40CjAMF9tG5ciI+cXITjVw==',
        'key_public'  => '9k+x8vDQJBcEYcEb/y/iipg8kXMU7sFfk1klV3PqMZkUBuJ/rDgVZtHmJGBydEKfnGKPAf6y9DBb7a+1tAz9Bg==', 
        'key_private' => 'UxMt4OpdAZhJFMOF/kmv/lZAYXjE4hV8EI9UdmQP71J9VbbIvmR0DEhX2D3He7AKTq1IQz4tAqDX+Jy1Svxlqw==',
        /*
        'key_shared'  => 'KEap7FOUphaeeE8J8ZGxyPej9+cY9r47St2gLy94RE6pJu1DTqMTUIH/+HKhrSw0lGHmSEaRj9k9Rz5mhQD/PQ==',
        'key_public'  => 'AMqq6mm6/vM+OsDYu3vS00IzF0m244N0H/sBV1K2+E5FBG4jLouGgt89ueQr1i3xvZZFK5myHPOHLslyomqxlJM=',
        'key_private' => 'W5vQAky0Ys6uCx7esGcLy9NTOEb5ePPwiJ7L9KZzGyOvJOi7ZPV6pEn5nGytiBrHKszt7hQZh2dG+Oh4pe299g=='
        */

    );

    self::$pub_url = 'http://'.gethostbyname(trim(`hostname`)).'3333';

    self::$http_methods = array('get','post','delete');
  } 
  public static function setUpBeforeClass()
  {
    self::$syndication = new Syndication(self::$api);
  }
  public static function tearDownAfterClass()
  {
  }

  public function testInitialization ()
  {
    $this->assertInstanceOf('Syndication',self::$syndication);
  }

  public function testCurlRequest ()
  {
    /// must be able to generate a valid curl requests
    $url     = self::$pub_url.'/single.html';
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

  public function _testApiConnectivity ()
  {
    foreach ( self::$http_methods as $http_method )
    {
        $resp = self::$syndication->apiCall($http_method,self::$api['syndication_url'].'/resources');
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
        $this->assertArrayHasKey( 'http',      $resp,         'Bad '.strtoupper($http_method).' Response has "http" key holding curl_info'); 
        $this->assertArrayHasKey( 'http_code', $resp['http'], 'Bad '.strtoupper($http_method).' Response has "http_code" key holding http status code'); 
        $this->assertNotEquals('200',$resp['http']['http_code'], 'Bad '.strtoupper($http_method).' Response does not have an http code of 200');
    }
  }
 
  public function _testApiKey()
  {
        $resp = self::$syndication->apiCall('get',self::$api['cms_manager_url'].'/secure/echo');
        /// good response must be 200 
        $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
        $this->assertEquals('200',$resp['http']['http_code'], 'Good Response has http code of 200');
  }

  public function _testLookup()
  {
  }
  public function _testSearch()
  {
  }

  public function _testPublish ()
  {
    $params = array(
        'mediaType'     => 'Html', 
        'name'          => 'return_type', 
        'sourceUrl'     => self::$pub_url.'/single.html', 
        'dateSyndicationVisible'  => gmdate('Y-m-d\TH:i:s\Z'), 
        'dateSyndicationCaptured' => gmdate('Y-m-d\TH:i:s\Z'),
        'dateSyndicationUpdated'  => gmdate('Y-m-d\TH:i:s\Z'),
        'language'      => '1',   
        'organization'  => '1'
    );

    $params['name'] = 'success';
    $resp = self::$syndication->publishMedia($params);

    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(             '200',     $resp->status );
    $this->assertObjectHasAttribute( 'results', $resp,                  'Response requires has "results" key '); 
    $this->assertEquals(             1,         count($resp->results),  'Results should only have one result');
    $this->assertArrayHasKey       ( 0,         $resp->results,         'Response[results] requires "0" key'); 
    $this->assertArrayHasKey       ( 'id',      $resp->results[0],      'Results[0] requires "id" key'); 
    $this->assertTrue(             is_numeric($resp->results[0]['id']), 'Results[0][id] is numeric');

    $params['name'] = 'serverError';
    $resp = self::$syndication->publishMedia($params);
    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute( 'status',  $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(             '500',     $resp->status );

    $params['name'] = 'invalidData';
    $resp = self::$syndication->publishMedia($params);
    $this->assertNotEmpty($resp);
    $this->assertObjectHasAttribute( 'status',   $resp, 'Response requires "status" attribute'); 
    $this->assertEquals(             '400',      $resp->status );
    $this->assertObjectHasAttribute( 'messages', $resp, 'Response->meta requires "message" attribute'); 
    $this->assertGreaterThan(        1,          count($resp->messages), 'Response->meta has a message'); 
    $this->assertArrayHasKey(    'errorMessage', $resp->messages[1], 'Response->messages[0] requires "errorMessage" attribute'); 
    $this->assertEquals( 'Field Constraint Violation', $resp->messages[1]['errorMessage'], 'Response->messages[0][errorMessage] is "Field Contraint Violation"'); 
  }

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

/*

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
