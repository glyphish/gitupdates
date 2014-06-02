<?php
	require('scripts/connect.php');
	date_default_timezone_set('America/New_York');

	header('Content-type: application/json;');

	if (array_key_exists('version', $_GET)) {
		function is_assoc($array) {
		  return (bool)count(array_filter(array_keys($array), 'is_string'));
		}

		$currentVersion = $_GET['version'];

		try {
			$query = 'SELECT * FROM releases WHERE prerelease=FALSE ORDER BY version DESC;';

			$result = pg_query($db, $query);
		}
		catch (Exception $exception) {
			throw new Exception('Unable to select rows.', 0, $exception);
		}

		$release = pg_fetch_assoc($result);

		if (!is_assoc($release)) {
			$release = $release[0];
		}

		if (str_replace('v', '', $release['version']) > $currentVersion) {
			$dateFormat = new DateTime($release['published']);

			$url = $release['zipball'];
			$name = $release['name'];
			$homepage = $release['homepage'];

			$output = array('url'=>$url, 'homepage'=>$homepage, 'name'=>$name, 'pub_date'=>$dateFormat->format('c'));

			echo str_replace('\/','/',json_encode($output));
		}
	}
?>
