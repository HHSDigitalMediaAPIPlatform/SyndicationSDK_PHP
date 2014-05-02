<?php
/**
 * Syndication API SDK
 *
 * @package CTAC\Syndication
 */ 

require_once('SyndicationApiClient.class.php');

/**
 * Syndication 
 * 
 * @author Dan Narkiewicz <dnarkiewicz@ctacorp.com> 
 * @package CTAC\Syndication
 */
class Syndication extends SyndicationApiClient
{

    /// SYNDICATION API FUNCTIONS

    /**
     * Gets a list of MediaType Names 
     * 
     * @access public
     * 
     * @return SyndicationResponse ->results[]
     *      name        : string
     *      description : string
     */
    function getMediaTypes ()
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
     *      smallLogoUrl : string 
     */
    function getSources ( $params=array() )
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
    function getCampaigns ( $params=array() )
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

    /// function getMediaByCampaignId ( $id ) : defined below with getMedia*() functions

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
     *      id      : int
     *      name    : string
     *      isoCode : string
     */
    function getLanguages ( $params=array() )
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
     *      id      : int
     *      name    : string
     *      isoCode : string
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
     *      id       : int
     *      name     : string
     *      language : string (not a language obj or id)
     *      type     : string (not a type obj or id)
     */
    function getTags ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tags.json",$params);
            return $this->createResponse($result,'get Tags');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
     }

    /**
     * Gets a list of all Tag Types
     *
     * @access public
     * @return SyndicationResponse ->results[]
     *      id          : int
     *      name        : string
     *      description : string 
     */
    function getTagTypes ()
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/tagTypes.json");
            return $this->createResponse($result,'get All Tag Types');
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
     *      id       : int
     *      name     : string
     *      language : string
     *      type     : string
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
     *      id       : int
     *      name     : string
     *      language : string
     *      type     : string
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
    
    /// function getMediaByTagId : defined below with getMedia*() functions

    /**
     * Gets a list of Media MetaData.
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function getMedia( $query )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media.json",$query);
            return $this->createResponse($result,'search Media MetaData','Search Criteria');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of Resources organized by MediaType.
     * 
     * @param mixed $q string for all-column text search
     * 
     * @access public
     * @return SyndicationResponse ->results[mediaType][]
     *      id                      : int
     *      name                    : string
     */
    function searchResources( $q )
    {
        try
        {
            $params = ( is_array($q) && isset($q['q']) ) ? $q : array( 'q' => $q );
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources.json",$params);
            return $this->createResponse($result,'search Resources','Search Criteria');
        } catch ( Exception $e ) {
            return $this->createResponse($e,'API Call');
        }
    }

    /**
     * Gets a list of Media MetaData.
     * 
     * @param mixed $q string for all-column text search or array with key 'q'
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function searchMedia( $q )
    {
        try
        {
            $params = ( is_array($q) && isset($q['q']) ) ? $q : array( 'q' => $q );
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/searchResults.json",$params);
            return $this->createResponse($result,'search Media MetaData','q');
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
     *      extendedAttributes      : extendedAttribute[]
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
     *      extendedAttributes      : extendedAttribute[]
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
     * Gets a list of Media MetaData
     * 
     * @param mixed $id Numeric Id of a Campaign 
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function getMediaByCampaignId ( $id )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/campaigns/{$id}/media.json");
            return $this->createResponse($result,'get MetaData','Campaign Id');
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function getRelatedMediaById ( $id )
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
     * Gets Most popular MediaItems.
     *
     * @param mixed $params 
     *      max                          : int
     *      offset                       : int
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function getMostPopularMedia ( $params=array() )
    {
        try
        {
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/mostPopularMedia.json",$params);
            return $this->createResponse($result,'get Most popular Media','');
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
     *      language     : string
     *      source       : string 
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
     *      extendedAttributes      : extendedAttribute[]
     */
    function publishMedia ( $params )
    {
        /// syndication will always return metadata for one content item
        /// if publishing a collection, we get single collection item, which contains a list of any sub-items also generated
        try
        {
            $type_path = strtolower($params['mediaType']);
            // dirty pluralization
            if( !in_array($type_path,array('socialmedia','audio')) ) { $type_path .= 's'; }
            //$type_path{0} = strtolower($type_path{0});
            $result = $this->apiCall('post',"{$this->api['syndication_url']}/resources/media/$type_path",$params,'json','json');
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
     * Gets the source content of a single Media item
     * 
     * @param mixed $id Numeric Id of the MediaItem 
     * 
     * @access public 
     * @return SyndicationResponse ->results[]
     *      mixed : string or base64 encoded data
     */
    function getMediaContentById    ( $id )
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
     * Gets the content belonging to a given MediaItem for embedding. Supports Iframe or Javascript return format.
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
     *      string html
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
     *      imageFloat   : string
     *      imageMargin  : csv int
     * @param mixed $return_format Html or JSON
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      string html
     */
    function getMediaSyndicateById ( $id, $params=array(), $return_format='html' )
     {
        try
        {
            $format = ($return_format=='json') ? 'json' : 'html';
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/syndicate.{$format}",$params);
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
     *      imageFloat   : string
     *      imageMargin  : csv int
     *
     * @access public 
     * @return SyndicationResponse ->results[]
     *      string html
     */
    function getMediaHtmlById ( $id, $params=array() ) { return $this->getMediaSyndicateById($id,$params,'html'); }



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
            $result = $this->apiCall('get',"{$this->api['syndication_url']}/resources/media/{$id}/youtubeMetaData.json");
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

    /// CMS MANGER API FUNCTIONS

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

}
