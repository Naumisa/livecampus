<?php

use app\Models\Model;
use app\Models\UserModel;

/**
 * Récupère et retourne l'utilisateur actuellement authentifié en se basant sur le token de session.
 * Cette fonction vérifie si un token de session est défini et, dans ce cas, tente de récupérer l'utilisateur correspondant.
 *
 * @return Model|null Retourne une instance du modèle UserModel représentant l'utilisateur authentifié,
 *                     ou null si aucun utilisateur n'est authentifié ou si le token ne correspond à aucun utilisateur.
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
 * Recherche et retourne un utilisateur en se basant sur un token d'authentification.
 * Utilise le modèle UserModel pour rechercher un utilisateur ayant le token spécifié.
 *
 * @param string $token Le token d'authentification utilisé pour identifier l'utilisateur.
 * @return Model|null Retourne une instance du modèle UserModel si un utilisateur correspondant au token est trouvé,
 *                     sinon retourne null.
 */
function get_user_with_token (string $token): ?Model
{
	return UserModel::first('remember_token', $token);
}

/**
 * Vérifie si un utilisateur est actuellement connecté.
 * Une vérification est faite pour s'assurer que l'email et le token de session sont définis et non nuls,
 * ce qui indique qu'un utilisateur est considéré comme étant connecté.
 *
 * @return bool Retourne true si l'utilisateur est connecté, sinon false.
 */
function isLoggedIn (): bool
{
	if (isset($_SESSION['email']) && isset($_SESSION['token']) && $_SESSION['token'] != null)
	{
		$email = $_SESSION['email'];
		$token = $_SESSION['token'];

		$user = UserModel::first('email', $email);
		if ($user !== null)
		{
			return $user->remember_token === $token;
		}
	}

	unset($_SESSION['email']);
	unset($_SESSION['token']);

	return false;
}

/**
 * Vérifie si l'utilisateur actuellement authentifié est un administrateur.
 * Utilise la fonction auth_user() pour récupérer l'utilisateur authentifié et vérifie son rôle.
 *
 * @return bool Retourne true si l'utilisateur authentifié est un administrateur (rôle égal à 1), sinon false.
 *              Retourne également false si aucun utilisateur n'est actuellement authentifié.
 */
function isAdmin (): bool
{ return auth_user()->role === 1; }

session_start();

