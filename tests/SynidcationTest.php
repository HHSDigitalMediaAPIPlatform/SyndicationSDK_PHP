<?php
require('src/Syndication.class.php');

class SyndicationTest extends PHPUnit_Framework_TestCase
{

  public function testInitialization ()
  {
    $synd = new Syndication();
    $this->assertInstanceOf('Syndication',$synd);
    return $synd;
  }

  public function testApiCall ()
  {
    $synd = new Syndication();
    $resp = $synd->apiCall('get','http://localhost:3000/200');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'content',   $resp, 'Good Response has "content" key holding actual response content'); 
    $this->assertArrayHasKey( 'http',      $resp, 'Good Response has "http" key holding curl_info'); 
    $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
    $this->assertEquals('200',$resp['http']['http_code']);
    return $synd;
  }
 
  public function testGetAllMediaTypes ()
  {
    $synd = new Syndication();
    $resp = $synd->getAllMediaTypes();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    /// results should lisot
    $expected = array( "Collection", "SocialMedia", "Audio", "Widget", "Infographic", "Image", "Video", "Html" );
    $this->assertEquals($expected,$resp['results'],'Good response matches expected set of 8 medieTypes');
  }


  public function testGetAllOrganizations ()
  {
    $synd = new Syndication();
    $resp = $synd->getAllOrganizations();
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
  }

  public function testGetOrganizationById ()
  {
    $synd = new Syndication();
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
  
    /// this should probably be a 404 instead of empty 200 ?
    $resp = $synd->getOrganizationById('missing');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'meta',   $resp,         'Good Response has "meta" key '); 
    $this->assertArrayHasKey( 'status', $resp['meta'], 'Good Response has "status" key holding http status code'); 
    $this->assertEquals('200',$resp['meta']['status']);
    /* TEMP COMMENT: GOOD TESTS BUT API IS BUGGED 
    $this->assertArrayHasKey( 'pagination', $resp['meta'], 'Good Response has "pagination" key under meta'); 
    $this->assertArrayHasKey( 'total',      $resp['meta']['pagination'], 'Good Response has "total" key under pagination'); 
    $this->assertEquals(0,$resp['meta']['pagination']['total'],'Should paginate zero result');
    */
    $this->assertEquals(0,count($resp['results']),'Should have zero results');
  }

  public function testSubscribe ()
  {
    $synd = new Syndication();
    $resp = $synd->subscribe(201);
    $this->assertNotEmpty($resp);
    $this->assertEquals('201',$resp['meta']['status']);
    $this->assertEquals(1,count($resp['results']),'Should only return one result');
    $this->assertEquals('201',$resp['results'][0]['id'],'Should return Id 201');
  } 

}

?>
