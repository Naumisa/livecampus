<?php
  require __DIR__.'/../vendor/autoload.php';

	require_once ("../config/app.php");

	$root = __DIR__;
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title><?= lang_get('title'), ' - ', lang_get("navigation." . routes_get_route_name()) ?></title>
		<link rel="icon" type="image/png" href="<?= app_get_path('public_storage') ?>images/favicon.png" />
		<?php if (file_exists('./build/app.css')): ?>
			<link href="/build/app.css" rel="stylesheet" />
		<?php else: ?>
			<script src="https://cdn.tailwindcss.com"></script>
			<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
		<?php endif; ?>
	</head>

	<body class="antialiased bg-gray-50 dark:bg-gray-900">
		<header>
			<?php include_once (app_get_path('resources'). "layouts/navigation.php") ?>

			<?php if (isLoggedIn()): ?>
				<?php include_once (app_get_path('resources'). "layouts/sidebar.php"); ?>
			<?php endif; ?>
		</header>

		<main class="p-4 <?= isLoggedIn() ? 'md:ml-64' : 'md:mx-8' ?> h-auto pt-20">
			<h1 class="pb-4 px-4 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
				<?= lang_get(routes_get_route_name() . '.title') ?>
			</h1>
			<div class="p-4 border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600">
				<?php routes_get_view(); ?>
			</div>
		</main>

		<footer>
            <?php include_once (app_get_path('resources'). "layouts/footer.php") ?>
		</footer>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
		<script src="/build/app.js"></script>
	</body>
</html>
