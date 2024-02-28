<?php

/**
 * @return string
 */
function getResourcesPath (): string
{ return "../resources"; }

/**
 * @return string
 */
function getStoragePath (): string
{ return "./storage"; }

/**
 * @return string
 */
function getViewsPath (): string
{ return getResourcesPath() . "/views"; }

/**
 * @return string
 */
function getControllersPath (): string
{ return "../app/Controllers"; }

/**
 * @return string
 */
function getLogFile (): string
{ return "../storage/logs/latest.log"; }

/**
 * @return void
 */
function getEnvironment(): void
{
	$path = "../.env";
	if (!file_exists($path)) {
		logFile("The .env file doesn't exist : $path");
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

getEnvironment();

$configs = [
	'routes',
	'languages',
	'auth',
	'logging',
	'database',
];

foreach ($configs as $config)
{
	require_once ("../config/$config.php");
}

$languages = getLangKeys("fr");
