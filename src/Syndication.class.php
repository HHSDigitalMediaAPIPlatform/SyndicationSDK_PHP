<?php
/**
 * Syndication API SDK
 *
 * @package Syndication\SDK\PHP
 */ 

/**
 * SyndicationResponse 
 * 
 * @author Dan Narkiewicz <dnarkiewicz@ctacorp.com> 
 * @package Syndication\SDK\PHP
 */
class SyndicationResponse
{
  /**
   * http-response content-type 
   * 
   * @var string
   *
   * @access public
   */
  var $format  = null;

  /**
   * http-reponse status 
   * 
   * @var mixed
   *
   * @access public
   */
  var $status  = null;
 
  /**
   * Array of messages
   * 
   * @var array
   * @array format
   *    errorMessage : string
   *    errorDetail  : mixed
   *    errorCode    : string
   *
   * @access public
   */
  var $messages = array();

  /**
   * List of result data 
   * 
   * @var array
   *
   * @access public
   */
  var $results = array();

  /**
   * Success of requested operation, not of http-request
   * 
   * @var boolean
   *
   * @access public
   */
  var $success = null;

  /**
   * Pagination Information 
   * 
   * @var array
   * @array format
   *    count       : int
   *    currentUrl  : string
   *    max         : int
   *    nextUrl     : string
   *    offset      : int
   *    order       : string
   *    pageNum     : int
   *    previousUrl : string
   *    sort        : string
   *    total       : int
   *    totalPages  : int
   *
   * @access public
   */
  var $pagination = array();

  /**
   * Raw request body content. JSON is decoded. 
   * 
   * @var string
   *
   * @access public
   */
  var $raw = null;

  /**
   * Format for server error messages      
   * 
   * @var array 
   * @array format
   *     errorMessage : string
   *     errorDetail  : mixed
   *     errorCode    : string
   *
   * @access public
   */
  var $empty_message = array(
            'errorMessage' => null,
            'errorDetail'  => null,
            'errorCode'    => null
      );
  /*
   * Format for pagination responses
   *
   * @var array
   * @array format
   *    count       : int
   *    currentUrl  : string
   *    max         : int
   *    nextUrl     : string
   *    offset      : int
   *    order       : string
   *    pageNum     : int
   *    previousUrl : string
   *    sort        : string
   *    total       : int
   *    totalPages  : int
   *
   * @access public
   */
  var $empty_pagination = array(
            'count'       => null,
            'currentUrl'  => null,
            'max'         => null,
            'nextUrl'     => null,
            'offset'      => null,
            'order'       => null,
            'pageNum'     => null,
            'previousUrl' => null,
            'sort'        => null,
            'total'       => null,
            'totalPages'  => null
      );

  /**
   * Constructor for Response 
   * 
   * @access protected
   * 
   * @return self
   */
  function __construct()
  {
    $this->success    = null;
    $this->format     = null;
    $this->status     = null;
    $this->messages   = array();
    $this->results    = array();
    $this->pagination = $this->empty_pagination;
    $this->raw        = null;
  }

  function addPagination( $pagination )
  {
    $this->pagination = array_merge($this->empty_pagination,$pagination);
  }

  function addMessage( $message )
  {
    if ( isset($message['errorMessage']) )
    {
        $this->messages[] = array_merge($this->empty_message,$message);
    } else if ( isset($message[0]) && isset($message[0]['errorMessage']) ) {
        foreach ( $message as $m )
        {
            $this->messages[] = array_merge($this->empty_message,$m);
        }
    }
  }
}



/**
 * Syndication 
 * 
 * @author Dan Narkiewicz <dnarkiewicz@ctacorp.com> 
 * @package Syndication\SDK\PHP
 */
class Syndication
{
    /**
     * Settings for outgoing requests
     *
     * @var array
     * @array format
     *     url      : string
     *     tinu_url : string
     *     cms_url  : string
     *     cms_id   : string
     *     api_key  : string
     *     timeout  : integer
     *
     * @access public
     */
    var $api = array(
        'url'      => '',
        'tiny_url' => '',
        'cms_url'  => '',
        'cms_id'   => '',
        'api_key'  => '',
        'timeout'  => '',
    );

