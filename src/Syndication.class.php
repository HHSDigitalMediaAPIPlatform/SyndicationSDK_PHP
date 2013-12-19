<?php
/**
 * @package Syndication\SDK\PHP
 */ 

/**
 * SyndicationResponse() 
 * 
 * @author Dan Narkiewicz <dnarkiewicz@ctacorp.com> 
 */
class SyndicationResponse()
{
  var $format  = null;
  var $status  = null;
  var $message = array();
  var $results = array();
  var $success = null;

  /**
   * Constructor for Response 
   * 
   * @access protected
   * 
   * @return self
   */
  function __construct()
  {
    $this->format  = null;
    $this->status  = null;
    $this->message = array();
    $this->results = array();
    $this->success = null;
  }
}



/**
 * Syndication 
 * 
 * @author Dan Narkiewicz <dnarkiewicz@ctacorp.com> 
 */
class Syndication
{
    /// these are not hardcoded - they are read in from somewhere
    var $api = array(
        'url'      => '',
        'tiny_url' => '',
        'cms_url'  => '',
        'cms_id'   => '',
        'api_key'  => '',
        'timeout'  => '',
    );

    var $empty_response_message = array(
        'errorMessage' => null,
        'errorDetail'  => null,
        'errorCode'    => null,
    );
    
