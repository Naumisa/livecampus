<?php

namespace back\Models;

use Exception;
use PDO;
use PDOException;

class DatabaseModel
{
	private PDO $pdo;

	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->connect();
	}

	/**
	 * @throws Exception
	 */
	public function connect(): void
	{
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];

		try {
			$this->pdo = new PDO(
				'mysql:host=' . $this->getEnv('DB_HOST', 'localhost') .
				';dbname=' . $this->getEnv('DB_NAME', 'database') .
				';charset=utf8',
				$this->getEnv('DB_USER', 'root'),
				'',
				$options,
			);
		}
		catch (PDOException $e)
		{
			$logMessage = "Erreur lors de la connexion à la database : " . $e->getMessage();
			log_file($logMessage);
		}
	}

	private function getEnv(string $name, string $default = ''): string
	{
		return getenv($name) ?: $default;
	}

	/**
	 * Crée une nouvelle table en base de données avec les colonnes spécifiées.
	 * Peut également ajouter des contraintes de clé étrangère si spécifiées.
	 *
	 * @param string $table Le nom de la table à créer.
	 * @param array $columns La définition des colonnes de la table.
	 * @param array|null $foreignKeys Les définitions des contraintes de clé étrangère, si présentes.
	 * @return bool True si la création réussit, False sinon.
	 */
	public function create_table(string $table, array $columns, ?array $foreignKeys = null): bool
	{
		$table_exist = $this->pdo->prepare("SELECT tables.table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . getenv('DB_NAME') . "' AND TABLE_NAME = '$table';");
		$table_exist->execute();
		if ($table_exist->fetch() !== false)
		{
			return false;
		}

		try {
			$this->pdo->beginTransaction();

			$columns = array_map(function ($name, $def) {
				return "$name {$def['query']}";
			}, array_keys($columns), $columns);

			$query = "CREATE TABLE IF NOT EXISTS $table (" . implode(", ", $columns) . ");";

			if (!empty($foreignKeys))
			{
				foreach ($foreignKeys as $fk => $def)
				{
					if (isset($def['referred_by']))
					{
						$query .= "ALTER TABLE {$def['referred_by']['table']} ADD CONSTRAINT fk_{$def['referred_by']['table']}_$fk FOREIGN KEY ({$def['referred_by']['column']}) REFERENCES $table($fk)";
						$query .= !isset($def['referred_by']['on_delete']) ? '' : " ON DELETE {$def['referred_by']['on_delete']}";
						$query .= !isset($def['referred_by']['on_update']) ? '' : " ON UPDATE {$def['referred_by']['on_update']}";
					}
					else
					{
						$query .= "ALTER TABLE $table ADD CONSTRAINT fk_$table"."_"."$fk FOREIGN KEY ($fk) REFERENCES {$def['refer_to']}";
						$query .= !isset($def['on_delete']) ? '' : " ON DELETE {$def['on_delete']}";
						$query .= !isset($def['on_update']) ? '' : " ON UPDATE {$def['on_update']}";
					}
					$query .= ";";
				}
			}

			$this->pdo->exec($query);
			return true;
		}
		catch (PDOException $e)
		{
			$logMessage = "Erreur lors de la connexion à la database : " . $e->getMessage();
			log_file($logMessage);
			return false;
		}
	}

	/**
	 * Récupère toutes les entrées d'une table spécifique.
	 *
	 * @param string $table Le nom de la table à interroger.
	 * @return array|null Les données récupérées ou null en cas d'échec.
	 */
	public function fetch_table(string $table): ?array
	{
		$query = "SELECT * FROM $table";
		try {
			$result = $this->pdo->prepare($query);
			$result->execute();
			return $result->fetchAll();
		}
		catch (PDOException $e)
		{
			$logMessage = "Erreur lors de la récupération de la table $table : " . $e->getMessage();
			log_file($logMessage);
			return null;
		}
	}

	/**
	 * Récupère les données spécifiques d'une table en fonction d'une colonne et de sa valeur.
	 *
	 * @param string $table Le nom de la table à interroger.
	 * @param string $column Le nom de la colonne pour la condition WHERE.
	 * @param mixed $value La valeur à rechercher dans la colonne spécifiée.
	 * @return array|null Les données récupérées ou null en cas d'échec.
	 */
	public function fetch_by_column(string $table, string $column, mixed $value): ?array
	{
		$query = "SELECT * FROM $table WHERE $column = :value";
		try {
			$result = $this->pdo->prepare($query);
			$result->execute(['value' => $value]);
			return $result->fetchAll();
		}
		catch (PDOException $e)
		{
			$logMessage = "Erreur lors de la récupération des données depuis $table où $column = $value : " . $e->getMessage();
			log_file($logMessage);
			return null;
		}
	}

	/**
	 * Insère de nouvelles données dans une table spécifique.
	 *
	 * @param string $table Le nom de la table où insérer les données.
	 * @param array $data Les données à insérer sous forme de tableau associatif colonne => valeur.
	 * @return int ID du nouvel objet si l'insertion réussie, 0 sinon.
	 */
	public function insert(string $table, array $data): int
	{
		$columns = implode(', ', array_keys($data));
		$placeholders = ':' . implode(', :', array_keys($data));

		$query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

		try {
			$result = $this->pdo->prepare($query);
			$result->execute($data);
			return (int) $this->pdo->lastInsertId();
		}
		catch (PDOException $e) {
			$logMessage = "Erreur lors de l'insertion dans la table $table : " . $e->getMessage();
			log_file($logMessage);
			return 0;
		}
	}

	/**
	 * Met à jour les données d'une entrée spécifique dans une table en fonction de son ID.
	 *
	 * @param string $table Le nom de la table à mettre à jour.
	 * @param int $id L'ID de l'entrée à mettre à jour.
	 * @param array $data Les nouvelles données sous forme de tableau associatif colonne => valeur.
	 * @return bool True si la mise à jour réussit, False sinon.
	 */
	public function update(string $table, int $id, array $data): bool
	{
		$sets = array_map(function ($column) {
			return "$column = :$column";
		}, array_keys($data));

		$setString = implode(', ', $sets);

		$query = "UPDATE $table SET $setString WHERE id = $id";

		try {
			$result = $this->pdo->prepare($query);
			$result->execute($data);
			return true;
		}
		catch (PDOException $e) {
			log_file("Erreur lors de la mise à jour de la table $table : " . $e->getMessage());
			return false;
		}
	}

	/**
	 * Supprime une entrée spécifique d'une table en fonction de son ID.
	 *
	 * @param string $table Le nom de la table où supprimer l'entrée.
	 * @param int $id L'ID de l'entrée à supprimer.
	 * @return bool True si la suppression réussit, False sinon.
	 */
	public function delete(string $table, int $id): bool
	{
		$query = "DELETE FROM $table WHERE id = :id";

		try {
			$result = $this->pdo->prepare($query);
			$result->execute(['id' => $id]);
			return true;
		}
		catch (PDOException $e) {
			log_file("Erreur lors de la suppression dans la table $table : " . $e->getMessage());
			return false;
		}
	}

	public function set_foreign_key_check(bool $value): void
	{
		$value = (int) $value;
		$query = "SET FOREIGN_KEY_CHECKS = $value";

		try {
			$result = $this->pdo->prepare($query);
			$result->execute();
			return;
		}
		catch (PDOException $e) {
			log_file("Erreur lors de la modification du champ FOREIGN_KEY_CHECKS : " . $e->getMessage());
			return;
		}
	}
}
