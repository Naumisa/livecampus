<?php

namespace app\Models;

use back\Models\DatabaseModel;

/**
 * Classe de base abstraite pour tous les modèles de l'application.
 * Fournit des méthodes communes pour la création, la récupération, la mise à jour, et la suppression des modèles.
 * S'appuie sur une instance de `DatabaseModel` pour l'interaction avec la base de données.
 */
abstract class Model
{
	const MODEL_READY = 0;
	const MODEL_CREATED = 1;
	const MODEL_DELETED = 2;

	protected static string $table;
	protected static array $fields = [];
	protected static array $foreign_fields = [];

	public int $id = 0;
	public string $created_at = '';
	public string $updated_at = '';
	public array $data = [];

	private int $state = self::MODEL_READY;

	protected static DatabaseModel $db;

	/**
	 * Constructeur de la classe Model.
	 * Initialise la connexion à la base de données en créant une nouvelle instance de `DatabaseModel`.
	 */
	public function __construct()
	{
		static::$db = new DatabaseModel();
	}

	/**
	 * Exécute la migration de la table associée au modèle.
	 * Crée la table dans la base de données selon la définition des champs et des champs étrangers.
	 */
	public function migrate(): void
	{
		static::$db->create_table(static::$table, static::$fields, static::$foreign_fields);
	}

	/**
	 * Crée une nouvelle instance du modèle dans la base de données avec les données fournies.
	 *
	 * @param array $data Données pour créer une nouvelle instance du modèle.
	 * @return self|null Retourne une instance du modèle si la création est réussie, sinon null.
	 */
	public static function create(array $data): ?self
	{
		$id = static::$db->insert(static::$table, $data);

		if ($id > 0) {
			return static::find($id);
		} else {
			log_file("Une erreur est survenue lors de la tentative de création du Model User.");
			return null;
		}
	}

	/**
	 * Sauvegarde les modifications de l'instance du modèle dans la base de données.
	 *
	 * @return bool Retourne true si la sauvegarde est réussie, sinon false.
	 */
	public function save(): bool
	{
		if ($this->state === self::MODEL_DELETED) {
			log_file("Tentative de mise à jour d'un Model précédemment supprimé.");
			return false;
		}

		$this->updated_at = date('Y-m-d H:i:s');

		$data = $this->to_array();
		unset($data['id']);

		if (!static::$db->update(static::$table, $this->id, $data)) {
			log_file("Une erreur est survenue lors de la tentative de sauvegarde du Model.");
			return false;
		}

		return true;
	}

	/**
	 * Supprime l'instance du modèle de la base de données.
	 */
	public function delete(): void
	{
		if ($this->state === self::MODEL_DELETED) {
			log_file("Tentative de suppression d'un Model précédemment supprimé.");
			return;
		}

		if (!static::$db->delete(static::$table, $this->id)) {
			log_file("Une erreur est survenue lors de la tentative de suppression du Model.");
		}
		else {
			$this->state = self::MODEL_DELETED;
		}
	}

	/**
	 * Rafraîchit l'instance du modèle avec les données actuelles de la base de données.
	 */
	public function refresh(): void
	{
		$modelData = static::$db->fetch_by_column(static::$table, 'id', $this->id);
		if ($modelData)
		{
			$this->parse_in_model($modelData[0]);
		}
	}

	/**
	 * Récupère toutes les instances du modèle de la base de données.
	 *
	 * @return array|null Retourne un tableau d'instances du modèle ou null si aucune instance n'est trouvée.
	 */
	public static function all(): ?array
	{
		if (!empty(static::$table)) {
			$rows = static::$db->fetch_table(static::$table);
		}

		if (!isset($rows) || !$rows)
		{
			return null;
		}

		return array_map(function ($row) {
			$instance = new static();
			return $instance->parse_in_model($row);
		}, $rows);
	}

	/**
	 * Trouve une instance du modèle par son identifiant.
	 *
	 * @param int $id Identifiant de l'instance du modèle à trouver.
	 * @return self|null Retourne l'instance du modèle si trouvée, sinon null.
	 */
	public static function find(int $id): ?self
	{
		return static::first('id', $id);
	}

	/**
	 * Trouve la première instance du modèle correspondant au critère spécifié.
	 *
	 * @param string $column Nom de la colonne pour la condition de recherche.
	 * @param string $value Valeur de la colonne pour la condition de recherche.
	 * @return self|null Retourne la première instance du modèle correspondant au critère, sinon null.
	 */
	public static function first(string $column, string $value): ?self
	{
		$instance = new static();
		$rows = static::fetch($column, $value);

		if (!$rows)
		{
			return null;
		}

		return $instance->parse_in_model($rows[0]);
	}

	/**
	 * Récupère les instances du modèle correspondant au critère spécifié.
	 *
	 * @param string $column Nom de la colonne pour la condition de recherche.
	 * @param string $value Valeur de la colonne pour la condition de recherche.
	 * @return array|null Retourne un tableau d'instances du modèle correspondant au critère, sinon null.
	 */
	public static function where(string $column, string $value): ?array
	{
		$rows = static::fetch($column, $value);

		if (!$rows)
		{
			return null;
		}

		return array_map(function ($row) {
			$instance = new static();
			return $instance->parse_in_model($row);
		}, $rows);
	}

	/**
	 * Récupère dans la base de données les données correspondant au critère spécifié.
	 *
	 * @param string $column Nom de la colonne pour la condition de recherche.
	 * @param string $value Valeur de la colonne pour la condition de recherche.
	 * @return array|null Retourne un tableau d'instances du modèle correspondant au critère, sinon null.
	 */
	protected static function fetch(string $column, string $value): ?array
	{
		if (!empty(static::$table)) {
			$rows = static::$db->fetch_by_column(static::$table, $column, $value);
		}

		if (!isset($rows) || !$rows)
		{
			return null;
		}

		return $rows;
	}

	/**
	 * Convertit un tableau associatif en instance du modèle.
	 *
	 * @return Model Un tableau associatif représentant l'instance du modèle.
	 */
	protected function parse_in_model(array $data): self
	{
		foreach ($data as $field => $value)
		{
			$this->$field = $value;
		}

		return $this;
	}

	/**
	 * Convertit l'instance du modèle en tableau associatif.
	 *
	 * @return array Un tableau associatif représentant l'instance du modèle.
	 */
	public function to_array(): array
	{
		$data = [];
		foreach (static::$fields as $field => $value)
		{
			$data[$field] = $this->$field;
		}
		return $data;
	}
}
