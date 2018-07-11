<?php

include_once("Logger.class.php");
include_once("ClientsRecord.class.php");

Logger::fnLog("Страница index.php");

$oClientsRecord = ClientsRecord::getInstance();
$aResult = $oClientsRecord->fnFetchAll(true);

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-2 order-md-1">
				<form method="POST">
					<h3>Фильтр</h3>
					<?php foreach (ClientsRecord::$aFormFields as $aField): ?>
					<div class="form-group">
						<label for="<?php echo $aField["name"] ?>"><?php echo $aField["label"] ?></label>
						<input type="text" class="form-control" id="<?php echo $aField["name"] ?>" name="<?php echo $aField["name"] ?>" value="<?php echo @$_REQUEST[$aField["name"]] ?>">
					</div>
					<?php endforeach ?>
					<input type="submit" class="btn btn-primary btn-lg btn-block">
				</form>
			</div>
			<div class="col-md-10 order-md-2">
				<div class="row" style="margin:10px 0px 10px 0px">
					<div class="col-md-8 order-md-1">
					</div>
					<div class="col-md-4 order-md-2">
						<a href="edit.php" class="btn btn-primary btn-lg btn-block">Добавить запись</a>
					</div>
				</div>
				<table class="table">
				  <thead>
				    <tr>
				      <th scope="col">#</th>
				      <th scope="col">Имя</th>
				      <th scope="col">Фамилия</th>
				      <th scope="col">Отчество</th>
				      <th scope="col">Дата рождения</th>
				      <th scope="col">Пасспорт, серия</th>
				      <th scope="col">Пасспорт, номер</th>
				      <th scope="col">Пасспорт, дата</th>
				      <th scope="col"></th>
				    </tr>
				  </thead>
				  <tbody>
				  <?php foreach ($aResult as $aItem): ?>
					<tr>
						<th scope="row"><?php echo $aItem['iClientID']?></th>
						<td><?php echo $aItem['sFirstName']?></td>
						<td><?php echo $aItem['sLastName']?></td>
						<td><?php echo $aItem['sFatherName']?></td>
						<td style="white-space: nowrap"><?php echo $aItem['dBirthDate']?></td>
						<td><?php echo $aItem['iPassportSerial']?></td>
						<td><?php echo $aItem['iPassportNumber']?></td>
						<td style="white-space: nowrap"><?php echo $aItem['dPassportDate']?></td>
						<td><a href="edit.php?iClientID=<?php echo $aItem['iClientID']?>" class="btn">✎</a></td>
					</tr>
				<?php endforeach ?>
				</tbody>	  			
			</div>
		</div>
	</div>
</body>
</html>