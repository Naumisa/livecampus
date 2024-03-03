<section id="register">
	<div class="flex flex-col items-center px-6 py-8 mx-auto">
		<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
			<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
				<h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
					<?= lang_get('register.title') ?>
				</h1>
				<form class="space-y-4 md:space-y-6" action="<?= routes_go_to_route('register.confirmation') ?>" method="POST">
					<div>
						<label for="user_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
							<?= lang_get('register.email') ?>
						</label>
						<input type="email" name="user_email" id="user_email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required>
					</div>
					<div>
						<label for="user_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
							<?= lang_get('register.password') ?>
						</label>
						<input type="password" name="user_password" id="user_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
					</div>
					<div>
						<label for="user_password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
							<?= lang_get('register.password_confirmation') ?>
						</label>
						<input type="password" name="user_password_confirmation" id="user_password_confirmation" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
					</div>
					<button type="submit" class="w-full bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:bg-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
						<?= lang_get('register.title') ?>
					</button>

					<p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
						<?= lang_get('register.have_account') ?>
						<span class="text-blue-700">
							<a href="<?= routes_go_to_route('login') ?>"><?= lang_get('navigation.login') ?></a>
						</span>
					</p>
				</form>
			</div>
		</div>
	</div>
</section>
