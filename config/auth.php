<?php

/**
 * @return array|null
 */
function auth_user (): ?array
{
	if (isset($_SESSION['token']))
	{
		return user_get_data_with_token($_SESSION['token'])[0];
	}
	else
	{
		return null;
	}
}

/**
 * @param string $token
 * @return array
 */
function user_get_data_with_token (string $token): array
{
	return db_fetch_data('users', 'remember_token', $token);
}

/**
 * @return bool
 */
function isLoggedIn (): bool
{ return isset($_SESSION['email']) && isset($_SESSION['token']) && $_SESSION['token'] != null; }

/**
 * @return bool
 */
function isAdmin (): bool
{ return auth_user()['role'] === 1; }

session_start();

