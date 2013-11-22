<?php

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

    var $params_allowed = array(
        'pagination' => array(
            'max', 'offset', 'sort', 'order'
        ),
        'media_search' => array(
            'mt', 'name', 'description', 'licenseInfo', 'sourceUri', 'dateAuthored', 'dateUpdated',
            'languageId', 'languageName', 'languageCode', 'hash', 'organizationId', 'organizationName',
            'organizationAbv', 'tagIds'
        ),
        'youtubeiframe' => array(
            'callback'
        ),
        'image' => array(
            'imageFloat', 'imageMargin'
        ),
        'requests_post' => array(
            'requestedUrl', 'contactEmail', 'requesterNote'
        ),
        'publish' => array(
            'mt', 'name', 'sourceUri', 'dateAuthored', 'dateUpdated', 'language', 'organization', 
            'description', 'liscenseInfo', 'externalGuid', 'hash', 'duration', 'seoText', 'width', 'height', 'format', 'altText', 'code'
        )
    );
    var $params_required = array(
      'required' => array(
        'always' => array( 'mt', 'name', 'sourceUri', 'dateAuthored', 'dateUpdated', 'language', 'organization' ),
        'audio'  => array( 'duration' ),
        'video'  => array( 'duration', 'width', 'height', 'altText' ),
        'widget' => array( 'width', 'height', 'code' ),
        'image'       => array( 'width', 'height', 'format', 'altText' ),
        'infographic' => array( 'width', 'height', 'format', 'altText' ),
      )
    );

    var $empty_response_message = array(
      'errorMessage' => null,
      'errorDetail'  => null,
      'errorCode'    => null,
    );
    var $empty_response = array(
        'meta'    => array(
            'format'  => null,
            'status'  => null,
            'message' => array() 
        ),
        'results' => null,
        'success' => null,
    );

	  function __construct( $api=null )
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

	  function getAllMediaTypes()
	  {
		    try
		    {
            $result = $this->apiCall('get',"{$this->api['url']}/mediaTypes.json");
            return $this->createResponse($result,'get All MediaTypes');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
	  }

    function getAllOrganizations ($params=array())
    {
        try
        {
            $params = $this->restrictParams($params,'pagination');
            $result = $this->apiCall('get',"{$this->api['url']}/organizations.json",$params);
            return $this->createResponse($result,'get All Organizations');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
	  }
    function getOrganizationById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/organizations/{$id}.json");
            return $this->createResponse($result,'get Organization','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }

    function getAllCampaigns ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns.json");
            return $this->createResponse($result,'get All Campaigns');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }
    function getCampaignById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns/{$id}.json");
            return $this->createResponse($result,'get Campaign','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }

    function getAllLanguages ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages.json");
            return $this->createResponse($result,'get All Languages');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }
    function getLanguageById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages/{$id}.json");
            return $this->createResponse($result,'get Language','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }

    function getAllTags ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags.json");
            return $this->createResponse($result,'get All Tags');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }
    function getTagById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}.json");
            return $this->createResponse($result,'get Tag','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }
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


	  function getPreviewById ( $id, $params=array() )
	  {
        try
        {
            $params = $this->restrictParams($params,'image');
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/preview.jpg");
            return $this->createResponse($result,'get Content Preview','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }
	  function getThumbnailById ( $id, $params=array() )
	  {
        try
        {
            $params = $this->restrictParams($params,'image');
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/thumbnail.jpg");
            return $this->createResponse($result,'get Content Thumbnail','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
    }

	  function getEmbeddedHtmlById ( $id )
	  {
	      try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/embed");
            return $this->createResponse($result,'get Embedded Html','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        } 
   }



	  function getSnippetCodeById ( $id )
	  {
	      try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/snippetCode");
            return $this->createResponse($result,'get Snippet Code','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
	  }

	  function getYoutubeMetadataById ( $id )
	  {
	      try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeMedaData.json");
            return $this->createResponse($result,'get YouTube MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
	   }
  	function getYoutubeIframeById ( $id, $params=array() )
	  {
        try
        {
            $params = $this->restrictParams($params,'youtubeIframe');
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeIframe",$params);
            return $this->createResponse($result,'get YouTube IFrame','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function getMetadataByTagId ( $id )
    {
        try
		    {
			      $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/media.json");
             return $this->createResponse($result,'get MetaData','Tag Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }
    function getMetadataById ( $id )
	  {
		    try
		    {
			      $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}.json");
            return $this->createResponse($result,'get MetaData','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /// full search
	  function getMetadata( $params )
	  {
		    try
		    {
            $params = $this->restrictParams($params,'pagination','media_search');
		        if ( empty($params['mt']) ) { $params['mt'] = 'Html'; } /// temp restriction
			      $result = $this->apiCall('get',"{$this->api['url']}/media.json",$params);
	          return $this->createResponse($result,'search MetaData','Search Criteria');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
   	}

    /// really just uses search
	  function getMetadataByUrl( $url )
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

	  function subscribe( $id )
	  {
        try
	 	    {
			      $result = $this->apiCall('post',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
   }

    function unSubscribe( $id )
    {
        try
	 	    {
            $result = $this->apiCall('delete',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'Un-Subscribe','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
   }


    function getAllSubscriptions()
    {
        try
	 	    {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/subscriptions.json",array(),'json');
            return $this->createResponse($result,'get My Subscriptions');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function getSubscriptionById( $id )
    {
        try
	 	    {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/subscriptions/{$id}",array(),'json');
            return $this->createResponse($result,'get My Subscriptions');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    function getCmsMetadata()
    {
        try
	 	    {
            $result = $this->apiCall('get',"{$this->api['cms_url']}/cms/{$this->api['cms_id']}",array(),'json');
            return $this->createResponse($result,'get My CMS Information');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

	  function publish( $params )
	  {
	      /// syndication will always return metadata for one content item
	      /// if publishing a collection, we get collection item, which contains list of any sub-items also generated
		    try
        {
            $params = $this->restrictParams($params,'publish');
            $query = http_build_query($params);
            $result = $this->apiCall('post',"{$this->api['url']}/media?".$query,array(),'json');
            return $this->createResponse($result,'Publish');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

	  function unPublish( $id )
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

	  function getContentById( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/content");
	          return $this->createResponse($result,'get Content','Id');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
	  }

    /* vv do i even need to use these two ? */
    function restrictParams( $params )
    {
        $allowed_fields = array();
        $args     = func_get_args();
        $last_arg = func_num_args()-1;
        if ( $last_arg == 1 )
        {
            if ( isset($this->params_allowed[$args[1]]) ) 
            {
                $allowed_fields = $this->params_allowed[$args[1]];
            }
        } else {
            $allowed_fields = array();
            for ( $a=1; $a<=$last_arg; $a++ )
            {
                if ( isset($this->params_allowed[$args[$a]]) ) 
                {
                  $allowed_fields = array_merge( $allowed_fields, $this->params_allowed[$args[$a]] );
                }
            }
        }
        if ( empty($allowed_fields) ) { return $params; }
        $allowed = array_intersect_key( $params, array_flip($allowed_fields) );
        return $allowed;
    }
    function hasRequiredParams( $params, $mt )
    {
        if ( !empty($this->params_required[$mt]) ) 
        {
          $required_fields = $this->params_required[$mt];
          foreach ( $required_fields as $field )
          {
            if ( !isset($params[$field]) ) { return false; } 
          }
        }
        return true;
    }
    /* ^^ do i even need to use these two ? */

    function guessFormatFromUrl($url)
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
    function guessFormatFromResponse($response)
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
    function httpStatusMessage( $status )
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

    function createResponse( $from, $action="Process Request", $key=null )
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

    function apiCall( $http_type, $url, $params=array(), $response_format=null )
    {
      if ( empty($response_format) )
      {
        $response_format = $this->guessFormatFromUrl($url);
      }

      /// our request format type
      $request_headers =  array('Content-Type: text/xml; charset=UTF-8');
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

      /// does syndication also need to check my key for update pushes?
      //if ( substr( $url, 0, strlen($this->api['cms_url']) ) == $this->api['cms_url'] )
      //{
      $request_headers[] = 'Authorization: syndication_api_key '. $this->api['api_key'];
      //}

      /// does the path end in a prefix ? if so, that is the response format
      $http_params = http_build_query( $params, '', '&' );
      $curl = curl_init();

      // 'Accept: application/json'
      curl_setopt($curl, CURLOPT_HTTPHEADER,     $request_headers);
      curl_setopt($curl, CURLOPT_USERAGENT,      'Syndication-Client/php-drupal v1'); // Useragent string to use for request
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true );
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
      if ( $response_format=='image' ) 
      {
        curl_setopt($curl, CURLOPT_HEADER,         false );
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true  );
      }

      /**/ /// timeouts
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10 );                    // seconds attempting to connect
      curl_setopt($curl, CURLOPT_TIMEOUT,        $this->api['timeout'] ); // seconds cURL allowed to execute
      /**/	

      /** /// forces new connections
      curl_setopt($curl, CURLOPT_FORBID_REUSE,  true );
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true );
      curl_setopt($curl, CURLOPT_MAXCONNECTS,   1);
      /**/	

      switch ( strtolower($http_type) )
      {
        case 'post':
          curl_setopt( $curl, CURLOPT_POST,       true    );
          curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
          break;
        case 'put':
          curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "PUT"        );
          curl_setopt( $curl, CURLOPT_POSTFIELDS,    $http_params );
          break;
        case 'delete':
          curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "DELETE" );
          curl_setopt( $curl, CURLOPT_POSTFIELDS,    $params  );
          break;
        case 'get':
        default:
          curl_setopt( $curl, CURLOPT_HTTPGET, true ); $url .= (strpos($url,'?')===FALSE?'?':'') . $http_params; 
          break;
      }
      curl_setopt( $curl, CURLOPT_URL, $url );

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
        /** BEGIN BS ** /
        /// clean up data - array with single null value is really empty
        if ( empty($decoded['results']) || count($decoded['results'])==1 && empty($decoded['results'][0]) )
        {
          $decoded['results'] = array();
          if ( isset($content['meta']) && isset($content['meta']['pagination']) )
          {
            /// no results means no count
            $content['meta']['pagination']['count'] = 0;
            /// if the count was misreported
            /// and we are looking at the beginning of the list, then the total might be wrong
            /// really i might get an empty set with a positive total if i have asked for an offset greater than total
            if ( $content['meta']['pagination']['total'] && $content['meta']['pagination']['offset']<$content['meta']['pagination']['total'] )
            {
              /// empty set is allowed               
            } else {
              /// empty set is not allowed, we will only get empty set if total set is empty
              $content['meta']['pagination']['total'] = 0;
            }
            /// if there is really one legit record, but we have asked for a far-away offset (>1)
            /// a total of 1 might be correct, and we still could have a misreported 1 count of the resultset
          }
        }
        $decoded['results'] = (array)$decoded['results'];
        /** END BS **/
        /// clean up data - require 'results' as an array always
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
}
