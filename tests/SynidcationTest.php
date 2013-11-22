<?php
require('src/Syndication.class.php');

class SyndicationTest extends PHPUnit_Framework_TestCase
{

  public function testInitialization ()
  {
    $synd = new Syndication(array(
            'url'      => 'http://localhost:3000/Syndication/api/v1/resources',
        		'tiny_url' => 'http://localhost:3000/',
            'cms_url'  => 'http://localhost:3000/CMS_Manager/api/v1/resources',
            'cms_id'   => 'drupal_cms_1',
            'api_key'  => 'TEST_CMS1', // 'TEST_CMS2'
            'timeout'  => 60
    ));
    $this->assertInstanceOf('Syndication',$synd);
    return $synd;
  }

  /**
   * @depends testInitialization
   */
  public function testApiCall ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->apiCall('get','http://localhost:3000/200');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'content',   $resp, 'Good Response has "content" key holding actual response content'); 
    $this->assertArrayHasKey( 'format',    $resp, 'Good Response has "format" key holding content format, if known'); 
    $this->assertArrayHasKey( 'http',      $resp, 'Good Response has "http" key holding curl_info'); 
    $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
    $this->assertEquals('200',$resp['http']['http_code']);
    return $synd;
  }
 
  public function testPublish ()
  {
    $synd       = new Syndication();
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
    $resp = $synd->publish($params);
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
   */
  public function testSubscribe ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->subscribe(201);
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
   */
  public function testGetAllMediaTypes ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->getAllMediaTypes();
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
   */
  public function testGetAllOrganizations ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->getAllOrganizations();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
  }

  /**
   * @depends testInitialization
   */
  public function testGetOrganizationById ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->getOrganizationById(1);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertArrayHasKey( 'pagination', $resp['meta'], 'Good Response has "pagination" key under meta'); 
    $this->assertArrayHasKey( 'total',      $resp['meta']['pagination'], 'Good Response has "total" key under pagination'); 
    $this->assertEquals(1,$resp['meta']['pagination']['total'],'Should only paginate one result');
    $this->assertEquals(1,count($resp['results']),'Should only return one result');
    $this->assertEquals('1',$resp['results'][0]['id'],'Should return Id 1');
    
    $resp = $synd->getOrganizationById('missing');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertEquals(0,count($resp['results']),'Should have zero results');
  }

  /**
   * @depends testInitialization
   */
  public function testGetAllLanguages ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->getAllLanguages();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
  }

  /**
   * @depends testInitialization
   */
  public function testGetLanguageById ( $synd )
  {
    //$synd = new Syndication();
    $resp = $synd->getLanguageById(1);
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertArrayHasKey( 'pagination', $resp['meta'], 'Good Response has "pagination" key under meta'); 
    $this->assertArrayHasKey( 'total',      $resp['meta']['pagination'], 'Good Response has "total" key under pagination'); 
    $this->assertEquals(1,$resp['meta']['pagination']['total'],'Should only paginate one result');
    $this->assertEquals(1,count($resp['results']),'Should only return one result');
    $this->assertEquals('1',$resp['results'][0]['id'],'Should return Id 1');
    
    $resp = $synd->getLanguageById('missing');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    $this->assertEquals(0,count($resp['results']),'Should have zero results');
  }

}

?>
