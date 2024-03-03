<?php

/**
 * Enregistre un message dans le fichier de log.
 * Effectue une rotation des logs lorsque la taille du fichier a atteint 1MB.
 * @param string $message Le message à loguer.
 * @return void
 */
function log_file (string $message): void
{
	$logPath = app_get_path('logs') . "latest.log";
	$maxSize = 1024 * 1024;

	if (file_exists($logPath) && filesize($logPath) > $maxSize)
	{
		$archivedLogPath = app_get_path('logs') . "log_" . date('Y-m-d_H-i-s') . ".log";
		rename($logPath, $archivedLogPath);
	}

	$logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";

	try
	{
		file_put_contents($logPath, $logMessage, FILE_APPEND);
	}
	catch (Exception $e)
	{
		error_log("Impossible d'écrire dans le fichier de log : " . $e->getMessage());
	}
}

/**
 * Enregistre un message d'erreur pour l'affichage ultérieur.
 * @param string $message Le message d'erreur à enregistrer.
 * @return void
 */
function log_session (string $message): void
{
	if (!isset($_SESSION['errors']))
	{
		$_SESSION['errors'] = [];
	}
	$_SESSION['errors'] = [$message];
}

/**
 * Vérifie si des erreurs ont été enregistrées.
 * @return bool Retourne true s'il y a des erreurs, false sinon.
 */
function has_error (): bool
{
	return !empty($_SESSION['errors']);
}

/**
 * Récupère les messages d'erreur enregistrés.
 * @return array Retourne un tableau des messages d'erreur.
 */
function get_errors (): array
{
	$errors = $_SESSION['errors'] ?? [];
	unset($_SESSION['errors']);
	return $errors;
}

