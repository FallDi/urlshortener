<?php

namespace shortener;

class ConfigException extends \Exception {}
class ConfigSectionNotFoundException extends ConfigException {}
class ConfigAttributeNotFoundException extends ConfigException {}

class Config {
	
	const CONFIG_FILE = 'config.ini';

	private static $instance = null;
	private static $config = [];
	
	private function __construct() {
	}

	/**
	 * @return Config
	 */
	public static function getInstance() {		
		if (!self::$instance) {
			self::$instance = new self();
			self::parse();
		}
		return self::$instance;
	}
	
	private static function parse() {
		$parseResult = @parse_ini_file(self::CONFIG_FILE, true);
		if (false === $parseResult) {
			throw new ConfigException('Cannot parse configuration file: ' . self::CONFIG_FILE);
		}
		self::$config = $parseResult;
	}
	
	public function get($section, $attribute) {
		if (!isset(self::$config[$section])) {
			throw new ConfigSectionNotFoundException("Section {$section} not found");
		}
		if (!isset(self::$config[$section][$attribute])) {
			throw new ConfigAttributeNotFoundException("Section {$section} doesn't contains attribute {$attribute}");
		}
		return self::$config[$section][$attribute];
	}
}