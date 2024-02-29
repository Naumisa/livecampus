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

### Back

1 - Modification du fichier environnement

- Editez les valeurs du fichier `example.env` selon vos données d'installation.
- Renommez ce fichier en `.env`.

2 - Installation des tables requises

*Coming soon*
