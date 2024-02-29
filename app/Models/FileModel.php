<?php
function file_get_table(): string
{
    return 'files';
}

function file_get_fields(): array
{
    return [
        'id' => [
            'type' => 'int',
            'required' => false,
            'unique' => true,
            'query' => 'INT AUTO_INCREMENT PRIMARY KEY',
        ],
        'name_origine' => [
            'type' => 'string',
            'required' => true,
            'unique' => false,
            'query' => 'VARCHAR(255) NOT NULL',
        ],
        'name_random' => [
            'type' => 'string',
            'required' => true,
            'unique' => true,
            'query' => 'VARCHAR(255) NOT NULL',
        ],
        'download_count' => [
            'type' => 'int',
            'required' => false,
            'unique' => false,
            'query' => 'INT DEFAULT 0',
        ],
        'created_at' => [
            'type' => 'timestamp',
            'required' => false,
            'unique' => false,
            'query' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ],
        'updated_at' => [
            'type' => 'timestamp',
            'required' => false,
            'unique' => false,
            'query' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ],
    ];
}

function file_get_data_array(int $id, string $name_origine, string $name_random, int $download_count)
{
    return [
        'file_id' => $id,
        'name_origine' => $name_origine,
        'name_random' => $name_random,
        'download_count' => $download_count
    ];
}

function file_delete(int $id): void
{
    if (!db_delete(file_get_table(), $id)) {
        log_file("Attempted to delete file.");
    }
}

function file_insert(int $userId, string $name_origine, string $name_random): void
{
    $data = file_get_data_array(0, $name_origine, $name_random, 0);
    $data['user_id'] = $userId;
    if (!db_insert(file_get_table(), $data)) {
        log_file("Failed to insert file into database.");
    }
}

function file_get_by_user(int $userId): array
{
    $conditions = ['user_id' => $userId];
    $files = db_select(file_get_table(), $conditions);
    return $files ?: [];
}
