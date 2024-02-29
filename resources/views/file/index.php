<section id="file">
    <img src="<?= app_get_path('public_storage') ?>images/home.jpg"  alt=""/>

    <!-- Formulaire pour ajouter un nouveau fichier -->
    <h3>Ajouter un nouveau fichier :</h3>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload">
        <input type="submit" value="Ajouter" name="ajouter">
    </form>
</section>