    /**
     * Constructor for Syndication interface
     * 
     * @param mixed $api if array, settings. if string, config file path. 
     * @access protected
     * 
     * @return Syndication
     */
    function __construct ( $api=null )
    {
        if ( is_array($api) )
        {
            $this->api = array_merge($this->api,$api);
        } else {
            /// if api is an array ( merge with defaults );
            /// if api is string ( assume file on filesystem - read and try and guess format )
            $this->api = array(
                'url'      => 'http://ctacdev.com:8090/Syndication/api/v1/resources',
                'tiny_url' => 'http://ctacdev.com:8082/',
                'cms_url'  => 'http://ctacdev.com:8090/CMS_Manager/api/v1/resources',
                'cms_id'   => 'drupal_cms_1',
                'api_key'  => 'TEST_CMS1', // 'TEST_CMS2'
                'timeout'  => 60
            );
        }
    }

    /// CLIENT FUNCTIONS

    function getClientVersion()
    {} /// return the version of this client

    function getServerVersion()
    {} ///  poll server for what it things it's version is

    /// API FUNCTIONS

    /**
     * Gets a list of all MediaType Names 
     * 
     * @access public
     * 
     * @return SyndicationResponse ->results[]
     *      string
     */
    function getAllMediaTypes ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/mediaTypes.json");
            return $this->createResponse($result,'get All MediaTypes');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of Organizations
     * 
     * @param array $params options
     *      max    : int
     *      offset : int
     *      sort   : string
     *      order  : string
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id   : int 
     *      name : string
     *      abv  : string
     *      url  : string 
     */
    function getAllOrganizations ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/organizations.json",$params);
            return $this->createResponse($result,'get All Organizations');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Organization
     * 
     * @param mixed $id Numeric Id of the organization 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id   : int 
     *      name : string
     *      abv  : string
     *      url  : string 
     */
    function getOrganizationById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/organizations/{$id}.json");
            return $this->createResponse($result,'get Organization','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of all Campaigns 
     * 
     * @param array $params options 
     *      max    : int
     *      offset : int
     *      sort   : string
     *      order  : string
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      startDate    : date
     *      endDate      : date
     *      organization : organization  
     */
    function getAllCampaigns ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns.json",$params);
            return $this->createResponse($result,'get All Campaigns');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Campaign
     * 
     * @param mixed $id Numeric Id of the campaign 
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      startDate    : date
     *      endDate      : date
     *      organization : organization  
     */
    function getCampaignById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns/{$id}.json");
            return $this->createResponse($result,'get Campaign','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of all Languages
     *
     * @param array $params options 
     *      max    : int
     *      offset : int
     *      sort   : string
     *      order  : string
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id    : int
     *      name  : string
     *      value : string
     */
    function getAllLanguages ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages.json",$params);
            return $this->createResponse($result,'get All Languages');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Language
     * 
     * @param mixed $id Numeric Id of the language 
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id    : int
     *      name  : string
     *      value : string
     */
    function getLanguageById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages/{$id}.json");
            return $this->createResponse($result,'get Language','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a single Tag
     * 
     * @param mixed $id Numeric Id of the tag 
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id   : int
     *      name : string
     */
    function getTagById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}.json");
            return $this->createResponse($result,'get Tag','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a list of Tags related to a specific Tag
     * 
     * @param mixed $id Numeric Id of the tag 
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id   : int
     *      name : string
     */
    function getRelatedTagsById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/related.json");
            return $this->createResponse($result,'get Related Tags','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    
    /**
     * Gets a list of Media MetaData.
     * Makes a different API call depending on $query. 
     * 
     * @param mixed $query if string: all-column text search. if array: per-column search params 
     *      max                      : int
     *      offset                   : int
     *      sort                     : string 
     *      order                    : string 
     *      mediaType                : string
     *      nameContains             : string
     *      descriptionContains      : string
     *      licenseInfoContains      : string
     *      sourceUri                : string
     *      sourceUriContains        : string
     *      dateAuthored             : string
     *      authoredSinceDate        : string
     *      authoredBeforeDate       : string
     *      authoredInRange          : string
     *      updatedSinceDate         : string
     *      updatedBeforeDate        : string
     *      updatedInRange           : int
     *      languageName             : string
     *      languageValue            : string
     *      hash                     : string
     *      hashContains             : string
     *      organizationId           : int
     *      organizationName         : string
     *      organizationNameContains : string
     *      organizationAbv          : string
     *      organizationAbvContains  : string
     *      tagIds                   : csv_string
     *      restrictToSet            : csv_string
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function searchMedia( $query )
    {
        try
        {
            if ( is_array($query) )
            {
              $result = $this->apiCall('get',"{$this->api['url']}/media.json",$query);
            } else {  
              $params = array( 'q' => $query );
              $result = $this->apiCall('get',"{$this->api['url']}.json",$params,'json');
            }
            return $this->createResponse($result,'search Resources','Search Criteria');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Media MetaData
     * 
     * @param mixed $id Numeric Id of the MediaItem 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function getMediaById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}.json");
            return $this->createResponse($result,'get MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Media MetaData
     * 
     * @param string $source_url Url of the media source 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function getMediaByUrl ( $source_url )
    {
        try
        {
            $params = array( 'sourceUri' => $source_url );
            $result = $this->apiCall('get',"{$this->api['url']}/media.json",$params);
            return $this->createResponse($result,'get MetaData','Url');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a list of Media MetaData
     * 
     * @param mixed $id Numeric Id of a Tag 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function getMediaByTagId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/media.json");
            return $this->createResponse($result,'get MetaData','Tag Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    
    /**
     * Publish a new piece of Media content 
     * 
     * @param mixed $params options 
     *      name         : string 
     *      sourceUri    : string 
     *      dateAuthored : date 
     *      dateUpdated  : date 
     *      language     : string
     *      organization : string 
     *
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function publishMedia ( $params )
    {
        /// syndication will always return metadata for one content item
        /// if publishing a collection, we get single collection item, which contains a list of any sub-items also generated
        try
        {
            $type_path = $params['mediaType'];
            // dirty pluralization
            if( !in_array($type_path,array('SocialMedia','Audio')) ) { $type_path .= 's'; }
            $type_path{0} = strtolower($type_path{0});
            $result = $this->apiCall('post',"{$this->api['url']}/media/$type_path",$params,'json');
            return $this->createResponse($result,'Publish');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * UnPublish a piece of Media content by Id 
     * 
     * @param mixed $id id Numeric Id of a Media item 
     * @access public
     * 
     * @return SyndicationResponse ->results[]
     *      media metatdata ?
     */
    function unPublishMediaById ( $id )
    {
        /// syndication will always return metadata for one content item
        /// if publishing a collection, we get collection item, which contains list of any sub-items also generated
        try
        {
            $result = $this->apiCall('delete',"{$this->api['url']}/media/{$id}");
            return $this->createResponse($result,'Un-Publish','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Subscribe to piece of Media content by Id 
     * 
     * @param mixed $id id Numeric Id of a Media item 
     * @access public
     * 
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function subscribeById ( $id )
    {
        try
        {
            $result = $this->apiCall('post',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * UnSubscribe to piece of Media content by Id 
     * 
     * @param mixed $id id Numeric Id of a Media item 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      licenseInfo  : string
     *      sourceUri    : string
     *      dateAuthored : date(rfc3339)
     *      dateUpdated  : date(rfc3339)
     *      language     : language
     *      active       : boolean
     *      externalGuid : string
     *      hash         : string
     *      organization : organization
     */
    function unSubscribeById ( $id )
    {
        try
        {
            $result = $this->apiCall('delete',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Un-Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single CMS's MetaData 
     * 
     * @param mixed $id Numeric Id of the CMS 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      cms metadata
     */
    function getCmsMetaData ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/cms/{$this->api['cms_id']}",array(),'json');
            return $this->createResponse($result,'get My CMS Information');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets all subscriptions belonging to the CMS identified by APIKEY
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      subscription metadata
     */
    function getSubscriptions ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/subscriptions.json",array(),'json');
            return $this->createResponse($result,'get My Subscriptions');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets the source content of a single Media item
     * 
     * @param mixed $id Numeric Id of the MediaItem 
     * 
     * @access public 
     * @return SyndicationResponse ->results[]
     *      mixed : string or base64 encoded data
     */
    function getMediaDataById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/content");
            return $this->createResponse($result,'get Content','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a preview image of a single Media item. Allows custom size configurations.
     * 
     * @param mixed $id Numeric Id of the Meda Item   
     * @param mixed $params options
     *      id         : int
     *      imageFloat : string
     * 
     * @access public 
     * @return SyndicationResponse ->results[]
     *      base64 encoded jpg
     */
    function getMediaPreviewById ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/preview.jpg");
            return $this->createResponse($result,'get Content Preview','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a fixed size thumbnail image of a single Media item. Allows custom margin configuration.
     * 
     * @param mixed $id Numeric Id of MediaItem
     * @param mixed $params options
     *      imageFloat  : string
     *      imageMargin : css string (int,int,int,int)
     * 
     * @access public 
     * @return SyndicationResponse ->results[]
     *      base64 encoded jpg
     */
    function getMediaThumbnailById ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/thumbnail.jpg");
            return $this->createResponse($result,'get Content Thumbnail','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets the content belonging to a given MediaItem for embedding. Supports HTML, JSON and XML responses based on request format
     *
     * @param mixed $id Numeric Id of MediaItem
     * @param string $format Desired return format of embedded html
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      string (html,json,xml)
     */
    function getMediaEmbeddedHtmlById ( $id, $format='html' )
    {
        try
        {
            if ( !in_array( $format, array('json','html','xml') ) ) { $format='html'; }
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/embed.{$format}");
            return $this->createResponse($result,'get Embedded Html','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Get javascript code used to embed this MediaItem.
     *
     * @param mixed $id Numeric Id of MediaItem
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      html
     */
    function getMediaJavascriptEmbedTagById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/javascriptEmbedTag.html");
            return $this->createResponse($result,'get Snippet Code','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets Youtube metadata for this MediaItem.
     *
     * @param mixed $id Numeric Id of MediaItem
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      youtube metatdata
     */
    function getMediaYoutubeMetadataById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeMedaData.json");
            return $this->createResponse($result,'get YouTube MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets the iframe content belonging to a given MediaItem for embedding. Name is placed into the iframe's html.
     *
     * @param mixed $id Numeric Id of MediaItem
     * @param mixed $params options
     *      width  : int
     *      height : int
     *      name   : string
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *     html 
     */
    function getMediaIframeEmbeddedTagById ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/iframeEmbeddedTag",$params);
            return $this->createResponse($result,'get Embedded IFrame','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }


    /// INTERNAL FUNCTIONS

    /**
     * Parse a URL string into array. 
     * Based on RFC3986 'URI Generic Syntax' regex. Added 'Format' as the last dot expression of the path. Does not include character encoding restrictions.
     * 
     * @param mixed $url url
     *
     * @access public
     * @return array keys 
     *      scheme
     *      userinfo
     *      host
     *      path
     *      format
     *      query
     *      fragment 
     */
    function parseUrl($url)
    {
        $simple_url = "/^(?:(?P<scheme>[^:\/?#]+):\/\/)(?:(?P<userinfo>[^\/@]*)@)?(?P<host>[^\/?#]*)(?P<path>[^?#]*?(?:\.(?P<format>[^\.?#]*))?)?(?:\?(?P<query>[^#]*))?(?:#(?P<fragment>.*))?$/i";
        $url_parts  = array();
        preg_match($simple_url, $url, $url_parts);
        return $url_parts;
    }
    /**
     * Guess response format directly from the url. 
     * Checks the path for a known file extension. Default for no file extension is 'raw'. Basic image types combined into 'image'.
     * 
     * @param mixed $url url 
     * @access public
     * 
     * @return string response format 
     */
    function guessFormatFromUrl ($url)
    {
        $url_parts = $this->parseUrl($url);
        if ( empty($url_parts) )
        {
            return 'raw';
        }
        $format = !empty($url_parts['format'])?$url_parts['format']:'raw';
        if ( in_array($format,array('jpg','jpeg','png','gif')) ) { $format = 'image'; }
        return $format;
    }
    /**
     * Guess response format from response headers. Defaults to 'raw'. Checks Content-Type header. If no content-type header and first character of body is '{', assume 'json'. 
     * 
     * @param mixed $response response 
     * @access public
     * 
     * @return string format
     */
    function guessFormatFromResponse ($response)
    {
        if ( stripos($response['content_type'],'json')       !== false ) { return 'json';  }
        if ( stripos($response['content_type'],'image')      !== false ) { return 'image'; }
        if ( stripos($response['content_type'],'html')       !== false ) { return 'html';  }
        if ( stripos($response['content_type'],'text')       !== false ) { return 'text';  }
        if ( stripos($response['content_type'],'javascript') !== false ) { return 'js';    }
        /// last ditch effort to guess json
        if ( is_string($response['content']) && $response['content']{0} == '{' ) { return 'json';  }
        return 'raw';
    }
    /**
     * Decode Http Status code into string
     * 
     * @param mixed $status status 
     * @access public
     * 
     * @return string status message
     */
    function httpStatusMessage ( $status )
    {
        # rfc2616-sec10
        $messages = array(
            // [Informational 1xx]
            100=>'100 Continue',
            101=>'101 Switching Protocols',
            // [Successful 2xx]
            200=>'200 OK',
            201=>'201 Created',
            202=>'202 Accepted',
            203=>'203 Non-Authoritative Information',
            204=>'204 No Content',
            205=>'205 Reset Content',
            206=>'206 Partial Content',
            // [Redirection 3xx]
            300=>'300 Multiple Choices',
            301=>'301 Moved Permanently',
            302=>'302 Found',
            303=>'303 See Other',
            304=>'304 Not Modified',
            305=>'305 Use Proxy',
            306=>'306 (Unused)',
            307=>'307 Temporary Redirect',
            // [Client Error 4xx]
            400=>'400 Bad Request',
            401=>'401 Unauthorized',
            402=>'402 Payment Required',
            403=>'403 Forbidden',
            404=>'404 Not Found',
            405=>'405 Method Not Allowed',
            406=>'406 Not Acceptable',
            407=>'407 Proxy Authentication Required',
            408=>'408 Request Timeout',
            409=>'409 Conflict',
            410=>'410 Gone',
            411=>'411 Length Required',
            412=>'412 Precondition Failed',
            413=>'413 Request Entity Too Large',
            414=>'414 Request-URI Too Long',
            415=>'415 Unsupported Media Type',
            416=>'416 Requested Range Not Satisfiable',
            417=>'417 Expectation Failed',
            // [Server Error 5xx]
            500=>'500 Internal Server Error',
            501=>'501 Not Implemented',
            502=>'502 Bad Gateway',
            503=>'503 Service Unavailable',
            504=>'504 Gateway Timeout',
            505=>'505 HTTP Version Not Supported'
        );
        return isset($messages[$status])? $messages[$status] : null;
    }

    /**
     * Wraps curl response or exception into a common SyndicationResponse Object. 
     * 
     * @param mixed $from Curl Response or Exception 
     * @param string $action Devloper friendly description of what triggered response
     * @param mixed $key API Key used to connect 
     *
     * @access public
     * @return SyndicationResponse object
     *      ->format   : string  http response format
     *      ->status   : string  http response status
     *      ->messages : array   developer friendly error messages
     *      ->results  : array
     *      ->success  : boolean
     */
    function createResponse ( $from, $action="Process Request", $item_key=null )
    {
        $response = new SyndicationResponse();
        $response->raw = $from;

        /// an exception was thrown
        if ( is_subclass_of($from,'Exception') )
        {
            $response->success = false;
            $response->status  = $from->getCode();
            $response->format  = 'Exception';
            $response->addMessage(array(
                'errorCode'    => $from->getCode(),
                'errorMessage' => $from->getMessage(),
                'errorDetail'  => "{$action} Exception"
            ));
            return $response;

        /// we got a response from the server
        } else if ( is_array($from)
            && !empty($from['http'])
            && !empty($from['format']) )
        {
            $status = isset($from['http']['http_code']) ? intval($from['http']['http_code']) : null;
            $response->status = $status;
            /// SUCCESS
            if ( $status>=200 && $status<=299 )
            {
                $response->success = true;
            /// CLIENT SIDE ERROR
            } else if ( $status>=400 && $status<=499 ) {
                /// BAD API KEY    
                if ( $status == 401 ) {
                    $errorDetail = "Unauthorized. Check API Key.";
                    /// VALID URL but specific id given does not exist 
                } else if ( $status == 404 && !empty($item_key) ) {
                    $errorDetail = "Failed to {$action}. {$item_key} Not Found.";
                    /// Error in the request
                } else {
                    $errorDetail = "Failed to {$action}. Request Error.";
                }
                $response->success  = false;
                $response->addMessage(array(
                    'errorCode'    => $status,
                    'errorMessage' => $this->httpStatusMessage($status),
                    'errorDetail'  => $errorDetail
                ));
                /// SERVER SIDE ERROR
            } else if ( $status>=500 && $status<=599 ) {
                $response->success  = false;
                $response->addMessage(array(
                    'errorCode'    => $status,
                    'errorMessage' => $this->httpStatusMessage($status),
                    'errorDetail'  => "Failed to {$action}. Server Error."
                ));
            }

            if ( $from['format']=='json' )
            {
                /// for any json response
                /// [meta] and [results] expected back from api
                /// [meta][messages] should be consumed if found
                /// [meta][pagination] should be consumed if found 
                /// [message] was changed to plural, check for both for now just incase

                /// look for meta
                if ( isset($from['meta']) )
                {
                    if ( isset($from['meta']['pagination']) )
                    {
                        $response->addPagination($from['meta']['pagination']);
                    }
                    if ( isset($from['meta']['messages']) )
                    {
                        $response->addMessage($from['content']['meta']['messages']);
                    }
                    if ( isset($from['meta']['message']) )
                    {
                        $response->addMessage($from['content']['meta']['message']);
                    }
                } else if ( isset($from['content']) && isset($from['content']['meta']) ) {
                    if ( isset($from['content']['meta']['pagination']) )
                    {
                        $response->addPagination($from['content']['meta']['pagination']); 
                    }
                    if ( isset($from['content']['meta']['messages']) )
                    {
                        $response->addMessage($from['content']['meta']['messages']);
                    }
                    if ( isset($from['content']['meta']['message']) )
                    {
                        $response->addMessage($from['content']['meta']['message']);
                    }
                }
                /// look for results
                if ( isset($from['content']) )
                {
                    if ( isset($from['content']['results']) ) 
                    {
                        $response->results = (array)$from['content']['results'];
                    } else {
                        $response->results = (array)$from['content'];
                    }
                }
                $response->format = 'json';
                return $response;
            } else if ( $from['format']=='image' ) {
                $response->format  = 'image';
                
                /// a single string: base64 encoded image : imagecreatefromstring?
                $response->results = $from['content'];
                return $response;
            /// unknown format
            } else {
                $response->format  = $from['format'];
                /// a single string : html : filtered_html?
                $response->results = $from['content'];
                return $response;
            }
        }
        /// we got something weird - can't deal with this
        $response->success = false;
        $status = null;
        if ( is_array($from) && !empty($from['http']) && isset($from['http']['http_status']) )
        {
            $status = $from['http']['http_status'];
        }
        $response->addMessage(array(
            'errorCode'    => $status,
            'errorMessage' => $this->httpStatusMessage($status),
            'errorDetail'  => "Unknown response from Server."
        ));
        return $response;
    }

    /**
     * Makes http request to a Syndication Service 
     * 
     * @param mixed $http_method http method 
     * @param mixed $url url 
     * @param array $params query params 
     * @param mixed $response_format expected response format 
     *
     * @access public
     * @return array format
     *      http    : array   curl info about request and response
     *      content : string  response body
     *      format  : string  response format
     */
    function apiCall ( $http_method, $url, $params=array(), $response_format=null )
    {
        if ( empty($response_format) )
        {
            $response_format = $this->guessFormatFromUrl($url);
        }

        foreach ( $params as $p=>$param )
        {
            if ( empty($param) && $param!==0 && $param!=='0' ) 
            {
                unset( $params[$p] );
            }
        }

        /// our request format type
        $request_headers = array(
            //'Content-Type: text/xml; charset=UTF-8',
            'Content-Type: application/x-www-form-urlencoded',
            'Date: '.gmdate('D, d M Y H:i:s', time()).' GMT'
        );

        /// ask for a specific format type of response
        if ( !empty($response_format) )
        {
            switch( $response_format )
            {
                case 'html':
                    $request_headers[] = 'Accept: text/html; charset=UTF-8';
                    break;
                case 'json':
                    $request_headers[] = 'Accept: application/json; charset=UTF-8';
                    break;
                case 'js':
                    $request_headers[] = 'Accept: application/javascript; charset=UTF-8';
                    break;
                case 'text':
                    $request_headers[] = 'Accept: text/plain; charset=UTF-8';
                    break;
                case 'image':
                    $request_headers[] = 'Accept: image/*;';
                    break;
            }
        }
        
        $apiKey = $this->apiGenerateKey( $http_method, $url, $params, $request_headers );
        $request_headers[] = "Authentication: syndication_api_key {$apiKey}";

        $curl = $this->apiBuildCurlRequest( $http_method, $url, $params, $request_headers, $response_format ); 

        $content = curl_exec($curl);
        $http    = curl_getinfo($curl);

        if ($content === false)
        {
            curl_close($curl);
            throw new Exception('Syndication: No Response: '. $http['http_code'], $http['http_code'] );
            return null;
        }
        curl_close($curl);

        if ( empty($response_format) )
        {
            $response_format = $this->guessFormatFromResponse($http);
        }

        $api_response = array(
            'http'    => $http,
            'content' => $content,
            'format'  => $response_format
        );
        /// test result content-type for JSON / HTML / IMG
        /// json needs to be decoded
        /// html stay as text
        /// images need to be: base64_encoded string or image resource
        if ( $response_format=='image' )
        {
            // as GD handle ?
            // $api_response['content'] = imagecreatefromstring($content);
        } else if ( $response_format=='text' ) {
            // nuthin
        } else if ( $response_format=='html' ) {
            // any html cleaning ?
        } else if ( $response_format=='js'   ) {
            // any xss cleaning ?
        } else if ( $response_format=='json' ) {
            $decoded = json_decode($content,true);
            if ( isset($decoded['results']) )
            {
                if ( empty($decoded['results']) || count($decoded['results'])==1 && empty($decoded['results'][0]) )
                {
                    $decoded['results'] = array();
                }
            }
            $api_response['content'] = $decoded;
        }
        return $api_response;
    }

    /**
     * Builds Curl Object capable of talking to Syndication Service
     * 
     * @param string $http_method http request method 
     * @param string $url url 
     * @param array $params query params 
     * @param array $headers headers 
     * @param string $response_format expected http response format 
     *
     * @access public
     * @return curl resouce handle
     */
    function apiBuildCurlRequest( $http_method, $url, $params=array(), $headers=array(), $response_format='' )
    {
        $http_params = http_build_query( $params, '', '&' );
       
        //$headers[] = "Date: ".gmdate("D, d M Y H:i:s", time())." GMT";

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_USERAGENT,      'Syndication-Client/php-drupal v1'); // Useragent string to use for request
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        if ( $response_format=='image' )
        {
            curl_setopt($curl, CURLOPT_HEADER,         false );
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true  );
        }

        switch ( strtolower($http_method) )
        {
            case 'post':
                //curl_setopt( $curl, CURLOPT_POST,       true    );
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST'    );
                if ( !empty($http_params) )
                {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
                }
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'put':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT'        );
                if ( !empty($http_params) )
                {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
                }
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'delete':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                if ( !empty($http_params) )
                {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params  );
                }
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'get':
            default:
                curl_setopt( $curl, CURLOPT_HTTPGET, true ); 
                if ( !empty($http_params) )
                {
                    $url .= (strpos($url,'?')===FALSE?'?':'&') . $http_params;
                }
                $headers[] = 'Content-length: 0';
                break;
        }
        curl_setopt( $curl, CURLOPT_HTTPHEADER,     $headers);
/// debug
//        curl_setopt( $curl, CURLOPT_VERBOSE, 1 );
//        curl_setopt( $curl, CURLOPT_STDERR,  fopen('php://stdout', 'w') );

        curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );                    // seconds attempting to connect
        curl_setopt( $curl, CURLOPT_TIMEOUT,        $this->api['timeout'] ); // seconds cURL allowed to execute
        /** /// forces new connections
        curl_setopt( $curl, CURLOPT_FORBID_REUSE,  true );
        curl_setopt( $curl, CURLOPT_FRESH_CONNECT, true );
        curl_setopt( $curl, CURLOPT_MAXCONNECTS,   1);
        /**/
        curl_setopt( $curl, CURLOPT_URL, $url );

        return $curl;
    }

    /**
     * Generate API Key.
     * Use public/private keys to sign this request. Used by Syndication Service to verify request authenticity.  
     * 
     * @param string $http_method http request method 
     * @param string $url url 
     * @param array $params query params 
     * @param array $headers http request headers 
     * 
     * @access public
     * @return string Api Key
     */
    function apiGenerateKey( $http_method, $url, $params, $headers )
    {
        /// need to figure out how key sharing works

      // ordered and scrubbed headers: date,content-type,content-length;
      $canonicalizedHeaders  = '';
      $desiredHeaders = array('date','content-type','content-length');
      $headerData = array();
      foreach ( $headers as $header )
      {
        $pos = strpos(':',$header);
        if ( $pos )
        {
          $name  = strtolower(trim(substr($header,0,$pos)));
          $value = substr($header,$pos+1);
          $headerData[$name] = trim($value);
          if ( in_array($name,$desiredHeaders) )
          {
            $canononicalizedHeaders = $name .':'. trim(str_replace(array('\n','\r'),' ',$value))."\n";
          } 
        }      
      }
      $canonicalizedHeaders = trim($canonicalizedHeaders);

      // just the clean url path
      $url_parts = $this->parseUrl($url);
      $canonicalizedResource = ( !empty($url_parts) && !empty($url_parts['path']) ) ? trim($url_parts['path']) : ''; 
        
      /// md5 of the body
      $http_params   = http_build_query( $params, '', '&' );
      $hashedData    = md5($http_params);

      // array of: date,content-type,http method;
      $requestData = array( 'date'         => isset($headerData['date'])         ? $headerData['date']         : '', 
                            'content-type' => isset($headerData['content-type']) ? $headerData['content-type'] : '', 
                            'method'       => strtoupper($http_method) ); 

      // put it all together
      $signingString = "{$requestData['method']}\n".
                       "{$hashedData}\n".
                       "{$requestData['content-type']}\n".
                       "{$requestData['date']}\n".
                       "{$canonicalizedHeaders}\n".
                       "{$canonicalizedResource}";

      /// grab keys 
      $sharedKey     = "SHARED SECRET KEY";  
      $myPublicKey   = "MY PUBLIC KEY";  
      
      /// hash up our thingy
      $computedHash  = base64_encode(hash_hmac('sha1', $signingString, $sharedKey, true ));

      /// share public key are our hash
      return "{$myPublicKey}:{$computedHash}";
    }
}
