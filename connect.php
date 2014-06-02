<?php
	require('secret.php');

	try {
		$db = pg_connect($dbCredentials);
	}
	catch (Exception $exception) {
		throw new Exception('Unable to connect to database.', 0, $exception);
	}
?>
