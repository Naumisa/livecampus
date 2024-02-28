<?php

/**
 * @param string $language
 * @return array
 */
function lang_get_array (string $language): array
{
	$languagesPath = [
		'fr' => app_get_path('resources') . "lang/fr.php",
	];

	return require_once($languagesPath[$language]);
}

/**
 * @param string $key
 * @return string
 */
function lang_get (string $key): string
{
	global $languages;
	return array_key_exists($key, $languages) ? $languages[$key] : $key;
};
