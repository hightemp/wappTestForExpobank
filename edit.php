<?php

include("Logger.class.php");
include("ClientsRecord.class.php");

Logger::fnLog("Страница edit.php");

$oClientsRecord = ClientsRecord::getInstance();
$aErrors = [];

if (isset($_REQUEST['iClientID'])) {
	$sTitle = 'Редактировать запись';

	$oClientsRecord->fnFind($_REQUEST['iClientID']);
} else {
	$sTitle = 'Создать запись';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$oClientsRecord->fnAssign();
	$aErrors = $oClientsRecord->fnValidate();
	if (empty($aErrors)) {
		$oClientsRecord->fnSave();

		Logger::fnLog("Добавлена запись в таблицу Clients");

		if (isset($_REQUEST['iClientID']))
			header("Location: edit.php?iClientID=".$_REQUEST['iClientID']);
		else
			header("Location: edit.php");
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
</head>
<body>
	<div class="container">
		<form method="POST">
			<div class="row" style="margin:10px 0px 10px 0px">
				<div class="col-md-8 order-md-1">
					<h3><?php echo $sTitle ?></h3>
				</div>
				<div class="col-md-2 order-md-2">
					<a href="index.php" class="btn btn-primary btn-lg btn-block">Назад</a>
				</div>
				<div class="col-md-2 order-md-3">
					<input type="submit" class="btn btn-primary btn-lg btn-block" name="save" value="Сохранить">
				</div>
			</div>

			<?php foreach (ClientsRecord::$aFormFields as $aField): ?>
			<div class="form-group">
				<label for="<?php echo $aField["name"] ?>"><?php echo $aField["label"] ?></label>
				<input 
					type="text" 
					class="form-control <?php if (isset($aErrors[$aField["name"]])): ?>is-invalid<?php endif ?>" 
					id="<?php echo $aField["name"] ?>" 
					name="<?php echo $aField["name"] ?>"
					value="<?php echo $oClientsRecord->{$aField["name"]} ?>"
				>
				<?php if (isset($aErrors[$aField["name"]])): ?>
				<div class="invalid-feedback">
        			<?php echo $aErrors[$aField["name"]] ?>
      			</div>
      			<?php endif ?>
			</div>
			<?php endforeach ?>	
		</form>	
	</div>
</body>
</html>