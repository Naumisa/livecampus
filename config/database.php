<?php

/**
 * Récupère l'hôte de la base de données depuis les variables d'environnement ou utilise
 * une valeur par défaut.
 * @return string L'hôte de la base de données.
 */
function db_get_host(): string
{
	return getenv('DB_HOST') ?? 'localhost';
}

/**
 * Récupère le nom de la base de données depuis les variables d'environnement ou utilise
 * une valeur par défaut.
 * @return string Le nom de la base de données.
 */
function db_get_database(): string
{
	return getenv('DB_NAME') ?? 'database';
}

/**
 * Récupère le nom d'utilisateur pour la connexion à la base de données depuis les variables
 * d'environnement ou utilise une valeur par défaut.
 * @return string Le nom d'utilisateur de la base de données.
 */
function db_get_username(): string
{
	return getenv('DB_USER') ?? 'root';
}

/**
 * Récupère le mot de passe pour la connexion à la base de données depuis les variables
 * d'environnement ou utilise une valeur par défaut.
 * @return string Le mot de passe de la base de données.
 */
function db_get_password(): string
{
	return getenv('DB_PASS') ?? 'password';
}

/**
 * Établit une connexion à la base de données et retourne l'objet PDO associé.
 * * @return PDO|null L'objet PDO en cas de succès, ou null en cas d'échec.
 */
function db_connect(): ?PDO
{
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	];

	try {
		$db = new PDO(
			'mysql:host=' . db_get_host() . ';dbname=' . db_get_database() . ';charset=utf8',
			db_get_username(),
			db_get_password(),
			$options,
		);
	}
	catch (Exception $e)
	{
		log_file("Erreur lors de la connexion à la database : " . $e->getMessage());
		return null;
	}

	return $db;
}

/**
 * Récupère toutes les entrées d'une table spécifique.
 * @param string $table Le nom de la table à interroger.
 * @return array|null Les données récupérées ou null en cas d'échec.
 */
function db_fetch_table(string $table): array|null
{
	$db = db_connect();

	if ($db == null)
	{
		return null;
	}

	$query = "SELECT * FROM $table";
	$result = $db->prepare($query);
	try
	{
		$result->execute();
	}
	catch (PDOException $e)
	{
		log_file("Erreur lors de la récupération de la table $table : " . $e->getMessage());
		return null;
	}
	return $result->fetchAll();
}

/**
 * Récupère les données spécifiques d'une table en fonction d'une colonne et de sa valeur.
 * @param string $table Le nom de la table à interroger.
 * @param string $column Le nom de la colonne pour la condition WHERE.
 * @param mixed $value La valeur à rechercher dans la colonne spécifiée.
 * @return array|null Les données récupérées ou null en cas d'échec.
 */
function db_fetch_data(string $table, string $column, mixed $value): ?array
{
	$db = db_connect();

	if ($db == null)
	{
		return null;
	}

	$query = "SELECT * FROM $table WHERE $column = :value";
	$result = $db->prepare($query);
	try {
		$result->execute([
			'value' => $value,
		]);
	}
	catch (PDOException $e)
	{
		log_file("Erreur lors de la récupération des données depuis $table où $column = $value : " . $e->getMessage());
		return null;
	}

	return $result->fetchAll();
}

/**
 * Crée une nouvelle table en base de données avec les colonnes spécifiées.
 * Peut également ajouter des contraintes de clé étrangère si spécifiées.
 * @param string $table Le nom de la table à créer.
 * @param array $data La définition des colonnes de la table.
 * @param array|null $foreignData Les définitions des contraintes de clé étrangère, si présentes.
 * @return bool True si la création réussit, False sinon.
 */
function db_create_table(string $table, array $data, array $foreignData = null): bool
{
	$db = db_connect();

	if ($db == null)
	{
		return false;
	}

	$columns = [];
	foreach ($data as $name => $definition)
	{
		$columns[] = "$name " . $definition['query'];
	}

	$query = "CREATE TABLE IF NOT EXISTS $table (" . implode(", ", $columns) . ");";

	try
	{
		$db->exec($query);
	}
	catch (PDOException $e)
	{
		log_file ("Erreur lors de la création de la table $table : " . $e->getMessage());
		return false;
	}

	if ($foreignData !== null)
	{
		foreach ($foreignData as $column => $definition)
		{
			$constraintQuery = "ALTER TABLE $table ADD CONSTRAINT fk_$column FOREIGN KEY ($column) REFERENCES " . $definition['refer_to'] . ";";
			try
			{
				$db->exec($constraintQuery);
			} catch (PDOException $e)
			{
				log_file("Erreur lors de l'ajout de la contrainte étrangère pour $column dans $table : " . $e->getMessage());
				return false;
			}
		}
	}

	return true;
}

