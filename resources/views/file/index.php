<section id="login">
    <h2><?= lang_get('file.title') ?></h2>
    <img src="<?= app_get_path('public_storage') ?>images/home.jpg"  alt=""/>

    <h2>Ajouter un fichier</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload">
        <input type="submit" value="Ajouter" name="ajouter">
    </form>

    <h2>Supprimer un fichier</h2>
    <form action="" method="post">
        <label>
            <select name="fileToDelete">
                <?php
                $files = glob("uploads/*");
                foreach($files as $file){
                    echo "<option value=\"$file\">".basename($file)."</option>";
                }
                ?>
            </select>
        </label>
        <input type="submit" value="Supprimer" name="supprimer">
    </form>

    <h2>Télécharger un fichier</h2>
    <form action="" method="post">
        <label>
            <select name="fileToDownload">
                <?php
                foreach($files as $file){
                    echo "<option value=\"$file\">".basename($file)."</option>";
                }
                ?>
            </select>
        </label>
        <input type="submit" value="Télécharger" name="telecharger">
    </form>

    <h2>Envoyer un fichier par email</h2>
    <form action="" method="post">
        <label>
            <select name="fileToSend">
                <?php
                foreach($files as $file){
                    echo "<option value=\"$file\">".basename($file)."</option>";
                }
                ?>
            </select>
        </label>
        <input type="email" name="destinataire" placeholder="Destinataire">
        <input type="submit" value="Envoyer" name="envoyer">
    </form>
</section>