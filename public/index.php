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
				<?php
				if (has_error())
				{
					foreach (get_errors() as $error): ?>
						<div id="alert-2" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-red-800 dark:text-red-400" role="alert">
							<svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
								<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
							</svg>
							<span class="sr-only">Info</span>
							<div class="ms-3 text-sm font-medium">
								<?= $error ?>
							</div>
							<button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-400 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:text-red-400" data-dismiss-target="#alert-2" aria-label="Close">
								<span class="sr-only">Close</span>
								<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
								</svg>
							</button>
						</div>
					<?php endforeach;
				}
				?>
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