    /**
     * Constructor for Syndication interface
     * 
     * @param mixed $api mixed if array: settings. if string: config file path. 
     * @access protected
     * 
     * @return void
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

    /// API FUNCTIONS

    /**
     * Gets a list of all MediaType Names [string]
     * 
     * @access public
     * 
     * @return SyndicationResponse
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
     * Gets a list of Organizations [{ id:long, name:string, abv:string, url:string }]
     * 
     * @param array $params { max:int, offset:int, sort:string, order:string }
     *  
     * @access public
     * 
     * @return SyndicationResponse
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
     * Gets a single Organization { id:long, name:string, abv:string, url:string }  
     * 
     * @param mixed $id Numeric Id of the organization 
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getOrganizationByOrganizationId ( $id )
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
     * Gets a list of all Campaigns [{ id:long, name:string, description:string, startDate:date, endDate:date, organization:organization }]  
     * 
     * @param array $params { max:int, offset:int, sort:string, order:string }
     *  
     * @access public
     * 
     * @return SyndicationResponse
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
     * Gets a single Campaign { id:long, name:string, description:string, startDate:date, endDate:date, organization:organization }  
     * 
     * @param mixed $id Numeric Id of the campaign 
     *  
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getCampaignByCampaignId ( $id )
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
     * Gets a list of all Languages [{ id:long, name:string, value:string }]  
     * 
     * @param array $params { max:int, offset:int, sort:string, order:string }
     *  
     * @access public
     * 
     * @return SyndicationResponse
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
     * Gets a single Language { id:long, name:string, value:string }  
     * 
     * @param mixed $id Numeric Id of the language 
     *  
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getLanguageByLanguageId ( $id )
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
     * Gets a list of all Tags [{ id:long, name:string }]  
     * 
     * @param array $params { sort:string, order:string }
     *  
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getAllTags ( $params )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags.json",$params);
            return $this->createResponse($result,'get All Tags');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    /**
     * Gets a single Tag { id:long, name:string, value:string }  
     * 
     * @param mixed $id Numeric Id of the tag 
     *  
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getTagByTagId ( $id )
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
     * Gets a list of Tags related to a specific Tag [{ id:long, name:string, value:string }]
     * 
     * @param mixed $id Numeric Id of the tag 
     *  
     * @access public
     * 
     * @return SyndicationResponse
     */
    function getRelatedTagsByTagId ( $id )
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
     * Gets a list of Media MetaData [{ id:long, name:string, description:string, licenseInfo:string, sourceUri:string, dateAuthored:date, dateUpdated:date, language:language, active:boolean, externalGuid:string, hash:string, organization:organization }] 
     * Makes a different API call depending on $query. 
     * 
     * @param mixed $query if string, send as query to all-column text search. if array, send as per-column search params. 
     *      { max:int, offset:int, sort:string, order:string, mediaType:string, nameContains:string, descriptionContains:string, licenseInfoContains:string, sourceUri:string, sourceUriContains:string, dateAuthored:string, authoredSinceDate:string, authoredBeforeDate:string, authoredInRange:string, updatedSinceDate:string, updatedBeforeDate:string, updatedInRange:long, languageName:string, languageValue:string, hash:string, hashContains:string, organizationId:long, organizationName:string, organizationNameContains:string, organizationAbv:string, organizationAbvContains:string, tagIds:csv_string, restrictToSet:csv_string }
     * 
     * @access public
     * 
     * @return SyndicationResponse
     */
    function searchMediaMetadata( $query )
    {
        try
        {
            if ( is_array($query) )
            {
              $result = $this->apiCall('get',"{$this->api['url']}/media.json",$query);
            } else {  
              $params = array( 'q' => $query );
              $result = $this->apiCall('get',"{$this->api['url']}/",$params,'json');
            }
            return $this->createResponse($result,'search Resources','Search Criteria');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getMediaMetadataByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}.json");
            return $this->createResponse($result,'get MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getMediaMetadataByMediaUrl ( $url )
    {
        try
        {
            $params = array( 'sourceUri' => $url );
            $result = $this->apiCall('get',"{$this->api['url']}/media.json",$params);
            return $this->createResponse($result,'get MetaData','Url');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getMediaMetadataByTagId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/media.json");
            return $this->createResponse($result,'get MetaData','Tag Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function publish ( $params )
    {
        /// syndication will always return metadata for one content item
        /// if publishing a collection, we get collection item, which contains list of any sub-items also generated
        try
        {
            $type_path = $params['mt'];
            // dirty pluralization
            if( !in_array($type_path,array('SocialMedia','Audio')) ) { $type_path .= 's'; }
            $type_path{0} = strtolower($type_path{0});
            //$query = http_build_query($params);
            $result = $this->apiCall('post',"{$this->api['url']}/media/$type_path",$params,'json');
            return $this->createResponse($result,'Publish');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function unPublishByMediaId ( $id )
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

    function subscribeByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('post',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function unSubscribeByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('delete',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Un-Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function getCmsMetadataByCmsId ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/cms/{$this->api['cms_id']}",array(),'json');
            return $this->createResponse($result,'get My CMS Information');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getAllSubscriptions ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/subscriptions.json",array(),'json');
            return $this->createResponse($result,'get My Subscriptions');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getSubscriptionByCmsId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'get My Subscriptions');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function getContentByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/content");
            return $this->createResponse($result,'get Content','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getPreviewByMediaId ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/preview.jpg");
            return $this->createResponse($result,'get Content Preview','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getThumbnailByMediaId ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/thumbnail.jpg");
            return $this->createResponse($result,'get Content Thumbnail','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getEmbeddedHtmlByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/embed");
            return $this->createResponse($result,'get Embedded Html','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getSnippetCodeByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/snippetCode");
            return $this->createResponse($result,'get Snippet Code','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getYoutubeMetadataByMediaId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeMedaData.json");
            return $this->createResponse($result,'get YouTube MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getYoutubeIframeByMediaId ( $id, $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeIframe",$params);
            return $this->createResponse($result,'get YouTube IFrame','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /// INTERNAL FUNCTIONS

    function guessFormatFromUrl ($url)
    {
        $simple_url = "/^(?:(?P<scheme>[^:\/?#]+):\/\/)(?:(?P<userinfo>[^\/@]*)@)?(?P<host>[^\/?#]*)(?P<path>[^?#]*?(?:\.(?P<format>[^\.?#]*))?)?(?:\?(?P<query>[^#]*))?(?:#(?P<fragment>.*))?$/i";
        if ( !preg_match($simple_url, $url, $url_parts) )
        {
            return 'raw';
        }
        $format = !empty($url_parts['format'])?$url_parts['format']:'raw';
        if ( in_array($format,array('jpg','jpeg','png','gif')) ) { $format = 'image'; }
        return $format;
    }
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

    function createResponse ( $from, $action="Process Request", $key=null )
    {
        /// an exception was thrown
        if ( is_subclass_of($from,'Exception') )
        {
            $response = $this->empty_response;
            $response['success']           = false;
            $response['meta']['status']    = $from->getCode();
            $response['meta']['format']    = 'Exception';
            $response['meta']['message'][] = array(
                'errorCode'    => $from->getCode(),
                'errorMessage' => $from->getMessage(),
                'errorDetail'  => "{$action} Exception"
            );
            return $response;

            /// response from server
        } else if ( is_array($from)
            && !empty($from['http'])
            && !empty($from['format']) )
        {
            $status = intval($from['http']['http_code']);
            if ( $from['format']=='json' )
            {
                /// we require a [meta] and [results] from any json response
                if ( is_array($from['content']) && isset($from['content']['meta']) && isset($from['content']['results']) )
                {
                    $response = $from['content'];
                } else {
                    $response = $this->empty_response;
                    $response['results'] = array($from['content']);
                }

                $response['meta']['status'] = $status;
                $response['meta']['format'] = 'json';

                if        ( $status>=200 && $status<=299 )
                {
                    $response['success'] = true;
                } else if ( $status>=400 && $status<=499 ) {
                    if ( $status == 401 ) {
                        $errorDetail = "Unauthorized. Check API Key.";
                    } else if ( $status == 404 && !empty($key) ) {
                        $errorDetail = "Failed to {$action}. {$key} Not Found.";
                    } else {
                        $errorDetail = "Failed to {$action}. Request Error.";
                    }
                    $response['success']  = false;
                    $response['meta']['message'][]  = array(
                        'errorCode'    => $status,
                        'errorMessage' => $this->httpStatusMessage($status),
                        'errorDetail'  => $errorDetail
                    );
                } else if ( $status>=500 && $status<=599 ) {
                    $response['success']  = false;
                    $response['meta']['message'][]  = array(
                        'errorCode'    => $status,
                        'errorMessage' => $this->httpStatusMessage($status),
                        'errorDetail'  => "Failed to {$action}. Server Error."
                    );
                }
                return $response;
            } else if ( $from['format']=='image' ) {
                $response = $this->empty_response;
                $response['success'] = true;
                $response['meta']['status'] = $status;
                $response['meta']['format'] = 'image';
                /// imagecreatefromstring ?
                $response['results'] = $from['content'];
                return $response;
            } else {
                $response = $this->empty_response;
                $response['success'] = true;
                $response['meta']['status'] = $status;
                $response['meta']['format'] = $from['format'];
                /// filter html ?
                $response['results'] = $from['content'];
                return $response;
            }
        }
        /// we got something weird - can't deal with this
        $response = $this->empty_response;
        $response['success'] = false;
        $status = null;
        if ( is_array($from) && !empty($from['http']) && isset($from['http']['http_status']) )
        {
            $status = $from['http']['http_status'];
        }
        $response['meta']['message'][] = array(
            'errorCode'    => $status,
            'errorMessage' => $this->httpStatusMessage($status),
            'errorDetail'  => "Unknown response from Server."
        );
        $response['results'] = $from;
        return $response;
    }

    function apiCall ( $http_type, $url, $params=array(), $response_format=null )
    {
        if ( empty($response_format) )
        {
            $response_format = $this->guessFormatFromUrl($url);
        }

        /// our request format type
        //$request_headers =  array('Content-Type: text/xml; charset=UTF-8');
        $request_headers = array('Content-Type: application/x-www-form-urlencoded');
        //$request_headers = array();

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

        $curl = $this->apiBuildCurlRequest( $http_type, $url, $params, $request_headers, $response_format ); 

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

    function apiBuildCurlRequest( $http_type, $url, $params=array(), $headers=array(), $response_format='' )
    {
        $http_params = http_build_query( $params, '', '&' );
       
        $headers[] = "Date: ".gmdate("D, d M Y H:i:s", time())." GMT";
        //$headers[] = 'Content-Type: text/xml; charset=UTF-8';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_USERAGENT,      'Syndication-Client/php-drupal v1'); // Useragent string to use for request
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        if ( $response_format=='image' )
        {
            curl_setopt($curl, CURLOPT_HEADER,         false );
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true  );
        }

        switch ( strtolower($http_type) )
        {
            case 'post':
                //curl_setopt( $curl, CURLOPT_POST,       true    );
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST'    );
                curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'put':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT'        );
                curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'delete':
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
                curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params  );
                $headers[] = 'Content-length: '.strlen($http_params);
                break;
            case 'get':
            default:
                curl_setopt( $curl, CURLOPT_HTTPGET, true ); 
                $url .= (strpos($url,'?')===FALSE?'?':'&') . $http_params;
                $headers[] = 'Content-length: 0';
                break;
        }
        curl_setopt( $curl, CURLOPT_HTTPHEADER,     $headers);

        curl_setopt( $curl, CURLOPT_VERBOSE, 1 );
        curl_setopt( $curl, CURLOPT_STDERR,  STDOUT );
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

    function generateApiKey( $http_type, $url, $params=array(), $headers=array() )
    {
      $canonicalizedHeaders  = ''; //"ordered and scrubbed headers: date,content-type,content-length";
      $desiredHeaders = array('date','content-type','content-length');
      foreach ( $headers as $header )
      {
        $pos = strpos(':',$header);
        if ( $pos )
        {
          $name  = strtolower(substr($header,0,$pos));
          if ( in_array($name,$desiredHeaders) )
          {
            $value = substr($header,$pos+1);
            $h = $name . str_replace(array('\n','\r'),' ',$value)
            $h = $name . str_replace(array('\n','\r'),' ',$value)
          }       
        }      
      }
      $canonicalizedResource = "path of request: no host, no query"; 
      $hashedData    = "hash up the body of the request";
      $requestData   = "get wrapped up: date,content-type,method";
      $signingString = "concat stuff we just got";
      $computedHash  = "hash signing string";
      $authHeader    = "header Authentication: syndication_api_key {$secret}:{$computedHash}";  
    }

}