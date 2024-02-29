<?php
/**
 * Fonction de contrôleur pour gérer l'index de la page des fichiers.
 *
 * @return array Données et vue à afficher.
 */
function index(): array
{
    $data = [];

    // Traitement du formulaire pour l'ajout de fichiers
    if(isset($_POST['ajouter'])){
        $allowedExtensions = array("jpg", "jpeg", "png", "gif"); // Extensions autorisées
        $maxFileSize = 20 * 1024 * 1024; // Taille maximale autorisée (20 Mo)

        $fileExtension = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
        $fileSize = $_FILES["fileToUpload"]["size"];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "Erreur : Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        } elseif ($fileSize > $maxFileSize) {
            echo "Erreur : La taille du fichier dépasse la limite de 20 Mo.";
        } else {
            $userId = auth_user();
            $hashedUserId = md5($userId); // Hachage de l'ID de l'utilisateur

            $targetDir = "storage/" . $hashedUserId . "/"; // Dossier de stockage basé sur le hachage de l'ID de l'utilisateur

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true); // Créer le dossier s'il n'existe pas
            }

            $fileName = $_FILES["fileToUpload"]["name"];
            $targetFile = $targetDir . $fileName;

            // Téléchargement du fichier avec un nom différent
            $newFileName = "file_" . time() . "." . $fileExtension;
            $newTargetFile = $targetDir . $newFileName;

            if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newTargetFile)) {
                echo "Le fichier a été téléchargé avec succès.";

                // Insérer les informations du fichier dans la base de données
                $name_origine = $_FILES["fileToUpload"]["name"];
                file_insert($name_origine, $newFileName, 0); // Le compteur de téléchargement est initialement défini sur 0
            } else {
                echo "Erreur lors du téléchargement du fichier.";
            }
        }
    }

    // Récupérer les fichiers de l'utilisateur depuis la base de données
    $userId = (auth_user()); // Fonction fictive pour récupérer l'ID de l'utilisateur connecté
    $files = file_get_by_user($userId); // Fonction pour récupérer les fichiers de l'utilisateur depuis la base de données

    return [
        'files' => $files,
        'data' => $data,
        'view' => "file/index",
    ];
}