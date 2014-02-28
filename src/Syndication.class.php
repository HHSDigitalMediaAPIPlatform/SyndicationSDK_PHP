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

  /**
   * Format for pagination responses
   *
   * @var array
   * @array format
   *    count       : int
   *    currentUrl  : string
   *    max         : int
   *    nextUrl     : string
   *    offset      : int
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

  /**
   * Add pagination info to response
   * 
   * @param array $pagination options
   *    count       : int
   *    currentUrl  : string
   *    max         : int
   *    nextUrl     : string
   *    offset      : int
   *    pageNum     : int
   *    previousUrl : string
   *    sort        : string
   *    total       : int
   *    totalPages  : int
   *
   * @access public
   * 
   * @return void
   */
  function addPagination( $pagination )
  {
    $this->pagination = array_merge($this->empty_pagination,$pagination);
  }

  /**
   * Add message to list of response messages 
   * 
   * @param mixed $message message 
   * @access public
   * 
   * @return void
   */
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

/*

http://ctacdev.com:8090/Syndication/swagger/media/api

*/

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
     *
     * @access public
     */
    var $api = array(
        'syndication_url'     => '',
        'syndication_tinyurl' => '',
        'cms_manager_url'     => '',
        'cms_manager_id'      => '',
        'key_shared'          => '',
        'key_public'          => '', 
        'key_private'         => ''
    );

    /**
     * Date format required by Syndication Server 
     * 
     * @var string 
     * @access public
     */
    var $date_format = 'Y-m-d\TH:i:s\Z';

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
        $settings = array();
        if ( is_array($api) )
        {
            $settings = $api;
        } else if ( is_string($api) && file_exists($api) && is_file($api) && is_readable($api) ) {
            /// try and see if it's an ini file
            if ( $ini = parse_ini_file($api,true) )
            {
                $settings = $ini;
            /// try and see if it's a json file
            } else {
                $contents = file_get_contents($api);
                $json = json_decode($contents,true);
                if ( !empty($json) && is_array($json) ) 
                {
                    $settings = $json;
                }    
            }
        }
        foreach ( array_keys($this->api) as $k )
        {
            if ( isset($settings[$k]) )
            {
                $this->api[$k] = $settings[$k];
            }
        }
    }

    /// CLIENT FUNCTIONS

    /**
     * Get Client Version : the versions of the server api this client can talk too 
     * 
     * @access public
     * 
     * @return string
     */
    function getClientVersion()
    {
        return array('2');
    }

    /**
     * Get Server Version : the version of the API for the configured server - generated dynamically from api settings
     * 
     * @access public
     * 
     * @return string
     */
    function getServerVersion()
    { 
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/swagger/api");
            return $this->createResponse($result,'get Server API Version');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/mediaTypes.json");
            return $this->createResponse($result,'get All MediaTypes');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of Sources
     * 
     * @param array $params options
     *      max    : int
     *      offset : int
     *      sort   : string
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int 
     *      name         : string
     *      acronym      : string
     *      websiteUrl   : string 
     *      largeLogoUrl : string 
     *      SmallLogoUrl : string 
     */
    function getAllSources ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/sources.json",$params);
            return $this->createResponse($result,'get All Sources');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    } 

    /**
     * Gets a single Source
     * 
     * @param mixed $id Numeric Id of the source 
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int 
     *      name         : string
     *      acronym      : string
     *      websiteUrl   : string 
     *      largeLogoUrl : string 
     *      SmallLogoUrl : string 
     */
    function getSourceById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/sources/{$id}.json");
            return $this->createResponse($result,'get Source','Id');
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
     *  
     * @access public
     * @return SyndicationResponse ->results[]
     *      id           : int
     *      name         : string
     *      description  : string
     *      startDate    : date
     *      endDate      : date
     *      source       : source  
     */
    function getAllCampaigns ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/campaigns.json",$params);
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
     *      source       : source  
     */
    function getCampaignById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/campaigns/{$id}.json");
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/languages.json",$params);
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/languages/{$id}.json");
            return $this->createResponse($result,'get Language','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of all Tags
     *
     * @param array $params options 
     *      max           : int
     *      offset        : int
     *      sort          : string
     *      name          : string
     *      nameContains  : string
     *      syndicationId : int
     *      typeId        : int
     *      typeName      : string
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id   : int
     *      name : string
     */
    function getTags ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tags.json",$params);
            return $this->createResponse($result,'get All Tags');
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tags/{$id}.json");
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tags/{$id}/related.json");
            return $this->createResponse($result,'get Related Tags','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    
    /// function getMediaByTagId ( $id ) : defined below with getMedia*() functions

    /**
     * Gets a list of Media MetaData.
     * Makes a different API call depending on $query. 
     * 
     * @param mixed $query if string or params with q: all-column text search. if array: per-column search params 
     *      max                          : int
     *      offset                       : int
     *      sort                         : string 
     *      mediaType                    : csv
     *      name                         : string
     *      nameContains                 : string
     *      sourceUrl                    : string
     *      sourceUrlContains            : string
     *      descriptionContains          : string
     *      licenseInfoContains          : string
     *      dateContentAuthored          : rfc3339
     *      contentAuthoredSinceDate     : rfc3339
     *      contentAuthoredBeforeDate    : rfc3339
     *      contentAuthoredInRange       : csv rfc3339
     *      dateContentUpdated           : rfc3339
     *      contentUpdatedSinceDate      : rfc3339
     *      contentUpdatedBeforeDate     : rfc3339
     *      contentUpdatedInRange        : csv rfc3339
     *      dateContentPublished         : rfc3339
     *      contentPublishedSinceDate    : rfc3339
     *      contentPublishedBeforeDate   : rfc3339
     *      contentPublishedInRange      : csv rfc3339
     *      dateContentReviewed          : rfc3339
     *      contentReviewedSinceDate     : rfc3339
     *      contentReviewedBeforeDate    : rfc3339
     *      contentReviewedInRange       : csv rfc3339
     *      dateSyndicationUpdated       : rfc3339
     *      syndicationUpdatedSinceDate  : rfc3339
     *      syndicationUpdatedBeforeDate : rfc3339
     *      syndicationUpdatedInRange    : csv rfc3339
     *      dateSyndicationVisible       : rfc3339
     *      syndicationVisibleSinceDate  : rfc3339
     *      syndicationVisibleBeforeDate : rfc3339
     *      syndicationVisibleInRange    : csv rfc3339
     *      languageId                   : string
     *      languageName                 : string
     *      languageIsoCode              : string
     *      hash                         : string
     *      hashContains                 : string
     *      sourceId                     : int
     *      sourceName                   : string
     *      sourceNameContains           : string
     *      sourceAcronym                : string
     *      sourceAcrynymContains        : string
     *      tagIds                       : csv int
     *      restrictToSet                : csv int
     * 
     * @access public
     * @return SyndicationResponse ->results[]
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function getMedia( $query )
    {
        try
        {
            if ( is_array($query) )
            {
              if ( isset($query['q']) )
              {
                $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/searchResults.json",$params);
              } else {  
                $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media.json",$query);
              }
            } else {  
              $params = array( 'q' => $query );
              $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/searchResults.json",$params);
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
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function getMediaById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}.json");
            return $this->createResponse($result,'get MetaData','Id');
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
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function getMediaByTagId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tags/{$id}/media.json");
            return $this->createResponse($result,'get MetaData','Tag Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets MediaItems related to certain MediaItem.
     *
     * @param mixed $id Numeric Id of origin MediaItem
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function getMediaRelatedToId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/relatedMedia.json");
            return $this->createResponse($result,'get Media Alternate Images','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Publish a new piece of Media content 
     * 
     * @param mixed $params options 
     *      name         : string 
     *      sourceUrl    : string 
     *      dateAuthored : date 
     *      dateUpdated  : date 
     *      language     : string
     *      source : string 
     *
     * @access public
     * @return SyndicationResponse ->results[]
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
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
            $result = $this->apiCall('post',"{$this->api['syndication_url']}/resources/media/$type_path",$params,'json');
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
            $result = $this->apiCall('delete',"{$this->api['syndication_url']}/resources/media/{$id}");
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
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function subscribeById ( $id )
    {
        try
        {
            $result = $this->apiCall('post',"{$this->api['cms_manager_url']}/resources/subscriptions/{$id}",array(),'json');
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
     *      id                      : int
     *      mediaType               : string
     *      name                    : string
     *      description             : string
     *      sourceUrl               : string
     *      dateContentAuthored     : rfc3339
     *      dateContentUpdated      : rfc3339
     *      dateContentPublished    : rfc3339
     *      dateContentReviewed     : rfc3339
     *      dateSyndicationVisible  : rfc3339
     *      dateSyndicationCaptured : rfc3339
     *      dateSyndicationUpdated  : rfc3339
     *      language                : language
     *      externalGuid            : string
     *      contentHash             : string
     *      source                  : source
     *      campaigns               : campaign[]
     *      tags                    : tag[]
     *      tinyUrl                 : string
     *      tinyToken               : string
     *      thumbnailUrl            : string
     *      alternateImages         : image[]
     *      attribution             : string
     */
    function unSubscribeById ( $id )
    {
        try
        {
            $result = $this->apiCall('delete',"{$this->api['cms_manager_url']}/resources/subscriptions/{$id}",array(),'json');
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
            $result = $this->apiCall('get',"{$this->api['cms_manager_url']}/resources/cms/{$this->api['cms_manager_id']}",array(),'json');
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
            $result = $this->apiCall('get',"{$this->api['cms_manager_url']}/resources/subscriptions.json",array(),'json');
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/content");
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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/preview.jpg");
            return $this->createResponse($result,'get Content Preview','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a fixed size thumbnail image of a single Media item. Allows custom margin configuration.
     * 
     * @param mixed $id Numeric Id of MediaItem
     * 
     * @access public 
     * @return SyndicationResponse ->results[]
     *      base64 encoded jpg
     */
    function getMediaThumbnailById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/thumbnail.jpg");
            return $this->createResponse($result,'get Content Thumbnail','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets the content belonging to a given MediaItem for embedding. Supports HTML, JSON and XML responses based on request format
     *
     * @param mixed $id Numeric Id of MediaItem
     * @param mixed $params options
     *      flavor : string
     *      width  : int
     *      height : int
     *      name   : string
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      string hmtml
     */
    function getMediaEmbedById ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/embed.html",$params);
            return $this->createResponse($result,'get Embedded Html','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets the content belonging to a given MediaItem for embedding as HTML.
     *
     * @param mixed $id Numeric Id of MediaItem
     * @param mixed $params options
     *      cssClass     : string (for extraction)
     *      stripStyles  : int
     *      stripImages  : int
     *      stripBreaks  : string
     *      stripClasses : string
     *      font-size    : int
     *      stripBreaks  : string
     *      imageFloat   : string
     *      imageMargin  : csv int
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      string hmtml
     */
    function getMediaHtmlById ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/syndicate.html",$params);
            return $this->createResponse($result,'get Embedded Html','Id');
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
    function getMediaYoutubeMetaDataById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/youtubeMedaData.json");
            return $this->createResponse($result,'get YouTube MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets list of alternate images for this MediaItem.
     *
     * @param mixed $id Numeric Id of MediaItem
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      youtube metatdata
     */
    function getMediaAlternateImagesById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/alternateImages.json");
            return $this->createResponse($result,'get Media Alternate Images','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets ratings for this MediaItem.
     *
     * @param mixed $id Numeric Id of MediaItem
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      likes : int
     */
    function getMediaRatingsById ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/ratings.json");
            return $this->createResponse($result,'get Media Alternate Images','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Calls an debug url which will do nothing but validate our key. 200 http response means valid.
     * 
     * @access public
     * 
     * @return SyndicationResponse ->results[]
     *      empty
     */
    function testCredentials()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_manager_url']}/debug/secure/resource");
            return $this->createResponse($result,'Test APIKey Credentials','ApiKey');
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
     * @param mixed $api_key API Key used to connect 
     *
     * @access public
     * @return SyndicationResponse object
     *      ->format   : string  http response format
     *      ->status   : string  http response status
     *      ->messages : array   developer friendly error messages
     *      ->results  : array
     *      ->success  : boolean
     */
    function createResponse ( $from, $action="Process Request", $api_key=null )
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
                } else if ( $status == 404 && !empty($api_key) ) {
                    $errorDetail = "Failed to {$action}. {$api_key} Not Found.";
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
        /// ascending order is default, descending order is speicified by a '-' sign 
        /// if 'order' param is set, reinterpret that as part of sort param 
        if ( !empty($params['sort']) && !empty($params['order']) ) 
        {
            // clean up sort, strip off leading '-'
            if ( $params['sort']{0} != '-' )
            {
                $params['sort'] = substr( $params['sort'], 1 );
            }
            if ( strtolower(substr($params['order'],4)) == 'desc' )
            {
                $params['sort'] = '-'.$params['sort'];
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

        /// content-length required for apiKeyGen
        switch ( strtolower($http_method) )
        {
            case 'post':
            case 'put':
            case 'delete':
                $request_headers[] = 'Content-Length: '.strlen(http_build_query($params,'','&'));
                break;
            case 'get':
            default:
                $request_headers[] = 'Content-Length: 0';
                break;
        }
        
        $apiKey = $this->apiGenerateKey( $http_method, $url, $params, $request_headers );
        $request_headers[] = "Authorization: syndication_api_key {$apiKey}";

        $curl = $this->apiBuildCurlRequest( $http_method, $url, $params, $request_headers, $response_format ); 


        /// do some temp memory writing bs to capture curl's output to grab actual request string
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($curl, CURLOPT_STDERR, $verbose);

        $content = curl_exec($curl);

        rewind($verbose);
        $verbose_log = stream_get_contents($verbose);

        $http = curl_getinfo($curl);
        $http['verbose_log'] = $verbose_log;

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
            try {
                $decoded = json_decode($content,true);
                if ( $decoded === null ) 
                {
                    /// bad json should return empty, or return raw unencoded values?
                } else if ( isset($decoded['results']) ) {
                    if ( empty($decoded['results']) || count($decoded['results'])==1 && empty($decoded['results'][0]) )
                    {
                        $decoded['results'] = array();
                    }
                }
                $api_response['content'] = $decoded;
            } catch ( Exception $e ) { 
                /// bad json should return empty, or return raw unencoded values?
            }
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
                break;
            case 'put':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT'        );
                if ( !empty($http_params) )
                {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
                }
                break;
            case 'delete':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                if ( !empty($http_params) )
                {
                    curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params  );
                }
                break;
            case 'get':
            default:
                curl_setopt( $curl, CURLOPT_HTTPGET, true ); 
                if ( !empty($http_params) )
                {
                    $url .= (strpos($url,'?')===FALSE?'?':'&') . $http_params;
                }
                break;
        }
        curl_setopt( $curl, CURLOPT_HTTPHEADER,     $headers);
        /** / // debug request output
        curl_setopt( $curl, CURLOPT_VERBOSE, 1 );
        curl_setopt( $curl, CURLOPT_STDERR,  fopen('php://stdout', 'w') );
        /**/

        curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 ); // seconds attempting to connect
        curl_setopt( $curl, CURLOPT_TIMEOUT,        12 ); // seconds cURL allowed to execute
        /** / // forces new connections
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
      // ordered and scrubbed headers: date,content-type,content-length;
      $canonicalizedHeaders  = '';
      $desiredHeaders = array('date','content-type','content-length');
      $headerData = array();
      rsort($headers);
/**/
$h = '';
/**/
      foreach ( $headers as $header )
      {
        $pos = strpos($header,':');
        if ( $pos )
        {
          $name  = strtolower(trim(substr($header,0,$pos)));
/**/
$h .= "$name,";
/**/
          $value = substr($header,$pos+1);
          $headerData[$name] = trim($value);
          if ( in_array($name,$desiredHeaders) )
          {
            $canonicalizedHeaders .= $name .':'. trim(str_replace(array('\n','\r'),' ',$value))."\n";
          }
        }
      }
      $canonicalizedHeaders = trim($canonicalizedHeaders);

      // just the clean url path - JS LOGIC IS DIFF - gets up to ? OR all url - this is prob wrong on zack's part
      $url_parts = $this->parseUrl($url);
      $canonicalizedResource = ( !empty($url_parts) && !empty($url_parts['path']) ) ? trim($url_parts['path']) : '';
        
      /// hash of the body - md5
      $http_params   = http_build_query( $params, '', '&' );
/**/
print_r(array(
    'raw'       => $http_params,

    'md5'       =>               md5($http_params),
    '64(md5)'   => base64_encode(md5($http_params)),

    'md5t'       =>               md5($http_params,true),
    '64(md5t)'   => base64_encode(md5($http_params,true)),

    'hash'      =>               hash('md5',$http_params),
    '64(hash)'  => base64_encode(hash('md5',$http_params)),

    'hashB'      =>               hash('md5',$http_params, true),
    '64(hashB)'  => base64_encode(hash('md5',$http_params, true)),

    'hash_hmac'     =>               hash_hmac('md5', $http_params, null ),
    '64(hash_hmac)' => base64_encode(hash_hmac('md5', $http_params, null )),

    'hash_hmacB'     =>               hash_hmac('md5', $http_params, null, true ),
    '64(hash_hmacB)' => base64_encode(hash_hmac('md5', $http_params, null, true )),
));
/**/
      $hashedData    = base64_encode(md5($http_params,true));

      // array of: date,content-type,http method;
      $requestMethod = strtoupper($http_method);

      // put it all together
      $signingString = "{$requestMethod}\n".
                       "{$hashedData}\n".
                       "{$canonicalizedHeaders}\n".
                       "{$canonicalizedResource}";
      /// hash up our thingy
      $computedHash  = base64_encode(hash_hmac('md5', $signingString, $this->api['key_shared'], true ));
/**/
print_r(array( 
    'm'=>$requestMethod,
    'd'=>$hashedData,
    'h'=>$canonicalizedHeaders,
    'r'=>$canonicalizedResource,
    'ss'=>$signingString, 
    'sk'=>$this->api['key_public'], 
    'hd'=>$hashedData,
    'h5'=>hash_hmac('md5', $signingString, $this->api['key_shared'], true ), 
    '64(h5)'=>$computedHash, 
    'pk'=>$this->api['key_public'], 
    'final'=>"{$this->api['key_public']}:{$computedHash}" 
));
/**/
      /// share public key are our hash
      return "{$this->api['key_public']}:{$computedHash}";# \n\n header order: $h \n\n Signing String:\n $signingString";
    }
}
