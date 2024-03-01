<?php

use app\Models\FileUserModel;
use app\Models\UserModel;

/**
 * Fonction de contrôleur pour gérer l'ajout de fichier de partage.
 */
function addUserToConsult(): array
{
    // recupere l'email saisie de l'utilisateur à qui partager le fichier
    $userMail = filter_input(INPUT_POST, 'mail_to_share'); //paramettre a modifier en fonction du button
    //$userMail = routes_get_params('email'); // test a la mano
    // recupere les donnees de l'utilisateur correpondant a l'email
    $user = new UserModel;
    $user->first('email', $userMail);
    if ($user->email === null) {
        echo "Cette e-mail n'existe pas";
    } else {
        echo 'coucou';
        // recupere l'id de l'utilisateur
        $userId = $user->id;
        // recupere l'id du fichier
        $fileId = (int)routes_get_params('id');
        // creer les donnees a envoyer
        $data = ['user_id' => $userId, 'file_id' => $fileId];
        // ajoute a la table file_user les donnees
        $fileUser = new FileUserModel;
        $fileUser->create($data);
    }
    $data = [];

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}

/**
 * Fonction de contrôleur pour gérer l'affichage des fichiers partagés.
 *
 * @return array Données et vue à afficher.
 */
function addUserDownload(): array
{
    $user = auth_user();
    $data['files'] = $user->sharedFiles();
    $data['user_storage_path'] = $user->storage_path();

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}
