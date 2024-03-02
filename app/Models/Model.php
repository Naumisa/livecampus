<?php

namespace app\Models;

use back\Models\DatabaseModel;

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

	public function __construct()
	{
		static::$db = new DatabaseModel();
	}

	public function migrate(): void
	{
		static::$db->create_table(static::$table, static::$fields, static::$foreign_fields);
	}

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

	public function save(): void
	{
		if ($this->state === self::MODEL_DELETED) {
			log_file("Tentative de mise à jour d'un Model précédemment supprimé.");
			return;
		}

		$this->updated_at = date('Y-m-d H:i:s');

		$data = $this->to_array();
		unset($data['id']);

		if (!static::$db->update(static::$table, $this->id, $data)) {
			log_file("Une erreur est survenue lors de la tentative de sauvegarde du Model.");
		}
	}

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

	public function refresh(): void
	{
		$modelData = static::$db->fetch_by_column(static::$table, 'id', $this->id);
		if ($modelData)
		{
			$this->parse_in_model($modelData[0]);
		}
	}

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

	public static function find(int $id): ?self
	{
		$instance = new static();
		$rows = static::fetch('id', $id);

		return $instance->parse_in_model($rows[0]);
	}

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

	protected function parse_in_model(array $data): self
	{
		foreach ($data as $field => $value)
		{
			$this->$field = $value;
		}

		return $this;
	}

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
