<?php
	require('connect.php');

	if ($_SERVER['HTTP_X_GITHUB_EVENT'] && $_SERVER['HTTP_X_GITHUB_EVENT'] == 'release') {
		$payload = json_decode(file_get_contents('php://input'), true);

		if ($payload['release']['draft'] == true) {
			exit();
		}
		else {
			$release = $payload['release'];
			$asset = $payload['release']['assets'][0];

			$name = $release['name'];
			$prerelease = 0;

			if ($release['prerelease'] == true) {
				$prerelease = 1;
			}

			$pubDate = $release['published_at'];
			$homepage = $release['html_url'];
			$version = $release['tag_name'];


			$download = 'https://github.com/glyphish/gallery/releases/download/'.$version.'/'.$asset['name'];

			try {
				$query = "INSERT INTO releases(version,prerelease,zipball,homepage,name,published) VALUES ('".$version."','".$prerelease."','".$download."','".$homepage."','".$name."','".$pubDate."');";

				$result = pg_query($db, $query);

				// echo pg_last_error($db);

				echo json_encode(array('result'=>$result));
			}
			catch (Exception $exception) {
				throw new Exception('Unable to insert in database.', 0, $exception);
			}
		}
	}
?>
