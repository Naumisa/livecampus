<?php
	require_once ("../config/app.php");
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title><?= $lang['web_title'], ' - ', $lang[$page] ?></title>
		<link rel="icon" type="image/png" href="images/favicon.png" />
		<link rel="stylesheet" href="css/build.css">
	</head>
	<body>
		<header class="bg-red">
			<?php include_once ("$resourcesPath/layouts/navigation.php") ?>
		</header>

		<main>
			<?php
				if ($controller != null)
                {
					require_once ($controller);
				}
				include_once ($vue);
			?>
		</main>

		<footer>
            <?php include_once ("$resourcesPath/layouts/footer.php") ?>
		</footer>
	</body>
</html>
