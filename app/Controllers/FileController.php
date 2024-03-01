<?php

use app\Models\FileModel;

/**
 * Fonction de contrôleur pour gérer l'envoi des fichiers.
 *
 * @return array Données et vue à afficher.
 */
function upload(): array
{
    $data = [];

    $file = $_FILES['dropzone-file'];

    // Traitement du formulaire pour l'ajout de fichiers
    if ($file != null) {
        $allowedExtensions = array("jpg", "jpeg", "png", "gif"); // Extensions autorisées
        $maxFileSize = 20 * 1024 * 1024; // Taille maximale autorisée (20 Mo)

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = $file["size"];

        if (!in_array($fileExtension, $allowedExtensions)) {
            log_file ("Erreur : Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.");
        } elseif ($fileSize > $maxFileSize) {
            log_file ("Erreur : La taille du fichier dépasse la limite de 20 Mo.");
        } else {
            $targetDir = auth_user()->storage_path(); // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true); // Créer le dossier s'il n'existe pas
            }

            // Téléchargement du fichier avec un nom différent
            $newFileName = "file_" . time() . "." . $fileExtension;
            $newTargetFile = $targetDir . $newFileName;

            if(move_uploaded_file($file["tmp_name"], $newTargetFile)) {

                // Insérer les informations du fichier dans la base de données
                $name_origine = $file["name"];

				try {
                    $fileModel = new FileModel();//Création du fichier
                    $data = $fileModel->fill($name_origine, $newFileName);//L'envoy du fichier
                    $data['owner_id'] = auth_user()->id;//Recherche de l'id
                    $fileModel->create($data);
                }
				catch (Exception $e)
				{
                    log_file ("Erreur lors du téléchargement du fichier : " . $e->getMessage());
					die();
				}

                log_file ("Le fichier a été téléchargé avec succès.");
            } else {
                log_file ("Erreur lors du téléchargement du fichier.");
            }
        }
    }

    $user = auth_user();
	$data['files'] = $user->files();
	$data['user_storage_path'] = $user->storage_path();

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}

/**
 * @return array
 */
function delete(): array
{
    // Récupère l'ID du fichier dont la suppression a été demandée
    $fileId = routes_get_params('id');
    // Récupère l'ID de l'utilisateur qui à créer le fichier
    // doit vérifier si le fichier appartient bien à l'utilisateur connecté
    $user = auth_user();
    $userId = $user->id;
    // recupere le mail de l'utilisateur
    $userEmail = $user->email;
    // Hachage de l'email de l'utilisateur
    $hashedUserId = md5($userEmail);
    // Chemin du fichier
    global $root;
    $targetDir = $user->storage_path(); // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

    $file = new FileModel;
    // cherche le fichier avec l'id $fileId
    $file->find($fileId);
    $fileUserId = $file->owner_id;
    if ($fileUserId === $userId) {
        unlink($targetDir . $file->name_random);
        $file->delete();
    }

    $user = auth_user();
	$data['files'] = $user->files();
	$data['user_storage_path'] = $user->storage_path();

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}
