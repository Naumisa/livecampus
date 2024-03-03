<?php

/**
 * Charge et retourne le tableau de traductions pour une langue spécifiée.
 *
 * @param string $language Le code de la langue (ex : 'fr').
 * @return array Le tableau des traductions.
 */
function lang_get_array (string $language): array
{
	$languagesPath = [
		'fr' => app_get_path('resources') . "lang/fr.php",
	];

	if (array_key_exists($language, $languagesPath))
	{
		return require($languagesPath[$language]);
	}

	return require($languagesPath['fr']);
}

/**
 * Récupère la traduction pour une clé spécifiée.
 *
 * @param string $key La clé de la traduction à récupérer.
 * @return string La traduction si trouvée, sinon retourne la clé.
 */
function lang_get (string $key): string
{
	$translations = lang_get_array(getenv('DEFAULT_LANG'));
	return $translations[$key] ?? $key;
};
