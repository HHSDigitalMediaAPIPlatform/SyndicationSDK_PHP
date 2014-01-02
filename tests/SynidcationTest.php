<?php
ini_set('display_errors',1);error_reporting(E_ALL);

require('src/Syndication.class.php');

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
        $this->run=sprintf('%s > %s 2>&1 & echo $!',$this->cmd,$out);
        $this->pid=trim(shell_exec($this->run));
        if($this->dir){chdir($cwd);}
    }
    public function running(){try{$r=shell_exec(sprintf('ps %d',$this->pid));return(count(explode("\n",$r))>2);}catch(Exception $e){};return false;}
    public function stop(){if(!$this->pid){return true;}shell_exec(sprintf('kill %d',$this->pid));return $this->running();}
}


class SyndicationTest extends PHPUnit_Framework_TestCase
{
  protected static $cms_port;  
  protected static $pub_port;
  protected static $cms_mock;  
  protected static $pub_mock;
  protected static $hostname;
  protected static $syndication;   
  
  public function __construct()
  {
    self::$cms_port = 3000;
    self::$pub_port = 3001;
    self::$hostname = gethostbyname(trim(`hostname`));

    //self::$cms_mock = new BGProcess('npm start','../cms_manager_simulator/');
    //self::$pub_mock = new BGProcess('php -S '.self::$hostname.':'.self::$pub_port,'./public/');
  }
  public static function setUpBeforeClass()
  {
    self::$syndication = new Syndication(array(
            'url'      => "http://localhost:".self::$cms_port."/Syndication/api/v1/resources",
       		'tiny_url' => "http://localhost:".self::$cms_port."/",
            'cms_url'  => "http://localhost:".self::$cms_port."/CMS_Manager/api/v1/resources",
            'cms_id'   => 'drupal_cms_1',
            'api_key'  => 'TEST_CMS1',
            'timeout'  => 60
    ));
    //self::$cms_mock->start();
    //self::$pub_mock->start();
  }
  public static function tearDownAfterClass()
  {
    //self::$cms_mock->stop();
    //self::$pub_mock->stop();
  }

  public function testInitialization ()
  {
    $this->assertInstanceOf('Syndication',self::$syndication);
    //$this->assertTrue(self::$cms_mock->running(),'Mock Service is running');
    //$this->assertTrue(self::$pub_mock->running(),'Public Server is running');
  }

  public function testCurlRequest ()
  {
    $type    = 'post'; 
    $url     = 'http://'.self::$hostname.':'.self::$pub_port.'/single.html';
    $params  = array('a'=>'1');    
    $headers = array();
    $format  = 'json';
    
    $curl = self::$syndication->apiBuildCurlRequest( $type, $url, $params, $headers, $format );
    $this->assertNotEmpty($curl);
    $this->assertInternalType('resource',$curl,'Must be a resource');
    $this->assertEquals('curl',get_resource_type($curl),'Must be a curl resource');

    $url     = 'http://'.self::$hostname.':'.self::$cms_port.'/200';
    echo file_get_contents($url);
  }

/*  
  public function testApiCall ()
  {
    $resp = self::$syndication->apiCall('get','http://'.self::$hostname.':'.self::$cms_port.'/200');
    $this->assertNotEmpty($resp);
    $this->assertArrayHasKey( 'content',   $resp, 'Good Response has "content" key holding actual response content'); 
    $this->assertArrayHasKey( 'format',    $resp, 'Good Response has "format" key holding content format, if known'); 
    $this->assertArrayHasKey( 'http',      $resp, 'Good Response has "http" key holding curl_info'); 
    $this->assertArrayHasKey( 'http_code', $resp['http'], 'Good Response has "http_code" key holding http status code'); 
    $this->assertEquals('200',$resp['http']['http_code']);
  }
  public function testPublishHtml ( Syndication $syndication )
  {
    $sourceUri  = 'http://'. self::$hostname.':'.self::$cms_port;
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
