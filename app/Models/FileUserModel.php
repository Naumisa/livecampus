<?php

namespace app\Models;

/**
 * Modèle pour la table d'association `file_user` qui gère les relations many-to-many entre les fichiers et les utilisateurs.
 * Cette classe permet de représenter et manipuler les relations qui définissent quels utilisateurs ont accès à quels fichiers.
 * Elle inclut des références aux identifiants des utilisateurs et des fichiers pour faciliter la gestion des droits d'accès.
 */
class FileUserModel extends Model
{
    protected static string $table = 'file_user';

    protected static array $fields = [
        'user_id' => [
            'type' => 'int',
            'required' => true,
            'unique' => false,
            'query' => 'INT',
        ],
        'file_id' => [
            'type' => 'int',
            'required' => true,
            'unique' => false,
            'query' => 'INT',
        ],
    ];
    protected static array $foreign_fields = [
        'user_id' => [
            'refer_to' => 'users(id)',
	        'on_delete' => 'cascade',
        ],
        'file_id' => [
            'refer_to' => 'files(id)',
	        'on_delete' => 'cascade',
        ],
    ];

	public int $user_id = 0;
	public int $file_id = 0;
}
