<div>
	<footer class="<?= isLoggedIn() ? 'md:ml-64' : 'md:mx-8' ?> bg-white rounded-lg shadow m-4 dark:bg-gray-800">
		<div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
			<span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 <?= lang_get('title') ?>™. All Rights Reserved.
			</span>
			<ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
				<?php if (isLoggedIn()): ?>
					<?php foreach($loggedLinks as $link => $value): ?>
					<li>
						<a href="<?= routes_go_to_route($link) ?>" class="hover:underline me-4 md:me-6">
							<?= lang_get($value) ?>
						</a>
					</li>
					<?php endforeach; ?>
				<?php else: ?>
					<?php foreach($loggedOutLinks as $link => $value): ?>
						<li>
							<a href="<?= routes_go_to_route($link) ?>" class="hover:underline me-4 md:me-6">
								<?= lang_get($value) ?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</footer>

</div>
