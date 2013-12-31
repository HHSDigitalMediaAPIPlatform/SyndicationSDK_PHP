<?php
ini_set('display_errors',1);error_reporting(E_ALL);

require('src/Syndication.class.php');


class SyndicationTest extends PHPUnit_Framework_TestCase
{

  public function testInitialization ()
  {
    $syndication = new Syndication(array(
            'url'      => 'http://localhost:3000/Syndication/api/v1/resources',
       		'tiny_url' => 'http://localhost:3000/',
            'cms_url'  => 'http://localhost:3000/CMS_Manager/api/v1/resources',
            'cms_id'   => 'drupal_cms_1',
            'api_key'  => 'TEST_CMS1',
            'timeout'  => 60
    ));
    $this->assertInstanceOf('Syndication',$syndication);
    return $syndication;
  }
  
  /**
   * @depends testInitialization
   */
  public function testCurlOutgoingRequest ( Syndication $syndication )
  {

    $type    = 'post'; 
    $url     = 'http://ctacdev.com:8090/Syndication/api/v1/resources/media/1.json';
    $params  = array('a'=>'1');    
    $headers = array();
    $format  = 'json';

    $content_length = strlen( http_build_query( $params, '', '&' ) );
    
    $res = $syndication->apiCall( $type, $url, $params, $headers, $format );
    $this->assertNotEmpty($res);
  }

  /**
   * @depends testInitialization
   */
  public function testApiCall ( Syndication $syndication )
  {
    $resp = $syndication->apiCall('get','http://localhost:3000/200');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'content',   $resp, 'Good Response has "content" key holding actual response content'); 
    $this->assertArrayHasKey( 'format',    $resp, 'Good Response has "format" key holding content format, if known'); 
    $this->assertArrayHasKey( 'http',      $resp, 'Good Response has "http" key holding curl_info'); 
    $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
    $this->assertEquals('200',$resp['http']['http_code']);
    return $syndication;
  }
  
  /**
   * @depends testApiCall
   * /
  public function testPublishHtml ( Syndication $syndication )
  {
    //$syndication       = new Syndication();
    $sourceUri  = 'http://'. gethostbyname(trim(`hostname`)) .':3001';
    $sourceUri .= '/single.html';
    $params = array(
        'mt'            => 'Html', 
        'name'          => 'TestName', 
        'sourceUri'     => $sourceUri, 
        'dateAuthored'  => gmdate('Y-m-d\TH:i:s\Z'), 
        'dateUpdated'   => gmdate('Y-m-d\TH:i:s\Z'),
        'language'      => '1',   
        'organization'  => '1'
    );
    $resp = $syndication->publish($params);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',    $resp,         'Response requires "meta" key'); 
    $this->assertArrayHasKey( 'status',  $resp['meta'], 'Response requires "status" key'); 
    $this->assertEquals(      '200',     $resp['meta']['status'] );
    $this->assertArrayHasKey( 'results', $resp,                   'Response requires has "results" key '); 
    $this->assertEquals(      1,         count($resp['results']), 'Results should only have one result');
    $this->assertArrayHasKey( 0,         $resp['results'],        'Response[results] requires "0" key'); 
    $this->assertArrayHasKey( 'id',      $resp['results'][0],     'Results[0] requires "id" key'); 
    $this->assertTrue(        is_numeric($resp['results'][0]['id']), 'Results[0][id] is numeric');
  }

  /**
   * @depends testApiCall
   * /
  public function testPublishImage ( Syndication $syndication )
  {
    //$syndication       = new Syndication();
    $sourceUri  = 'http://'. gethostbyname(trim(`hostname`)) .':3001';
    $sourceUri .= '/single.img';
    $params = array(
        'mt'            => 'Html', 
        'name'          => 'TestName', 
        'sourceUri'     => $sourceUri, 
        'dateAuthored'  => gmdate('Y-m-d\TH:i:s\Z'), 
        'dateUpdated'   => gmdate('Y-m-d\TH:i:s\Z'),
        'language'      => '1',   
        'organization'  => '1'
    );
    $resp = $syndication->publish($params);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',    $resp,         'Response requires "meta" key'); 
    $this->assertArrayHasKey( 'status',  $resp['meta'], 'Response requires "status" key'); 
    $this->assertEquals(      '200',     $resp['meta']['status'] );
    $this->assertArrayHasKey( 'results', $resp,                   'Response requires has "results" key '); 
    $this->assertEquals(      1,         count($resp['results']), 'Results should only have one result');
    $this->assertArrayHasKey( 0,         $resp['results'],        'Response[results] requires "0" key'); 
    $this->assertArrayHasKey( 'id',      $resp['results'][0],     'Results[0] requires "id" key'); 
    $this->assertTrue(        is_numeric($resp['results'][0]['id']), 'Results[0][id] is numeric');
  }



  /**
   * @depends testInitialization
   * /
  public function testSubscribe ( Syndication $syndication )
  {
    //$syndication = new Syndication();
    $resp = $syndication->subscribe(201);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',    $resp,         'Response requires "meta" key'); 
    $this->assertArrayHasKey( 'status',  $resp['meta'], 'Response requires "status" key'); 
    $this->assertEquals(      '201',     $resp['meta']['status'] );
    $this->assertArrayHasKey( 'results', $resp,                   'Response requires has "results" key '); 
    $this->assertEquals(      1,         count($resp['results']), 'Results should only have one result');
    $this->assertArrayHasKey( 0,         $resp['results'],               'Response[results] requires "0" key'); 
    $this->assertArrayHasKey( 'mediaId', $resp['results'][0],            'Results[0] requires "mediaId" key'); 
    $this->assertEquals(      '201',     $resp['results'][0]['mediaId'], 'Results[0][mediaId] == 201');
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
