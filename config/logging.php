<?php

/**
 * @param string $message
 * @return void
 */
function logFile(string $message): void
{
	$logFile = "../storage/logs/latest.log";
	file_put_contents($logFile, $message, FILE_APPEND);
}
