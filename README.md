# Projet : LiveTransfer

---

## Prérequis

- PHP 8.3.x
- Node JS 18.7.x

## Installation

### Front

1 - Installation des composants requis

```bash
npm install
```

2 - Génération du fichier de styles nécessaire à l'affichage de nos pages

```bash
npm run build
```

3 - Génération des liens symboliques requis

```bash
ln -r -s ./storage/public ./public/storage
ln -r -s ./resources/js/app.js ./public/build/app.js
```

4 - Génération des fichiers de chargements

```bash
composer dump-autoload
```

### Back

1 - Modification du fichier environnement

- Dupliquez le fichier `example.env` en `.env` dans le même répertoire (racine).
- Editez les valeurs du fichier `.env` selon vos données d'installation.

2 - Installation des tables requises

- Les tables sont crées de manière automatique et un utilisateur par défaut est
  accessible au premier lancement de l'application

```json
{
    "email": "admin@email.com",
    "password": "password"
}
```
