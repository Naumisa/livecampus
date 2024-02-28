<?php

/**
 * @param string $message
 * @return void
 */
function logFile(string $message): void
{
	file_put_contents(getLogFile(), "[" . date('Y-m-d H:i:s') . "] " . $message . "\n", FILE_APPEND);
}
