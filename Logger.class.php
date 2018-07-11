<?php

include_once("Database.class.php");

class Logger
{

	public static function fnLog($sString)
	{
		$iIP = ip2long($_SERVER['REMOTE_ADDR']);

		$oQueryHandler = Database::fnHandler()->prepare("INSERT INTO Log (sEventName, iIP, dRecordTime) VALUES (:sString, :sIP, NOW())");
		$oQueryHandler->bindParam(':sString', $sString, PDO::PARAM_STR, 255);
		$oQueryHandler->bindParam(':sIP', $iIP, PDO::PARAM_STR, 15);
		//$oQueryHandler->bindParam(':sIP', $_SERVER['HTTP_X_FORWARDED_FOR'], PDO::PARAM_STR);
		$oQueryHandler->execute();
	}

}