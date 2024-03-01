<?php
	$navigation_linkClass = [
		// Active page
		"block px-3 text-white  rounded md:bg-transparent md:text-red-700 md:p-0
		dark:text-white md:dark:text-white",
        // Inactive page
		"block px-3 text-gray-900 rounded md:hover:bg-transparent md:hover:text-red-800 md:p-0
		dark:text-gray-300 md:dark:hover:text-red-500 dark:hover:text-gray-900",
	];
?>

<nav class="bg-red-600 border-b border-red-800 px-4 py-2.5 dark:bg-red-800 dark:border-red-400 fixed left-0 right-0 top-0 z-50">
	<?php if (isLoggedIn()): ?>
	<div class="flex flex-wrap justify-between items-center">
		<div class="flex justify-start items-center">
			<button data-drawer-target="drawer-navigation"
					data-drawer-toggle="drawer-navigation"
					aria-controls="drawer-navigation"
					class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
				<svg aria-hidden="true"
					 class="w-6 h-6"
					 fill="currentColor"
					 viewBox="0 0 20 20"
					 xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd"
						  d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
						  clip-rule="evenodd"></path>
				</svg>
				<svg aria-hidden="true"
					 class="hidden w-6 h-6"
					 fill="currentColor"
					 viewBox="0 0 20 20"
					 xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd"
						  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
						  clip-rule="evenodd"></path>
				</svg>
				<span class="sr-only">Toggle sidebar</span>
			</button>
			<a href="<?= routes_go_to_route('dashboard') ?>" class="flex items-center justify-between mr-4">
				<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"><?= lang_get('title') ?></span>
			</a>
		</div>
		<div class="flex items-center lg:order-2">
			<button type="button"
					data-drawer-toggle="drawer-navigation"
					aria-controls="drawer-navigation"
					class="p-2 mr-1 text-gray-500 rounded-lg md:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
				<span class="sr-only">Toggle search</span>
				<svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
					<path clip-rule="evenodd" fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"></path>
				</svg>
			</button>
			<!-- Notifications -->
			<button type="button"
					data-dropdown-toggle="notification-dropdown"
					class="hidden p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
				<span class="sr-only">View notifications</span>
				<!-- Bell icon -->
				<svg aria-hidden="true"
					 class="w-6 h-6"
					 fill="currentColor"
					 viewBox="0 0 20 20"
					 xmlns="http://www.w3.org/2000/svg">
					<path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
				</svg>
			</button>
			<!-- Dropdown menu -->
			<div class="hidden overflow-hidden z-50 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-gray-100 shadow-lg dark:divide-gray-600 dark:bg-gray-700"
				 id="notification-dropdown">
				<div class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
					Notifications
				</div>
				<div>
					<a href="#"
					   class="flex py-3 px-4 border-b hover:bg-gray-100 dark:hover:bg-gray-600 dark:border-gray-600">
						<div class="flex-shrink-0">
							<img class="w-11 h-11 rounded-full"
								 src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
								 alt="Bonnie Green avatar"
							/>
							<div class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white bg-primary-700 dark:border-gray-700">
								<svg aria-hidden="true"
									 class="w-3 h-3 text-white"
									 fill="currentColor"
									 viewBox="0 0 20 20"
									 xmlns="http://www.w3.org/2000/svg">
									<path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"></path>
									<path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
								</svg>
							</div>
						</div>
						<div class="pl-3 w-full">
							<div class="text-gray-500 font-normal text-sm mb-1.5 dark:text-gray-400">
								New message from
								<span class="font-semibold text-gray-900 dark:text-white">Bonnie Green</span>:
								"Hey, what's up? All set for the presentation?"
							</div>
							<div class="text-xs font-medium text-primary-600 dark:text-primary-500">
								a few moments ago
							</div>
						</div>
					</a>
				</div>
				<a href="#"
				   class="block py-2 text-md font-medium text-center text-gray-900 bg-gray-50 hover:bg-gray-100 dark:bg-gray-600 dark:text-white dark:hover:underline">
					<div class="inline-flex items-center">
						<svg aria-hidden="true"
							 class="mr-2 w-4 h-4 text-gray-500 dark:text-gray-400"
							 fill="currentColor"
							 viewBox="0 0 20 20"
							 xmlns="http://www.w3.org/2000/svg">
							<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
							<path fill-rule="evenodd"
								  d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
								  clip-rule="evenodd"></path>
						</svg>
						View all
					</div>
				</a>
			</div>
			<button type="button"
					class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
					id="user-menu-button"
					aria-expanded="false"
					data-dropdown-toggle="dropdown">
				<span class="sr-only">Open user menu</span>
				<img
						class="w-8 h-8 rounded-full"
						src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/michael-gough.png"
						alt="user photo"
				/>
			</button>
			<!-- Dropdown menu -->
			<div class="hidden z-50 my-4 w-56 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600"
				 id="dropdown">
				<div class="py-3 px-4">
					<span class="block text-sm font-semibold text-gray-900 dark:text-white"><?= auth_user()->username ?></span>
					<span class="block text-sm text-gray-900 truncate dark:text-white"><?= auth_user()->email ?></span>
				</div>
				<ul class="py-1 text-gray-700 dark:text-gray-300"
					aria-labelledby="dropdown">
					<li>
						<a href="<?= routes_go_to_route('profile') ?>"
						   class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
							<?= lang_get('navigation.profile') ?>
						</a>
					</li>
				</ul>
				<ul class="py-1 text-gray-700 dark:text-gray-300"
					aria-labelledby="dropdown">
					<li>
						<a href="<?= routes_go_to_route('logout') ?>"
						   class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
							<?= lang_get('navigation.logout') ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="max-w-screen-xl flex flex-wrap justify-between items-center mx-auto">
		<a href="<?= routes_go_to_route('home') ?>" class="flex items-center justify-between mr-4">
			<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"><?= lang_get('title') ?></span>
		</a>
		<button data-collapse-toggle="navbar" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar" aria-expanded="false">
			<span class="sr-only">Open main menu</span>
			<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
				<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
			</svg>
		</button>
		<div class="hidden w-full md:block md:w-auto" id="navbar">
			<ul class="font-medium flex flex-col p-4 md:p-0 mt-4 md:mt-0 rounded-l md:flex-row md:space-x-8 rtl:space-x-reverse">
				<?php foreach($loggedOutLinks as $link => $value): ?>
					<li>
						<a href="<?= routes_go_to_route($link) ?>" <?= routes_get_route_name() === $link ? "class='$navigation_linkClass[0]' aria-current=\"page\"" : "class='$navigation_linkClass[1]'" ?>>
							<?= lang_get($value) ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>
</nav>
