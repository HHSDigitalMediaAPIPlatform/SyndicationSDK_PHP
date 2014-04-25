<?php

/// combine classes into one single lib file for easy distribution

$classes_dir = realpath(dirname(__FILE__).'/../dist/classes');

$c = "";

$r  = trim(file_get_contents($classes_dir.'/SyndicationResponse.class.php'));
$r  = preg_replace( '/^\<\?php/', '', $r );
$r  = preg_replace( '/\?\>$/',    '', $r );
$c .= $r;

$a  = trim(file_get_contents($classes_dir.'/SyndicationApiClient.class.php'));
$a  = preg_replace( '/^\<\?php/', '', $a );
$a  = preg_replace( '/\?\>$/',    '', $a );
$a  = str_replace( "require_once('SyndicationResponse.class.php');", '', $a );
$c .= $a;

$s  = trim(file_get_contents($classes_dir.'/Syndication.class.php'));
$s  = preg_replace( '/^\<\?php/', '', $s );
$s  = preg_replace( '/\?\>$/',    '', $s );
$s  = str_replace( "require_once('SyndicationApiClient.class.php');", '', $s );
$c .= $s;

$c = "<?php\n$c\n\n?>";

file_put_contents( $classes_dir.'/../lib/Syndication.lib.php', $c );

?>
