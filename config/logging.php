<?php

/**
 * @param string $message
 * @return void
 */
function log_file(string $message): void
{
	file_put_contents(app_get_path('logs') . "latest.log", "[" . date('Y-m-d H:i:s') . "] " . $message . "\n", FILE_APPEND);
}
