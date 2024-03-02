<?php

use app\Models\Model;
use app\Models\UserModel;

/**
 * @return Model|null
 */
function auth_user (): ?Model
{
	if (isset($_SESSION['token']))
	{
		return get_user_with_token($_SESSION['token']);
	}
	else
	{
		return null;
	}
}

/**
 * @param string $token
 * @return Model|null
 */
function get_user_with_token (string $token): ?Model
{
	return UserModel::first('remember_token', $token);
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
{ return auth_user()->role === 1; }

session_start();

