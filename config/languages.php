<?php

/**
 * @param string $language
 * @return array
 */
function getLangKeys (string $language): array
{
	$languagesPath = [
		'fr' => getResourcesPath() . "/lang/fr.php",
	];

	return require_once($languagesPath[$language]);
}

/**
 * @param string $key
 * @return string
 */
function getLang (string $key): string
{
	global $languages;
	return array_key_exists($key, $languages) ? $languages[$key] : $key;
};