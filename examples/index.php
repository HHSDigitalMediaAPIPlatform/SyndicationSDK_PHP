<?php
///  include the library
require_once( '../src/Syndication.class.php' );

/// find our configuration parameters
$conf = require_once('./config.php');

/// instantiate a client using the config
$synd = new Syndication($conf);

/// make calls against the configured server
$response = $synd->getMediaTypes();

/// test success of call
if ( $response->success )
{
    /// display results
    echo "<h3>Media Types found at <i>{$conf['syndication_base']}</i></h3>\n"; 
    echo "<ul>\n"; 
    foreach ( $response->results as $mediaType )
    {
        echo "<li>{$mediaType['name']}</li>\n";
    }
    echo "</ul>\n"; 
} else {
    echo "<h3>No Media Types Found</h3>\n";
}

/// make calls against the configured server
$response = $synd->getMedia(array('descriptionContains'=>'health','max'=>2));
if ( $response->success )
{
    echo "<h3>Search : ". count($response->results) ." results found</h3>\n"; 
} else {
    echo "<h3>Search : Failed</h3>\n";
}

/// make calls against the configured server
$response = $synd->getMedia(array('descriptionContains'=>'DoesNotExistInDatabase','max'=>2));
/// the call can be successfull even if you didn't find what you wanted
if ( $response->success )
{
    echo "<h3>Search : ". count($response->results) ." results found</h3>\n"; 
} else {
    echo "<h3>Search : Failed</h3>\n";
}

?>

<hr />

<h4>This File:</h4>
<pre style="padding:4em; background-color:#D8D8D8;">
<?php echo htmlentities(file_get_contents('./index.php')); ?>
</pre>

<hr />

<h4>Config File:</h4>
<pre style="padding:4em; background-color:#D8D8D8;">
<?php echo htmlentities(file_get_contents('./config.php')); ?>
</pre>

