<?php
/**
 * @return array
 */
function index(): array
{
    $data = [];

    return [
        'data' => $data,
        'view' => "file/index",
    ];
}

// Traitement du formulaire pour l'ajout de fichiers
if (isset($_POST['ajouter'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
}

// Traitement du formulaire pour la suppression de fichiers
if (isset($_POST['supprimer'])) {
    $fileToDelete = $_POST['fileToDelete'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
    }
}

// Traitement du formulaire pour le téléchargement de fichiers
if (isset($_POST['telecharger'])) {
    $fileToDownload = $_POST['fileToDownload'];
    if (file_exists($fileToDownload)) {
        header('Content-Disposition: attachment; filename=' . basename($fileToDownload));
        readfile($fileToDownload);
        exit;
    }
}

// Traitement du formulaire pour l'envoi de fichiers par email
if (isset($_POST['envoyer'])) {
    $fileToSend = $_POST['fileToSend'];
    $destinataire = $_POST['destinataire'];
    $sujet = 'Fichier envoyé depuis le formulaire';
    $message = 'Veuillez trouver le fichier en pièce jointe.';
    $headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    if (file_exists($fileToSend)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileToSend);
        $fileContent = file_get_contents($fileToSend);
        $fileAttachment = chunk_split(base64_encode($fileContent));
        $boundary = md5(time());

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n\r\n";

        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-Type: " . $mime . "; charset=\"ISO-8859-1\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"" . basename($fileToSend) . "\"\r\n\r\n";
        $message .= $fileAttachment . "\r\n\r\n";
        $message .= "--" . $boundary . "--";

        mail($destinataire, $sujet, $message, $headers);
    }
}

