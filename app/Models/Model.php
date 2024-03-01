<?php

namespace app\Models;

abstract class Model
{
	const MODEL_READY = 0;
	const MODEL_CREATED = 1;
	const MODEL_DELETED = 2;

	protected string $table;
	protected array $fields = [];
	protected array $foreign_fields = [];

	public int $id = 0;
	protected string $created_at = '';
	protected string $updated_at = '';

	private int $state = 0;

	private array $query = [];

	public function migrate(): void
	{
		db_create_table($this->table, $this->fields, $this->foreign_fields);
	}

	public function create(array $data): void
	{
		$this->id = db_create_model($this->table, $this->fields, $data);

		if ($this->id != null) {
			$newModel = Model::find($this->id);

			foreach ($this->fields as $field => $definition) {
				if (!isset($this->$field) and $this->$field != null) {
					continue;
				}

				$this->$field = $newModel->$field;
			}
		} else {
			log_file("Une erreur est survenue lors de la tentative de création du Model User.");
		}

		$this->state = self::MODEL_CREATED;
	}

	public function save(): void
	{
		if ($this->state === static::MODEL_DELETED) {
			log_file("Tentative de mise à jour d'un Model précédemment supprimé.");
			return;
		}

		$this->updated_at = date('Y-m-d H:i:s');

		$data = [];

		foreach ($this->fields as $field => $definition) {
			if (!isset($this->$field) and $this->$field != null) {
				continue;
			}

			$data[$field] = $this->$field;
		}

		if (!db_update($this->table, $this->id, $data)) {
			log_file("Une erreur est survenue lors de la tentative de sauvegarde du Model.");
		}
	}

	public function delete(): void
	{
		if ($this->state === static::MODEL_DELETED) {
			log_file("Tentative de suppression d'un Model précédemment supprimé.");
			return;
		}

		if (!db_delete($this->table, $this->id)) {
			log_file("Une erreur est survenue lors de la tentative de suppression du Model.");
		}
	}

	public function find(int $id): ?Model
	{
		return $this->first('id', $id);
	}

	public function first(string $column, string $value): ?Model
	{
		$this->where($column, $value);

		if (empty($this->query))
		{
			return null;
		}

		return $this->get(0);
	}

	public function get(int $id): Model
	{
		return $this->parse_in_model($this->query[$id]);
	}

	public function where(string $column, string $value): int
	{
		$this->query = db_fetch_data($this->table, $column, $value);
		return !empty($this->query) ? count($this->query) : 0;
	}

	public function all(): ?array
	{
		return db_fetch_table($this->table);
	}

	public function parse_in_model(array $data): Model
	{
		foreach ($data as $field => $value)
		{
			$this->$field = $value;
		}

		return $this;
	}
}
