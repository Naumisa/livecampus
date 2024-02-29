<?php

/**
 * @param string $key
 * @return string
 */
function app_get_path (string $key): string
{
	$paths = [
		'resources' 		=> '../resources/',
		'views' 			=> '../resources/views/',
		'controllers' 		=> '../app/Controllers/',
		'models' 			=> '../app/Models/',
		'public_storage' 	=> '/storage/',
		'logs' 				=> '../storage/logs/',
	];

	return $paths[$key];
}

/**
 * @return void
 */
function app_get_environment(): void
{
	$path = "../.env";
	if (!file_exists($path)) {
		log_file("The .env file doesn't exist : $path");
	}

	$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		// Ignorer les commentaires
		if (str_starts_with(trim($line), '#')) {
			continue;
		}

		list($name, $value) = explode('=', $line, 2);
		$name = trim($name);
		$value = trim($value);

		if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
			putenv(sprintf('%s=%s', $name, $value));
			$_ENV[$name] = $value;
			$_SERVER[$name] = $value;
		}
	}
}

app_get_environment();

$configs = [
	'database',
	'auth',
	'routes',
	'languages',
	'logging',
];

foreach ($configs as $config)
{
	require_once ("../config/$config.php");
}

$languages = lang_get_array(getenv('DEFAULT_LANG') ?? 'fr');

// TODO: Use this for Authentification
include_once (app_get_path('models') . "UserModel.php");

user_migrate();
if (count(user_get_data_with_id(1)) == 0)
{
	user_create(user_get_data_array("admin", "admin@email.com", "password", 1));
}
// END-TODO

$loggedOutLinks = [
	'home' => 'navigation.home',
	'services' => 'navigation.services',
	'contact' => 'navigation.contact',
	'login' => 'navigation.login',
	'register' => 'navigation.register',
];

$loggedLinks = [
	'dashboard' => 'navigation.dashboard',
	'file' => 'navigation.file',
];

$adminLinks = [
	'users' => 'navigation.users',
];
