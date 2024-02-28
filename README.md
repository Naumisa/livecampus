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
npx tailwindcss -i ./resources/css/app.css -o ./public/build/app.css
```

3 - Génération des liens symboliques requis

```bash
ln -r -s ./storage/public ./public/storage
ln -r -s ./resources/js/app.js ./public/build/app.js
```

### Back

1 - Installation des tables requises

*Coming soon*
