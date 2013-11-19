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
  }

}

?>
