<?php
	require_once ("../config/app.php");
	global $resourcesPath, $page, $controller;
	if ($page === '') $page = 'home';
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title><?= getLang('title'), ' - ', getLang("navigation.$page") ?></title>
		<link rel="icon" type="image/png" href="./storage/images/favicon.png" />
		<link rel="stylesheet" href="build/app.css">
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

		<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
		<script src="build/app.js"></script>
	</body>
</html>
