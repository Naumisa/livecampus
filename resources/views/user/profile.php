<section id="profile">
	<form
		action="<?= routes_go_to_route('profile.edit') ?>"
		method="post"
		class="lg:flex lg:flex-row lg:justify-center"
	>
		<div class="flex flex-col items-center justify-center px-6 py-8 h-full space-y-4 md:space-y-6">
			<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
				<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
					<h2 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
						<?= lang_get('profile.title') ?> : <?= ucfirst($data['user']->username) ?>
					</h2>
					<div>
						<label
							for="username"
							class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
						>
							<?= lang_get('profile.username') ?>
						</label>
						<input
							type="text"
							name="username"
							id="username"
							class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
							value="<?= $data['user']->username ?>"
							disabled
						>
					</div>
					<div>
						<label
							for="email"
							class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
						>
							<?= lang_get('profile.email') ?>
						</label>
						<input
							type="email"
							name="email"
							id="email"
							class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
							value="<?= $data['user']->email ?>"
							disabled
						>
					</div>
					<div class="flex items-center justify-center space-x-2">
						<a
							href="#"
							onclick="enableUserEditForm()"
							id="edit"
							class="w-full bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:bg-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
						>
							<?= lang_get('profile.edit') ?>
						</a>
						<a
							href="#"
							onclick="disableUserEditForm()"
							id="cancel"
							class="w-full bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:bg-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
							hidden
						>
							<?= lang_get('profile.cancel') ?>
						</a>
						<button
							type="submit"
							id="submit"
							class="w-full bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:bg-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
							hidden
						>
							<?= lang_get('profile.send') ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="flex flex-col items-center justify-center px-6 py-8 h-full space-y-4 md:space-y-6">
		<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
			<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
				<h2 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
					<?= lang_get('profile.edit_password') ?>
				</h2>
				<div>
					<label
						for="password"
						class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
					>
						<?= lang_get('profile.password') ?>
					</label>
					<input
						type="password"
						name="password"
						id="password"
						placeholder="*******"
						class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
					>
				</div>
				<div>
					<label
						for="password_confirm"
						class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
					>
						<?= lang_get('profile.password_confirm') ?>
					</label>
					<input
						type="password"
						name="password_confirm"
						id="password_confirm"
						class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
						placeholder="*******"
					>
				</div>
				<div class="flex items-center justify-center">
					<button
						type="submit"
						id="submit"
						class="w-full bg-gray-50 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:text-white dark:bg-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
					>
						<?= lang_get('profile.send') ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	</form>
</section>
