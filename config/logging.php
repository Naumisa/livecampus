<?php

/**
 * @param string $message
 * @return void
 */
function logFile(string $message): void
{
	file_put_contents(getLogFile(), $message, FILE_APPEND);
}
