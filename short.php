<?php

require_once 'models/UrlShortener.php';

/**
 * Short controller
 *
 * Ajax handler, get long URL and return short URL
 */

$url = $_POST['url'];

try {
	$urlShortener = new \shortener\UrlShortener();
	$shortUrl = $urlShortener->getShortUrl($url);
	echo $shortUrl;
} catch (\shortener\UrlShortenerException $e) {
	die($e->getMessage());
} catch (\Exception $e) {
	// TODO: yes, i understand that user must cannot see internal error messaging,
	// but for this small task it useful for deployment and development
	// on production this must be redirect to long files
	die($e->getMessage());
}
