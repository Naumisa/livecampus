<nav class="bg-red-700 mx-auto p-6 lg:px-8">
	<!-- Navigation : large screen -->
	<div class="hidden lg:flex items-center justify-between">
		<a href="index.php" class="text-xl leading-6 font-bold text-gray-50"><?= $lang['title'] ?></a>

		<div class="flex lg:gap-x-12">
			<a href="index.php?page=home" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['home'] ?></a>
			<a href="index.php?page=menu" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['menu'] ?></a>
			<a href="index.php?page=services" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['services'] ?></a>
			<a href="index.php?page=contact" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['contact'] ?></a>
			<a href="index.php?page=location" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['location'] ?></a>

			<a href="index.php?page=login" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['login'] ?> <span aria-hidden="true">&rarr;</span></a>
		</div>
	</div>

	<!-- Navigation : mobile screen -->
	<div class="flex flex-col lg:hidden items-center">
		<a href="index.php" class="text-xl leading-6 font-bold text-gray-50 pb-4"><?= $lang['title'] ?></a>

		<div class="flex flex-wrap gap-x-6 gap-y-4 justify-center">
			<a href="index.php?page=home" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['home'] ?></a>
			<a href="index.php?page=menu" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['menu'] ?></a>
			<a href="index.php?page=services" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['services'] ?></a>
			<a href="index.php?page=contact" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['contact'] ?></a>
			<a href="index.php?page=location" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['location'] ?></a>

			<a href="index.php?page=login" class="text-sm font-semibold leading-6 text-gray-50"><?= $lang['login'] ?> <span aria-hidden="true">&rarr;</span></a>
		</div>
	</div>
</nav>
