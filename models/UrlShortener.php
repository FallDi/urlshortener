<?php

namespace shortener;

require_once 'Database.php';

class UrlShortenerException extends \Exception {}
class UrlShortenerNotFoundException extends \Exception {}

class UrlShortener {
	
	const MAX_URL_LENGTH = 256;
	const ALPHABET = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";

	/** @var \shortener\Database */
	private $database;

	/** @var string */
	private $host;

	public function __construct() {
		try {
			$this->database = Database::getInstance();
		} catch (DatabaseException $e) {
			throw new UrlShortenerException($e->getMessage());
		}

		$this->host = "http://" . $_SERVER['HTTP_HOST'];
	}

	public function getShortUrl($longUrl) {
		if (!$this->isLongUrlValid($longUrl)) {
			throw new UrlShortenerException("URL is invalid");
		}

		try {
			$id = $this->database->createRecord($longUrl);
		} catch (DatabaseDuplicateException $e) {
			$id = $this->database->getRecordByLongUrl($longUrl);
		}

		$encodedId = $this->baseEncode($id, self::ALPHABET);

		return $this->host . '/' . $encodedId;
	}

	public function getLongUrl($shortUrl) {
		if (!$this->isShortUrlValid($shortUrl)) {
			throw new UrlShortenerException("Short URL is invalid");
		}

		$id = $this->baseDecode($shortUrl, self::ALPHABET);

		try {
			$longUrl = $this->database->getRecordByUid($id);
		} catch (DatabaseRecordNotFoundException $e) {
			throw new UrlShortenerNotFoundException("The short URL not found");
		}

		return $longUrl;
	}
	
	private function baseEncode($num, $alphabet) {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}

		if ($num)
			$encoded = $alphabet[$num] . $encoded;

		return $encoded;
	}

	private function baseDecode($num, $alphabet) {
		$decoded = 0;
		$multi = 1;
		while (strlen($num) > 0) {
			$digit = $num[strlen($num)-1];
			$decoded += $multi * strpos($alphabet, $digit);
			$multi = $multi * strlen($alphabet);
			$num = substr($num, 0, -1);
		}

		return $decoded;
	}

	private function isShortUrlValid($shortUrl) {
		return is_string($shortUrl) && 1 === preg_match('/^[0-9a-zA-Z]+$/', $shortUrl);
	}

	private function isLongUrlValid($longUrl) {
		return is_string($longUrl) && strlen($longUrl) <= self::MAX_URL_LENGTH;
	}

}