/**
 * Insère de nouvelles données dans une table spécifique.
 * @param string $table Le nom de la table où insérer les données.
 * @param array $data Les données à insérer sous forme de tableau associatif colonne => valeur.
 * @return int ID du nouvel objet si l'insertion réussie, 0 sinon.
 */
function db_insert(string $table, array $data): int
{
	$db = db_connect();

	if ($db == null)
	{
		return 0;
	}

	$columns = implode(', ', array_keys($data));
	$placeholders = ':' . implode(', :', array_keys($data));

	$query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
	$result = $db->prepare($query);

	try
	{
		$result->execute($data);
	}
	catch (PDOException $e)
	{
		log_file("Erreur lors de l'insertion dans la table $table : " . $e->getMessage());
		return 0;
	}

	return $db->lastInsertId();
}

/**
 * Met à jour les données d'une entrée spécifique dans une table en fonction de son ID.
 * @param string $table Le nom de la table à mettre à jour.
 * @param int $id L'ID de l'entrée à mettre à jour.
 * @param array $data Les nouvelles données sous forme de tableau associatif colonne => valeur.
 * @return bool True si la mise à jour réussit, False sinon.
 */
function db_update(string $table, int $id, array $data): bool
{
	$db = db_connect();

	if ($db == null)
	{
		return false;
	}

	$sets = [];
	foreach ($data as $column => $value) {
		$sets[] = "$column = :$column";
	}
	$setString = implode(', ', $sets);

	$query = "UPDATE $table SET $setString WHERE id = :id";
	$result = $db->prepare($query);

	try {
		$result->execute($data);
	}
	catch (PDOException $e)
	{
		log_file("Erreur lors de la mise à jour de la table $table : " . $e->getMessage());
		return false;
	}

	return true;
}

/**
 * Supprime une entrée spécifique d'une table en fonction de son ID.
 * @param string $table Le nom de la table où supprimer l'entrée.
 * @param int $id L'ID de l'entrée à supprimer.
 * @return bool True si la suppression réussit, False sinon.
 */
function db_delete(string $table, int $id): bool
{
	$db = db_connect();

	if ($db == null)
	{
		return false;
	}

	$query = "DELETE FROM $table WHERE id = :id";
	$result = $db->prepare($query);

	try {
		$result->execute([
			'id' => $id,
		]);
	}
	catch (PDOException $e)
	{
		log_file("Erreur lors de la suppression dans la table $table : " . $e->getMessage());
		return false;
	}

	return true;
}

/**
 * Insère une nouvelle entrée dans la table spécifiée en respectant les contraintes de modèle.
 * @param string $modelTable Le nom de la table dans laquelle insérer les données.
 * @param array $modelFields Les champs du modèle avec leurs contraintes (type, unicité, etc.).
 * @param array $modelData Les données à insérer sous forme de tableau associatif (colonne → valeur).
 * @return int Retourne ID si l'insertion réussie, 0 en cas d'échec ou de non-respect des contraintes.
 */
function db_create_model(string $modelTable, array $modelFields, array $modelData): int
{
	foreach ($modelFields as $field => $details)
	{
		if (isset($details['unique']) && $details['unique'] && $field != 'id')
		{
			$existingData = db_fetch_data($modelTable, $field, $modelData[$field]);
			if (!empty($existingData))
			{
				log_file("Violation d'unicité pour $field avec la valeur " . $modelData[$field]);
				return 0;
			}
		}
	}

	$data = [];
	foreach ($modelFields as $field => $details)
	{
		if (!isset($modelData[$field]) && $details['required']) {
			log_file("Champ requis manquant : $field");
			return 0;
		}
		elseif (isset($modelData[$field]) && !db_verify_data($modelData[$field], $details['type']))
		{
			log_file("Type de donnée invalide pour $field. Attendu : " . $details['type']);
			return 0;
		}

		if (isset($modelData[$field]))
		{
			$data[$field] = $modelData[$field];
		}
	}

	return db_insert($modelTable, $data);
}

/**
 * Vérifie le type des données avant leur insertion ou mise à jour dans la base de données.
 * @param mixed $data La donnée à vérifier.
 * @param string $type Le type attendu de la donnée ('int' ou 'string').
 * @return bool True si la donnée correspond au type attendu, False sinon.
 */
function db_verify_data(mixed $data, string $type): bool
{
	switch ($type)
	{
		case 'int':
			return is_int($data) || ctype_digit($data);
		case 'string':
			return is_string($data);
		default:
			log_file("Type de donnée non compatible ou non implémenté : $type.");
			return false;
	}
}
