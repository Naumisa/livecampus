<?php

/**
 * @return string
 */
function db_get_host(): string
{
	return getenv('DB_HOST') ?? 'localhost';
}

/**
 * @return string
 */
function db_get_database(): string
{
	return getenv('DB_NAME') ?? 'database';
}

/**
 * @return string
 */
function db_get_username(): string
{
	return getenv('DB_USER') ?? 'root';
}

/**
 * @return string
 */
function db_get_password(): string
{
	return getenv('DB_PASS') ?? 'password';
}

/**
 * @return PDO
 */
function db_connect(): PDO
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
		logFile("Erreur lors de la connexion à la database : " . $e->getMessage());
		die();
	}

	return $db;
}

/**
 * @param string $table
 * @return array
 */
function db_fetch_table(string $table): array
{
	$db = db_connect();
	$query = 'SELECT * FROM ' . $table;
	$result = $db->prepare($query);
	try {
		$result->execute();
	}
	catch (PDOException $e)
	{
		logFile("Erreur lors de la récupération de la table : " . $e->getMessage());
		die();
	}
	return $result->fetchAll();
}

/**
 * @param string $table
 * @param string $column
 * @param $value
 * @return array
 */
function db_fetch_data(string $table, string $column, $value): array
{
	$db = db_connect();
	$query = 'SELECT * FROM ' . $table . ' WHERE ' . $column . ' = :value';
	$result = $db->prepare($query);
	try {
		$result->execute([
			'value' => $value,
		]);
	}
	catch (PDOException $e)
	{
		logFile("Erreur lors de la récupération des données : " . $e->getMessage());
		die();
	}
	return $result->fetchAll();
}

/**
 * @param string $table
 * @param array $data
 * @return void
 */
function db_create_table(string $table, array $data): void
{
	$db = db_connect();
	$query = "CREATE TABLE IF NOT EXISTS $table (";

	$columns = [];
	foreach ($data as $name => $definition)
	{
		$columns[] = "$name " . $definition['query'];
	}

	$query .= implode(", ", $columns);
	$query .= ");";

	try {
		$db->exec($query);
	} catch (PDOException $e) {
		logFile ("Erreur lors de la création de la table : " . $e->getMessage());
	}
}

/**
 * @param string $table
 * @param array $data => ['column1' => 'value1', 'column2' => 'value2', ...]
 * @return bool
 */
function db_insert(string $table, array $data): bool
{
	$db = db_connect();

	$columns = implode(', ', array_keys($data));
	$placeholders = ':' . implode(', :', array_keys($data));

	$query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
	$result = $db->prepare($query);

	$values = [];
	foreach ($data as $key => $value) {
		$values[':' . $key] = $value;
	}

	try {
		$result->execute($values);
	}
	catch (PDOException $e)
	{
		logFile("Erreur lors de l'insertion dans la table : " . $e->getMessage());
		return false;
	}

	return true;
}

/**
 * @param string $table
 * @param int $id
 * @param array $data => ['column1' => 'value1', 'column2' => 'value2', ...]
 * @return void
 */
function db_update(string $table, int $id, array $data): void
{
	$db = db_connect();

	$sets = [];
	foreach ($data as $column => $value) {
		$sets[] = "$column = :$column";
	}
	$setString = implode(', ', $sets);

	$query = "UPDATE $table SET $setString WHERE id = :id";
	$result = $db->prepare($query);

	$data['id'] = $id;
	$values = [];
	foreach ($data as $key => $value) {
		$values[':' . $key] = $value;
	}

	try {
		$result->execute($values);
	}
	catch (PDOException $e)
	{
		logFile("Erreur lors de la mise à jour de la table : " . $e->getMessage());
	}
}

/**
 * @param string $modelTable
 * @param array $modelFields
 * @param array $modelData
 * @return bool
 */
function db_create_model(string $modelTable, array $modelFields, array $modelData): bool
{
	$isUnique = true;

	foreach ($modelFields as $field => $value)
	{
		if (!$value['required'])
		{
			continue;
		}

		$fetched = count(db_fetch_data(getUser_table(), $field, $modelData[$field]));
		if ($fetched > 0)
		{
			$isUnique = false;
			break;
		}
	}

	if (!$isUnique)
	{
		return false;
	}

	$data = [];
	foreach ($modelFields as $field => $details)
	{
		if (isset($modelData[$field]))
		{
			if (db_verify_data($modelData[$field], $details['type']))
			{
				$data[$field] = $modelData[$field];
			}
			else
			{
				logFile("$field is not in type " . $details['type']);
				break;
			}
		}
		elseif ($details['required'])
		{
			logFile("$field is required but not filled.");
			break;
		}
	}

	return db_insert($modelTable, $data);
}

/**
 * @param $data
 * @param string $type
 * @return bool
 */
function db_verify_data($data, string $type): bool
{
	switch ($type)
	{
		case 'int':
			return is_int($data);
		case 'string':
			return is_string($data);
		default:
			logFile("Value type is not compatible or not yet implemented.");
			return false;
	}
}
