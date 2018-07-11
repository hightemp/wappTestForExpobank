<?php

class Database 
{
	private static $_oInstance;
	private $_oDBHandler;

	public static function getInstance()
	{
		if (!self::$_oInstance)
			self::$_oInstance = new self();
		return self::$_oInstance;
	}

	public function __construct() {
		$this->_oDBHandler = new PDO(
			'mysql:host=localhost;dbname=Application', 
			$_ENV['DB_USER'], 
			$_ENV['DB_PASSWORD']
		);		
	}

	public static function fnHandler()
	{
		return self::getInstance()->_oDBHandler;
	}
}