<?php

include_once("Logger.class.php");
include_once("Database.class.php");

class ClientsRecord 
{
	public static $aFormFields = [
		[
			"label" => "Имя", 
			"name" => "sFirstName",
			"validator" => [
				"required",
			],
			"pdo_param" => PDO::PARAM_STR,
			"pdo_length" => 255,
		],
		[
			"label" => "Фамилия", 
			"name" => "sLastName",
			"validator" => [
				"required",
			],
			"pdo_param" => PDO::PARAM_STR,
			"pdo_length" => 255,
		],
		[
			"label" => "Отчество", 
			"name" => "sFatherName",
			"validator" => [
				"required",
			],
			"pdo_param" => PDO::PARAM_STR,
			"pdo_length" => 255,
		],
		[	
			"label" => "Дата рождения", 
			"name" => "dBirthDate",
			"validator" => [
				"required",
				"date",
			],
			"pdo_param" => PDO::PARAM_STR,
			"pdo_length" => 10,
		],
		[
			"label" => "Пасспорт, серия", 
			"name" => "iPassportSerial",
			"validator" => [
				"required",
				"passport_serial",
			],
			"pdo_param" => PDO::PARAM_INT,
		],
		[
			"label" => "Пасспорт, номер", 
			"name" => "iPassportNumber",
			"validator" => [
				"required",
				"passport_number",
			],
			"pdo_param" => PDO::PARAM_INT,
		],
		[
			"label" => "Пасспорт, дата выдачи", 
			"name" => "dPassportDate",
			"validator" => [
				"required",
				"date",
			],
			"pdo_param" => PDO::PARAM_STR,
			"pdo_length" => 10,
		],
	];

	private static $_oInstance;
	private $_oQueryHandler;
	private $_aBoundFields;
	private $_aRecord;

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

	public function __get($sName)
	{
		return $this->_aRecord[$sName];
	}

	public function fnPrepareQuery() 
	{
		function fnFilterEmpty($oValue) {
			return !empty($oValue);
		}

		$aFilteredREQUEST = array_filter($_REQUEST, 'fnFilterEmpty');
		$this->_aBoundFields = [];
		$aFilteredREQUESTKeys = array_keys($aFilteredREQUEST);
		$sCondition = "WHERE 1 ";

		foreach ($aFilteredREQUESTKeys as $sName) {
			$sCondition .= "AND $sName = :$sName ";
			$this->_aBoundFields[] = $sName;
		}

		$this->_oQueryHandler = Database::fnHandler()->prepare("SELECT * FROM Clients $sCondition ORDER BY iClientID ASC");
	}

	public function fnFilter() 
	{
		
	}

	public function fnValidate() 
	{
		$aErrors = [];

		foreach (self::$aFormFields as $aField) {
			foreach ($aField["validator"] as $sValidatorName) {
				switch ($sValidatorName) {
					case "required":
						if ($_REQUEST[$aField["name"]] == "")
							$aErrors[$aField["name"]] = "Поле не должно быть пустым";
						break;
					case "passport_serial":
						if (!preg_match("/\d\d\d\d/", $_REQUEST[$aField["name"]]))
							$aErrors[$aField["name"]] = "Поле должно содержать серию формата 0000";
						break;
					case "passport_number":
						if (!preg_match("/\d\d\d\d\d\d/", $_REQUEST[$aField["name"]]))
							$aErrors[$aField["name"]] = "Поле должно содержать номер формата 000000";
						break;
					case "date":
						if (!preg_match("/(\d\d\d\d)-(\d\d)-(\d\d)/", $_REQUEST[$aField["name"]], $aMatches))
							$aErrors[$aField["name"]] = "Поле должно содержать дату формата 0000-00-00";
						if (isset($aMatches[2]) && ($aMatches[2]>12 || $aMatches[2]<1))
							$aErrors[$aField["name"]] = "Месяц должен быть в диапозоне от 1 до 12";
						if (isset($aMatches[3]) && ($aMatches[3]>31 || $aMatches[3]<1))
							$aErrors[$aField["name"]] = "Число должено быть в диапозоне от 1 до 31";
						break;
				}

			}
		}

		return $aErrors;
	}

	public function fnAssign() 
	{
		$this->_aRecord = $_REQUEST;
	}

	public function fnEexecute()
	{
		foreach (self::$aFormFields as $aField) {
			if (!in_array($aField['name'], $this->_aBoundFields))
				continue;
			switch ($aField['pdo_param']) {
				case PDO::PARAM_STR:
					$this->_oQueryHandler->bindParam(":".$aField['name'], $_REQUEST[$aField["name"]], $aField['pdo_param'], $aField['pdo_length']);
					break;
				case PDO::PARAM_INT:
					$this->_oQueryHandler->bindParam(":".$aField['name'], $_REQUEST[$aField["name"]], $aField['pdo_param']);
					break;
			}
		}
		$this->_oQueryHandler->execute();
		//$this->_oQueryHandler->execute($this->_aQueryData);
	}

	public function fnFetchAll($bWithPrepare = false)
	{
		if ($bWithPrepare) {
			$this->fnPrepareQuery();
			$this->fnEexecute();
		}
		return $this->_oQueryHandler->fetchAll();				
	}

	public function fnFind($iID)
	{
		$iID = intval($iID);
		$oQueryHandler =  Database::fnHandler()->query("SELECT * FROM Clients WHERE iClientID = $iID LIMIT 1");
		$this->_aRecord = $oQueryHandler->fetch(PDO::FETCH_ASSOC);
	}
	
	public function fnSave()
	{
		$sQuery = [];
		foreach (self::$aFormFields as $aField) {
			if (!isset($_REQUEST[$aField["name"]]))
				continue;
			$sQuery[] = "{$aField["name"]} = '{$_REQUEST[$aField["name"]]}'";
		}
		$sQuery = implode(",", $sQuery);

		if (isset($this->_aRecord['iClientID'])) {
			$iID = intval($this->_aRecord['iClientID']);
			$oQueryHandler = Database::fnHandler()->prepare("UPDATE Clients SET $sQuery WHERE iClientID = $iID");
			echo "UPDATE Clients VALUES $sQuery WHERE iClientID = $iID";
		} else {
			$oQueryHandler = Database::fnHandler()->prepare("INSERT INTO Clients SET $sQuery");
			echo "INSERT INTO Clients VALUES $sQuery";
		}
		$oQueryHandler->execute();
	}
}