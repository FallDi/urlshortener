<?php

namespace shortener;

class CacheException extends \Exception {}

require_once 'Config.php';

/**
 * Simple cache on Redis strings, with ttl
 */
class Cache {

	/** @var \Redis */
	private $connection;

	/** @var Cache */
	private static $instance = null;

	/** @var integer */
	private $ttl;

	private function __construct() {
		$hostPort = Config::getInstance()->get('cache', 'hostport');
		$this->ttl = Config::getInstance()->get('cache', 'ttl');
		$this->connect($hostPort);
	}


	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	private function connect($hostPort) {
		$this->connection = new \Redis();
		if (!$this->connection->connect($hostPort)) {
			throw new CacheException("Cannot connect to cache: " . $hostPort);
		}
	}

	public function get($key) {
		return $this->connection->get($key);
	}

	public function set($key, $value) {
		return $this->connection->setex($key, $this->ttl, $value);
	}
}