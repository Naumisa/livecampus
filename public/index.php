<?php
	require_once ("../config/app.php");
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title><?= lang_get('title'), ' - ', lang_get("navigation." . routes_get_route()) ?></title>
		<link rel="icon" type="image/png" href="<?= app_get_path('public_storage') ?>images/favicon.png" />
		<link rel="stylesheet" href="/build/app.css?<?= time() ?>">
	</head>
	<body>
		<header class="bg-red">
			<?php include_once (app_get_path('resources'). "layouts/navigation.php") ?>
		</header>

		<main>
			<?php routes_get_view(routes_get_controller()); ?>
		</main>

		<footer>
            <?php include_once (app_get_path('resources'). "layouts/footer.php") ?>
		</footer>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
		<script src="/build/app.js"></script>
	</body>
</html>
