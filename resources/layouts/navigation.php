<?php
	$navigation_linkClass = [
		// Active page
		"block py-2 px-3 text-white bg-gray-700 rounded md:bg-transparent md:text-red-700 md:p-0
		dark:text-white md:dark:text-white",
        // Inactive page
		"block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-red-800 md:p-0
		dark:text-gray-300 md:dark:hover:text-red-500 dark:hover:bg-gray-700 dark:hover:text-gray-900 md:dark:hover:bg-transparent",
	];
?>

<nav class="bg-gray-300 border-red-200 dark:bg-red-900">
	<div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
		<a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
			<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"><?= getLang('title') ?></span>
		</a>
		<button data-collapse-toggle="navbar" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar" aria-expanded="false">
			<span class="sr-only">Open main menu</span>
			<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
				<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
			</svg>
		</button>
		<div class="hidden w-full md:block md:w-auto" id="navbar">
			<ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-300 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 dark:bg-gray-800 md:dark:bg-red-900 dark:border-gray-700">
				<li>
					<a href="/home" <?= getPage() === 'home' || getPage() === '' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
						<?= getLang('navigation.home') ?>
					</a>
				</li>
				<li>
					<a href="/services" <?= getPage() === 'services' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
						<?= getLang('navigation.services') ?>
					</a>
				</li>
				<li>
					<a href="/contact" <?= getPage() === 'contact' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
						<?= getLang('navigation.contact') ?>
					</a>
				</li>

                <?php if ($isLoggedIn): ?>
					<li>
						<a href="/upload" <?= getPage() === 'upload' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
                            <?= getLang('navigation.upload') ?>
						</a>
					</li>
					<li>
						<a href="/account" <?= getPage() === 'account' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
                            <?= getLang('navigation.account') ?>
						</a>
					</li>
                <?php else: ?>
					<li>
						<a href="/login" <?= getPage() === 'login' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
							<?= getLang('navigation.login') ?>
						</a>
					</li>
					<li>
						<a href="/register" <?= getPage() === 'login' ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
							<?= getLang('navigation.register') ?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>
