<?php

/// this class should be framework agnostic : transferable between drupal and wordpress

class Syndication
{
    /// these are not hardcoded - they are read in from somewhere
	var $api = array(
		'url'      => '',
		'tiny_url' => '',
		'cms_url'  => '',
		'cms_id'   => '',
		'cms_key'  => '',
		'timeout'  => '',
	);

    var $param_restrictions = array(
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
        )
    );

    var $empty_json_response = array(
        'meta' => array(
            'message' => array(
                'status'       => null,
                'errorMessage' => null,
                'errorDetail'  => null,
                'errorCode'    => null,
            ),
        ),
        'success' => null,
        'results' => null,
    );

	function __construct( $api=null )
	{
	    $this->api = array(
            'url'      => 'http://ctacdev.com:8090/Syndication/api/v1/resources',
       		'tiny_url' => 'http://ctacdev.com:8082/',
            'cms_url'  => 'http://ctacdev.com:8090/CMS_Manager/api/v1/resources',
            'cms_id'   => 'drupal_cms_1',
            'cms_key'  => 'sha256',
            'timeout'  => 60
        );
	}


    /// full list only
	function getAllMediaTypes()
	{
		try
		{
            $result = $this->apiCall('get',"{$this->api['url']}/mediaTypes.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'MediaTypes Lookup Failed. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'MediaTypes Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
			$response = $this->empty_json_response;
			$response['success']                         = false;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
    		return $response;
		}
	}

    /// pagination on full list only
    function getAllOrganizations ($params)
    {
        try
        {
            $params   = $this->restrictParams($params,'pagination');
            $result = $this->apiCall('get',"{$this->api['url']}/organizations.json",$params);
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Organizations Lookup Failed. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Organizations Lookup Failed. Server Error.';
            }
            return $response;

        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
    function getOrganizationById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/organizations/{$id}.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No Organization Found For Id.";
                } else {
                    $errorDetail = "Organization Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Organization Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }

    /// full list only
    function getAllCampaigns ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Campaigns Lookup Failed. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Campaigns Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
    function getCampaignById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/campaigns/{$id}.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Campaign Lookup Failed. Campaign Id Not Found.";
                } else {
                    $errorDetail = "Campaign Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Campaign Lookup Not Processed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }


    /// full list only
    function getAllLanguages ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Languages Lookup Failed. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Languages Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
    function getLanguageById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/languages/{$id}.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No Language Found For Id.";
                } else {
                    $errorDetail = "Language Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Language Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }

    /// full list only
    function getAllTags ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Tags Lookup Failed. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Tags Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
    function getTagById ($id)
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No Tag Found For Id.";
                } else {
                    $errorDetail = "Tag Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Tag Lookup Failed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
			$response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }

    /// formatted views of media come by id only
	function getPreviewById ( $id, $params=array() )
	{
        try
        {
            $params = $this->restrictParams($params,'image');

            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/preview.jpg");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Preview Lookup Failed. Media Id Not Found.";
                } else {
                    $errorDetail = "Preview Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Preview Lookup Not Processed. Server Error.';
            }
            return  $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
	function getThumbnailById ( $id, $params=array() )
	{
        try
        {
            $params = $this->restrictParams($params,'image');

            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/thumbnail.jpg");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Thumbnail Lookup Failed. Media Id Not Found.";
                } else {
                    $errorDetail = "Thumbnail Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Thumbnail Lookup Not Processed. Server Error.';
            }
            return  $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }
	function getSnippetById ( $id )
	{
	    try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/snippetCode.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Snippet Code Lookup Failed. Media Id Not Found.";
                } else {
                    $errorDetail = "Snippet Code Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Snippet Code Lookup Not Processed. Server Error.';
            }
            return  $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
	}
	function getYoutubeMetadataById ( $id )
	{
	    try
        {
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeMedaData.json");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Youtube Metadata Lookup Failed. Media Id Not Found.";
                } else {
                    $errorDetail = "Youtube Metadata Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Youtube Metadata Lookup Not Processed. Server Error.';
            }
            return  $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
	}

	function getYoutubeIframeById ( $id, $params=array() )
	{
        try
        {
            $params = $this->restrictParams($params,'youtubeIframe');
            $result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/youtubeIframe",$params);

            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "YouTube Iframe Lookup Failed. Media Id Not Found.";
                } else {
                    $errorDetail = "YouTube Iframe Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response = $this->empty_json_response;
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'YouTube Iframe Lookup Not Processed. Server Error.';
            }
            return $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
	}

    function getMetadataByTagId ( $id )
    {
        try
		{
			$result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/media.json");
			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Tagged MetaData Lookup Failed. Tag Id Not Found.";
                } else {
                    $errorDetail = "Tagged MetaData Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Tagged MetaData Lookup Not Processed. Server Error.';
            }
            return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
      		return $response;
		}
    }
    function getMetadataByRelatedTagId ( $id )
    {
        try
		{
			$result = $this->apiCall('get',"{$this->api['url']}/tags/{$id}/related.json");
			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "Tag-Related MetaData Lookup Failed. Tag Id Not Found.";
                } else {
                    $errorDetail = "Tag-Related MetaData Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Tag-Related MetaData Lookup Not Processed. Server Error.';
            }
            return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
      		return $response;
		}
    }
    function getMetadataById ( $id )
	{
		try
		{
			$result = $this->apiCall('get',"{$this->api['url']}/media/{$id}.json");
			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No MetaData Found For Id.";
                } else {
                    $errorDetail = "MetaData Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'MetaData Lookup Failed. Server Error.';
            }
            return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
      		return $response;
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
			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No MetaData Found For Search Parmas.";
                } else {
                    $errorDetail = "MetaData Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'MetaData Lookup Failed. Server Error.';
            }
            return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
     		return $response;
        }
	}

    /// really just uses search
	function getMetadataByUrl( $url )
	{
		try
		{
			$params = array( 'sourceUri' => $url );
			$result = $this->apiCall('get',"{$this->api['url']}/media.json",$params);

			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No MetaData Found For Url.";
                } else {
                    $errorDetail = "MetaData Lookup Failed. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'MetaData Lookup Failed. Server Error.';
            }
            return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
            $response['success']                         = false;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
     		return $response;
		}
	}

	function subscribe( $syndication_id )
	{
		try
		{
			$result = $this->apiCall('post',"{$this->api['cms_url']}/subscriptions/{$syndication_id}",array(
			    'cmsId'  => $this->api['cms_id'],
			    'cmsKey' => $this->api['cms_key'],
			));
			$status = intval($result['http']['http_code']);

            $response = $this->empty_json_response;
			$response['meta']['message']['status'] = $status;

			if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Subscribe. Request Error.';
 			} else if ( $status>=500 && $status<=599 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Subscribe. Server Error.';
  			}
			return  $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
            $response['success']                         = false;
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
      		return $response;
		}
	}

    function unSubscribe( $syndication_id )
    {
        try
        {
            $result = $this->apiCall('delete',"{$this->api['cms_url']}/subscriptions/{$syndication_id}");
            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Un-Subscribe. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response = $this->empty_json_response;
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Un-Subscribe. Server Error.';
            }
            return  $response;
        } catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['success']                         = false;
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
        }
    }

	function publish( $url, $collection )
	{
	    /// syndication will always return metadata for one content item
	    /// if publishing a collection, we get collection item, which contains list of any sub-items also generated
		try
		{
			$result = $this->apiCall('post',"{$this->api['url']}/media",array(
			    'sourceUrl'        => $url,
			    'cmsId'            => $this->api['cms_id'],
			    'cmsKey'           => $this->api['cms_key'],
			    'createCollection' => empty($collection)?'0':'1',
			));

			$status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Publish. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response = $this->empty_json_response;
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Publish. Server Error.';
            }
            return $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
            $response['success']                         = false;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
		}
	}

	function unPublish( $syndication_id )
	{
	    /// syndication will always return metadata for one content item
	    /// if publishing a collection, we get collection item, which contains list of any sub-items also generated
		try
		{
            $result = $this->apiCall('delete',"{$this->api['url']}/media/{$syndication_id}",array(
                'cmsId'     => $this->api['cms_id'],
                'cmsKey'    => $this->api['cms_key'],
            ));

            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Un-Publish. Request Error.';
            } else if ( $status>=500 && $status<=599 ) {
                $response = $this->empty_json_response;
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Failed to Un-Publish. Server Error.';
            }
            return $response;
		} catch ( Exception $e ) {
			$response = $this->empty_json_response;
            $response['success']                         = false;
			$response['meta']['message']['status']       = $e->getCode();
			$response['meta']['message']['errorCode']    = $e->getCode();
			$response['meta']['message']['errorMessage'] = $e->getMessage();
			$response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
		}
	}

	function getContentById( $id )
	{
		try
		{
			$result = $this->apiCall('get',"{$this->api['url']}/media/{$id}/content");

            $status = intval($result['http']['http_code']);

            $response = $result['content'];
            $response['meta']['message']['status'] = $status;

            if        ( $status>=200 && $status<=299 )
            {
                $response['success']                         = true;
            } else if ( $status>=400 && $status<=499 ) {
                if ( $status == 404 ) {
                    $errorDetail = "No Content Found For Id.";
                } else {
                    $errorDetail = "No Content Found. Request Error.";
                }
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = $errorDetail;
            } else if ( $status>=500 && $status<=599 ) {
                $response = $this->empty_json_response;
                $response['success']                         = false;
                $response['meta']['message']['errorCode']    = $status;
                $response['meta']['message']['errorMessage'] = $this->httpStatusMessage($status);
                $response['meta']['message']['errorDetail']  = 'Request Not Processed. Server Error.';
            }
            return $response;
		} catch ( Exception $e ) {
            $response = $this->empty_json_response;
            $response['success']                         = false;
            $response['meta']['message']['status']       = $e->getCode();
            $response['meta']['message']['errorCode']    = $e->getCode();
            $response['meta']['message']['errorMessage'] = $e->getMessage();
            $response['meta']['message']['errorDetail']  = 'API Exception';
            return $response;
		}
	}

    function restrictParams( $params )
    {
        $args     = func_get_args();
        $last_arg = func_num_args()-1;
        if ( $last_arg == 1 )
        {
            $all_restrictions = (array)$args[1];
        } else {
            $all_restrictions = array();
            for ( $a=1; $a<=$last_arg; $a++ )
            {
                $all_restrictions = array_merge( $all_restrictions, $this->param_restrictions[$args[$a]] );
            }
        }
        $restricted = array_intersect_key( $params, array_flip($all_restrictions) );
        return $restricted;
    }
    function guessFormatFromUrl($url)
    {
        $simple_url = "/^(?:(?P<scheme>[^:\/?#]+):\/\/)(?:(?P<userinfo>[^\/@]*)@)?(?P<host>[^\/?#]*)(?P<path>[^?#]*?(?:\.(?P<format>[^\.?#]*))?)?(?:\?(?P<query>[^#]*))?(?:#(?P<fragment>.*))?$/i";
        if ( !preg_match($simple_url, $url, $url_parts) )
        {
            return 'raw';
        }
        return !empty($url_parts['format'])?$url_parts['format']:'raw';
    }
    function guessFormatFromResponse($response)
    {
        if ( stripos($response['content_type'],'json')  !== false ) { return 'json';  }
        if ( stripos($response['content_type'],'image') !== false ) { return 'image'; }
        if ( stripos($response['content_type'],'html')  !== false ) { return 'html';  }
        if ( stripos($response['content_type'],'text')  !== false ) { return 'text';  }
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
        return isset($messages[$status])? $messages[$status] : "Unknown";
    }

    /// returns fixed hash
    // {meta,pagination,result}
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
                case 'text':
                    $request_headers[] = 'Accept: text/plain; charset=UTF-8';
                    break;
                case 'image':
                    $request_headers[] = 'Accept: image/*;';
                    break;
                case 'jpg':
                    $request_headers[] = 'Accept: image/jpeg;';
                    break;
	        }
	    }
	    //if ( substr( $url, 0, strlen($this->api['cms_url']) ) == $this->api['cms_url'] )
	    //{
            $request_headers[] = 'Authorization: CMS_Manager '. base64_encode($this->api['cms_key']);
	    //}

	    /// does the path end in a prefix ? if so, that is the response format
		$http_params = http_build_query( $params, '', '&' );
		$curl = curl_init();

		// 'Accept: application/json'
		curl_setopt($curl, CURLOPT_HTTPHEADER,     $request_headers);
		curl_setopt($curl, CURLOPT_USERAGENT,      'Syndication-Client/php-drupal v1'); // Useragent string to use for request
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		/**/ /// timeouts
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10 );                    // seconds attempting to connect
		curl_setopt($curl, CURLOPT_TIMEOUT,        $this->api['timeout'] ); // seconds cURL allowed to execute
		/**/	

		/** /// forces new connections
		curl_setopt($curl, CURLOPT_FORBID_REUSE,  true);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
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
				curl_setopt( $curl, CURLOPT_POSTFIELDS,    $params );
				break;
			case 'get':
			default:
				curl_setopt( $curl, CURLOPT_HTTPGET, true );
				$url .= (strpos($url,'?')===FALSE?'?':'') . $http_params; 
				break;
		}
		curl_setopt( $curl, CURLOPT_URL, $url );

		$content = curl_exec($curl);
        $http    = curl_getinfo($curl);

		if ($content === false) {
		    curl_close($curl);
		    throw new Exception('Syndication: No Response: '. $http['http_code'], $http['http_code'] );
		    return null;
		}
		curl_close($curl);

	    if ( empty($response_format) )
	    {
	        $response_format = $this->guessFormatFromResponse($http);
	    }

        $response = array(
            'http'    => $http,
            'content' => $content,
        );

		/// test result content-type for JSON / HTML / IMG
		if( $response_format=='json' )
		{
            $decoded = json_decode($content,true);
            /// clean up data - require 'results' as an array
            if ( empty($decoded['results']) || count($decoded['results'])==1 && empty($decoded['results'][0]) )
            {
                $decoded['results'] = array();
            }
            $response['content'] = $decoded;
	    }
	    return $response;
	}
}