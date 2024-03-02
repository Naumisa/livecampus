<?php

use app\Models\FileModel;
use app\Models\FileUserModel;
use app\Models\UserModel;

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
    if ($file != null)
	{
        $excludedExtensions = array("php", "html", "js", "sql"); // Extensions autorisées
        $maxFileSize = 20 * 1024 * 1024; // Taille maximale autorisée (20 Mo)

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = $file["size"];

        if (in_array($fileExtension, $excludedExtensions))
		{
            log_file ("Erreur : Les fichiers de type $fileExtension ne sont pas autorisés.");
			die();
        }
		elseif ($fileSize > $maxFileSize)
		{
            log_file ("Erreur : La taille du fichier dépasse la limite de 20 Mo.");
			die();
        }
		else {
            $targetDir = auth_user()->storage_path(); // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

            if (!file_exists($targetDir))
			{
                mkdir($targetDir, 0777, true); // Créer le dossier s'il n'existe pas
            }

            // Téléchargement du fichier avec un nom différent
            $newFileName = "file_" . time() . "." . $fileExtension;
            $newTargetFile = $targetDir . $newFileName;

            if (move_uploaded_file($file["tmp_name"], $newTargetFile))
			{
                // Insérer les informations du fichier dans la base de données
                $name_origine = $file["name"];

				try {
                    $fileModel = new FileModel();//Création du fichier
                    $data = $fileModel->fill($name_origine, $newFileName);//L'envoy du fichier
                    $data['owner_id'] = auth_user()->id;//Recherche de l'id
                    $fileModel->create($data);

					header('Location: /user/dashboard');
					log_file ("Le fichier a été téléchargé avec succès.");
					die();
                }
				catch (Exception $e)
				{
                    log_file ("Erreur lors du téléchargement du fichier : " . $e->getMessage());
					die();
				}
            }
			else {
                log_file ("Erreur lors du téléchargement du fichier.");
				die();
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
    // Chemin du fichier
    $targetDir = $user->storage_path(); // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

    // cherche le fichier avec l'id $fileId
    $file = FileModel::find($fileId);
    $fileUserId = $file->owner_id;
    if ($fileUserId === $userId) {
        unlink($targetDir . $file->name_random);
        $file->delete();

		header('Location: ' . routes_go_to_route('dashboard'));
		die();
    }

    $user = auth_user();
	$data['files'] = $user->files();
	$data['user_storage_path'] = $user->storage_path();

    return [
        'data' => $data,
        'view' => "user/dashboard",
    ];
}

function share(): array
{
	// recupere l'email saisie de l'utilisateur à qui partager le fichier
	$userMail = filter_input(INPUT_POST, 'mail_to_share'); //paramettre a modifier en fonction du button
	// recupere les donnees de l'utilisateur correpondant a l'email
	$user = new UserModel;
	$user->first('email', $userMail);
	if ($user->email === null)
	{
		log_file("Cette e-mail n'existe pas");
	}
	else {
		// recupere l'id de l'utilisateur
		$userId = $user->id;
		// recupere l'id du fichier
		$fileId = (int)routes_get_params('id');
		// creer les donnees a envoyer
		$data = ['user_id' => $userId, 'file_id' => $fileId];
		// ajoute a la table file_user les donnees
		$fileUser = new FileUserModel;
		$fileUser->create($data);

		header('Location: ' . routes_go_to_route('files.shared'));
		die();
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
 * Fonction de contrôleur pour gérer l'affichage des fichiers partagés.
 *
 * @return array Données et vue à afficher.
 */
function shared(): array
{
	$user = auth_user();
	$data['files'] = $user->sharedFiles();
	$data['user_storage_path'] = $user->storage_path();

	if (!empty($data['files']) > 0) {
		$disk_space = 0;

		foreach ($data['files'] as $file)
		{
			$disk_space += filesize($file->data['path'] . $file->name_random);
		}

		$data['disk_space'] = $disk_space;
	}

	return [
		'data' => $data,
		'view' => "user/dashboard",
	];
}
