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
            echo "Erreur : Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        } elseif ($fileSize > $maxFileSize) {
            echo "Erreur : La taille du fichier dépasse la limite de 20 Mo.";
        } else {
            $userEmail = auth_user()->email;
            $hashedUserId = md5($userEmail); // Hachage de l'email de l'utilisateur

            global $root;
            $targetDir = $root . app_get_path('public_storage') . "uploads/" . $hashedUserId . "/"; // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true); // Créer le dossier s'il n'existe pas
            }

            $fileName = $file["name"];
            $targetFile = $targetDir . $fileName;

            // Téléchargement du fichier avec un nom différent
            $newFileName = "file_" . time() . "." . $fileExtension;
            $newTargetFile = $targetDir . $newFileName;

            if (move_uploaded_file($file["tmp_name"], $newTargetFile)) {

                // Insérer les informations du fichier dans la base de données
                $name_origine = $file["name"];

                /* TODO-Hind
				try {
					file_insert($name_origine, $newFileName, 0); // Le compteur de téléchargement est initialement défini sur 0
				}
				catch (Exception $e)
				{
					echo "Erreur lors du téléchargement du fichier : " . $e->getMessage();
					die();
				}
				*/

                /// INFO: Pour éviter d'afficher de manière non structurée sur la page
                /// des éléments d'informations :
                /// > log_info("Le fichier a été téléchargé avec succès.");
                /// Ceci va t'ajouter le retour de cette ligne dans le fichier :
                /// > /storage/logs/latest.log
                /// Une prochaine fonction permettra de stocker l'information
                /// pour en faire un retour propre sur la page.
                echo "Le fichier a été téléchargé avec succès.";
            } else {
                echo "Erreur lors du téléchargement du fichier.";
            }
        }
    }

    // Récupérer les fichiers de l'utilisateur depuis la base de données
    // $userId = (auth_user()); // Fonction fictive pour récupérer l'ID de l'utilisateur connecté
    //$files = file_get_by_user($userId); // Fonction pour récupérer les fichiers de l'utilisateur depuis la base de données

    return [
        //'files' => $files,
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
    $targetDir = $root . app_get_path('public_storage') . "uploads/" . $hashedUserId . "/"; // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

    $file = new FileModel;
    // cherche le fichier avec l'id $fileId   
    $file->find($fileId);
    $fileUserId = $file->owner_id;
    if ($fileUserId === $userId) {
        unlink($targetDir . $file->name_random);
        $file->delete();
    }

    $data = [];

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}
