<aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-12 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
	   aria-label="Sidenav"
	   id="drawer-navigation">
	<div class="overflow-y-auto py-6 px-3 h-full bg-white dark:bg-gray-800">
		<ul class="space-y-2">
			<?php if (isLoggedIn()): ?>
				<?php foreach($loggedLinks as $link => $value): ?>
					<li>
						<a href="<?= routes_go_to_route($link) ?>"
						   class="p-2 text-base font-medium text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
							<span><?= lang_get($value) ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		<ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
			<?php if (isAdmin()): ?>
				<?php foreach($adminLinks as $link => $value): ?>
					<li>
						<a href="<?= routes_go_to_route($link) ?>"
						   class="p-2 text-base font-medium text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
							<span><?= lang_get($value) ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>
</aside>
