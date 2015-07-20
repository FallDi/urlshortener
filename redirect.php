<?php

require_once 'models/UrlShortener.php';

/**
 * Redirect controller
 */

// remove slash
$url = ltrim($_SERVER['REQUEST_URI'], '/');

try {
	$urlShortener = new \shortener\UrlShortener();
	$longUrl = $urlShortener->getLongUrl($url);
	header('Location: ' . $longUrl);
} catch (\shortener\UrlShortenerNotFoundException $e) {
	die($e->getMessage());
} catch (\Exception $e) {
	// TODO: yes, i understand that user must cannot see internal error messaging,
	// but for this small task it useful for deployment and development
	// on production this must be redirect to long files
	die($e->getMessage());
}