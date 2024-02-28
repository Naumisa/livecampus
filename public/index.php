<?php
	require_once ("../config/app.php");
	$page = getPage() == '' ? 'home' : getPage();
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
			<?php include_once (getResourcesPath(). "/layouts/navigation.php") ?>
		</header>

		<main>
			<?php getView(getController()); ?>
		</main>

		<footer>
            <?php include_once (getResourcesPath(). "/layouts/footer.php") ?>
		</footer>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
		<script src="build/app.js"></script>
	</body>
</html>
