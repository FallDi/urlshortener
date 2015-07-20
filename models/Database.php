<?php

namespace shortener;

require_once 'Config.php';

class DatabaseException extends \Exception {}
class DatabaseDuplicateException extends \Exception {}
class DatabaseRecordNotFoundException extends \Exception {}

class Database {

	const TABLE_NAME = 'urls';

	/** @var \PDO */
	private $dbh;

	/** @var Database */
	private static $instance = null;

	/** @var bool */
	private $useCache = false;

	/** @var string mysql|pgsql */
	private $db;

	private function __construct() {
		$this->connect();
		$this->useCache = Config::getInstance()->get('cache', 'enabled');
		if ($this->useCache) {
			require_once 'Cache.php';
		}
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function connect() {
		$this->db = Config::getInstance()->get('database', 'db');
		$host = Config::getInstance()->get('database', 'host');
		$database = Config::getInstance()->get('database', 'database');
		$user = Config::getInstance()->get('database', 'user');
		$password = Config::getInstance()->get('database', 'password');

		$dsn = "{$this->db}:host={$host};dbname={$database};user={$user};password={$password}";

		try {
			$this->dbh = new \PDO($dsn, $user, $password);
		} catch (\PDOException $e) {
			throw new DatabaseException('Connection failed: ' . $e->getMessage());
		}
	}
	
	public function getRecordByUid($uid) {
		if ($this->useCache) {
			// try go get value from cache
			$longUrl = Cache::getInstance()->get($uid);
			if ($longUrl)
				return $longUrl;
		}


		$sth = $this->dbh->prepare('SELECT longurl FROM ' . self::TABLE_NAME . ' WHERE uid = :uid');
		$sth->bindParam(':uid', $uid, \PDO::PARAM_INT);
		if (false === $sth->execute()) {
			$errorInfo = $sth->errorInfo();
			throw new DatabaseException($errorInfo[2]);
		}

		$result = $sth->fetch(\PDO::FETCH_ASSOC);
		if (false === $result) {
			throw new DatabaseRecordNotFoundException();
		}

		if ($this->useCache) {
			// put value into cache
			Cache::getInstance()->set($uid, $result['longurl']);
		}

		return $result['longurl'];
	}

	public function getRecordByLongUrl($longUrl) {
		$sth = $this->dbh->prepare('SELECT uid FROM ' . self::TABLE_NAME . ' WHERE longurl = :longUrl');
		$sth->bindParam(':longUrl', $longUrl, \PDO::PARAM_STR);
		if (false === $sth->execute()) {
			$errorInfo = $sth->errorInfo();
			throw new DatabaseException($errorInfo[2]);
		}

		$result = $sth->fetch(\PDO::FETCH_ASSOC);
		if (false === $result) {
			throw new DatabaseRecordNotFoundException();
		}
		return $result['uid'];
	}
	
	public function createRecord($longUrl) {
		$sth = $this->dbh->prepare('INSERT INTO ' . self::TABLE_NAME . ' (longUrl) VALUES (:longUrl)');
		$sth->bindParam(':longUrl', $longUrl, \PDO::PARAM_STR);
		if (false === $sth->execute()) {
			$errorInfo = $sth->errorInfo();
			if ($errorInfo[1] == 1062 && $this->db == 'mysql' || $errorInfo[1] == 7 && $this->db == 'pgsql') {
				// check duplicate record
				throw new DatabaseDuplicateException();
			}
			throw new DatabaseException($errorInfo[2]);
		}
		if ($this->db == 'pgsql') {
			$id= $this->dbh->lastInsertId('urls_uid_seq');
		}
		else {
			$id = $this->dbh->lastInsertId();
		}
		return $id;
	}
	
}