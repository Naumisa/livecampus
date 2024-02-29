<?php
/**
 * @return array
 */
function index(): array
{
    $data = [];

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
            $userId = get_current_user();
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
            } else {
                echo "Erreur lors du téléchargement du fichier.";
            }
        }
    }

    // Récupérer les fichiers de l'utilisateur depuis la base de données
    $userId = get_current_user(); // Fonction fictive pour récupérer l'ID de l'utilisateur connecté
    $files = get_user_files($userId); // Fonction fictive pour récupérer les fichiers de l'utilisateur depuis la base de données

    // Afficher la liste des fichiers dans l'interface utilisateur
    if (!empty($files)) {
        echo "<h3>Liste de vos fichiers précédemment envoyés :</h3>";
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li><a href='download.php?file_id={$file['id']}'>{$file['filename']}</a></li>";
        }
        echo "</ul>";
    } else {
        echo "Aucun fichier n'a été envoyé précédemment.";
    }

    return [
        'data' => $data,
        'view' => "file/index",
    ];
}
?>
