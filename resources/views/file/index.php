<section id="file">
    <img src="<?= app_get_path('public_storage') ?>images/home.jpg"  alt=""/>

    <!-- Affichage de la liste des fichiers précédemment envoyés -->
    <?php if (!empty($files)): ?>
        <h3>Vos fichiers précédemment envoyés :</h3>
        <ul>
            <?php foreach ($files as $file): ?>
                <li><a href="download.php?file_id=<?= $file['id'] ?>"><?= $file['filename'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun fichier n'a été envoyé précédemment.</p>
    <?php endif; ?>

    <!-- Formulaire pour ajouter un nouveau fichier -->
    <h3>Ajouter un nouveau fichier :</h3>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload">
        <input type="submit" value="Ajouter" name="ajouter">
    </form>
</section>
