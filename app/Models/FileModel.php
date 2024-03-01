<?php

/**
 * Retourne le nom de la table des fichiers.
 * @return string Le nom de la table.
 */
function file_get_table(): string
{ return 'files'; }

/**
 * Définit la structure de la table des fichiers, y compris les types de données,
 * les contraintes et les valeurs par défaut des champs.
 * @return array La définition des champs de la table des fichiers.
 */
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
            'query' => 'VARCHAR(255) NOT NULL UNIQUE',
        ],
        'download_count' => [
            'type' => 'int',
            'required' => false,
            'unique' => false,
            'query' => 'INT DEFAULT 0',
        ],
		'owner_id' => [
			'type' => 'int',
			'required' => false,
			'unique' => false,
			'query' => 'INT NOT NULL',
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

/**
 * Renvoie la définition des clés étrangères pour la table des fichiers.
 * @return array Les définitions des clés étrangères.
 */
function file_get_foreign_keys (): array
{
	return [
		'owner_id' => [
			'refer_to' => 'users(id)',
			'on_delete' => 'CASCADE',
		],
	];
}

/**
 * @param int $id
 * @param string $name_origine
 * @param string $name_random
 * @param int $download_count
 * @return array
 */
function file_get_data_array(int $id, string $name_origine, string $name_random, int $download_count): array
{
    return [
        'file_id' => $id,
        'name_origine' => $name_origine,
        'name_random' => $name_random,
        'download_count' => $download_count
    ];
}

/**
 * Crée ou migre la table des fichiers dans la base de données.
 * Applique la structure de table et les contraintes de clé étrangère définies.
 *
 * @return void
 */
function file_migrate(): void
{
	db_create_table(file_get_table(), file_get_fields(), file_get_foreign_keys());
}

/**
 * Supprime un fichier spécifié par son ID de la base de données.
 * @param int $id L'ID du fichier à supprimer.
 * @return void
 */
function file_delete(int $id): void
{
    if (!db_delete(file_get_table(), $id))
	{
        log_file("Erreur lors de la tentative de suppression du fichier $id.");
    }
}

/* TODO-Hind : Correction de la création
function file_create(int $userId, string $name_origine, string $name_random): void
{
    $data = file_get_data_array(0, $name_origine, $name_random, 0);
    $data['user_id'] = $userId;
    if (!db_insert(file_get_table(), $data)) {
        log_file("Failed to insert file into database.");
    }
}
*/

/**
 * Met à jour les informations d'un fichier spécifique dans la base de données.
 * @param int $id L'ID du fichier à mettre à jour.
 * @param array $data Les données à mettre à jour (clé => valeur).
 * @return void
 */
function file_update(int $id, array $data): void
{
	if (!db_update(file_get_table(), $id, $data))
	{
		log_file("Erreur lors de la tentative de mise à jour du fichier ($id).");
	}
}

/**
 * Récupère les données d'un fichier spécifique par son ID.
 * @param int $id L'ID du fichier à récupérer.
 * @return array|null Les données du fichier spécifié.
 */
function file_get_data_with_id (int $id): ?array
{
	return db_fetch_data(file_get_table(), 'id', $id)[0];
}

/**
 * Récupère les données des fichiers appartenant à un utilisateur spécifique.
 * @param int $userId L'ID de l'utilisateur.
 * @return array Les données des fichiers.
 */
function file_get_data_with_user_id (int $userId): array
{
	return db_fetch_data(file_get_table(), 'fk_owner_id', $userId);
}
