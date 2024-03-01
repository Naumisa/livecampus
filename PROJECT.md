# Projet: LiveTransfer

---

# Membres du projet

---

| NOM           | Prénom   |
|---------------|----------|
| EL MOUTAWAKIL | Hind     |
| OBERLIN       | Valentin |
| SEYMORTIER    | Florian  |
| WAGNER        | Thomas   |

# Description du projet

---

## Contexte

Votre entreprise souhaiterait que vous puissiez développer une application web de type
WeTransfer, permettant de stocker et de servir des fichiers. Cette application doit être
protégée par mot de passe pour des raisons de confidentialité.

## Fonctionnalités

Le site devra disposer des fonctionnalités suivantes :
- Pour tout le monde :
  - [x] Créer un compte
  - [x] Se connecter
  - [x] Se déconnecter
- Pour une personne authentifiée
  - [x] Modifier son profil
  - [ ] Envoyer un fichier
  - [ ] Télécharger un fichier
  - [ ] Voir combien de fois un fichier a été téléchargé
  - [ ] Supprimer un fichier envoyé
  - [ ] Réserver le téléchargement uniquement à un utilisateur en particulier

## Détail des fonctionnalités

### Créer un compte

Une page doit permettre de créer un compte. Il sera demandé une adresse mail, un mot de passe
et la confirmation de ce mot de passe. Les données peuvent être stockées en base de données
ou dans un fichier, mais dans tous les cas, le mot de passe doit être hashé pour des raisons
de sécurité.

Il n'est pas nécessaire de faire une validation par mail de ce compte, par contre, il faudra
vérifier que l'adresse mail est unique pour chaque utilisateur : deux comptes ne peuvent pas
être associés à la même adresse mail.

### Se connecter

Avec son adresse mail et son mot de passe, une personne peut s'identifier à l'application. La
personne restera connectée tout au long de sa session de navigation : tant que le navigateur
n'est pas fermé, ou tant que la personne ne se déconnecte pas.

### Se déconnecter

Un lien doit être disponible pour se déconnecter, visible sur toutes les pages du site tant que
l'on est connecté.

### Modifier son profil

Une page doit être disponible pour modifier ses informations : son adresse mail, ou son mot de
passe. Dans le cas du mot de passe, il sera demandé une confirmation du mot de passe. En cas de
modification de l'email, n'oubliez pas de vérifier que l'adresse mail soit unique.

### Envoyer un fichier

Un formulaire est disponible pour ajouter un nouveau fichier. Dans ce cas, il faudra faire attention :
- À interdire l'envoi des fichiers terminant par ".php"
- À enregistrer chaque fichier dans un dossier ayant comme nom un hash de l'adresse mail de la personne,
  pour isoler chaque fichier par utilisateur
- À interdire les fichiers de plus de 20 Mo

Bonus : Enregistrer le fichier sous un nom différent (et unique) du fichier envoyé.
Exemple : Si j'envoie "image.jpg" de mon ordinateur, le serveur doit renommer "image.jpg" en (par exemple)
"8e7a0kl.jpg", tout en retenant quelque part le nom original "image.jpg". Cette action est faite pour éviter
des noms de fichiers incompatibles pour le serveur.

### Télécharger un fichier

Quand une personne est connectée à son compte, une liste des fichiers préalablement envoyés doit être
disponible dans l'interface. En cliquant sur le fichier, il doit être possible de le télécharger à nouveau.

Attention, il ne doit pas être téléchargeable par toute personne authentifiée (et encore moins toute
personne publique).

### Voir combien de fois un fichier a été téléchargé

A chaque clic sur un fichier, il faudrait qu'un compteur s'incrémente de 1 à chaque fois, pour ce fichier.
Dans l'interface, il doit être possible de savoir combien de fois chaque ficher a été téléchargé.

### Supprimer un fichier envoyé

La personne ayant envoyé un fichier doit pouvoir le supprimer. Ajoutez un bouton pour supprimer un fichier.
Une confirmation serait souhaitable, mais n'est pas obligatoire.

### Réserver le téléchargement

Il doit être possible d'associer à un fichier une adresse e-mail particulière.

Exemple de scénario :
- john.doe@test.com ajoute un fichier dans son espace personnel.
- John souhaite permettre à Alice de télécharger ce fichier : il ajoute alors l'adresse mail
  "alice.doe@test.com" à côté de ce fichier.
- Si le compte "alice.doe@test.com" existe, alors Alice pourra, en suivant le lien de téléchargement,
  télécharger ce fichier.

Attention, le fichier doit être réservé à la personne ciblée : il ne doit pas être téléchargeable par
toute personne authentifiée (et encore moins toute personne publique).

## Modalités de rendu

---

Le projet doit être réalisé par groupe de 2 à 4 personnes. La note sera portée sur les éléments suivants :
- Les fonctionnalités sont présentes.
- Une documentation est présente pour l'installation du projet.
- Les bonnes pratiques de développement sont respectées :
  - Qualité du code
  - Commentaires 
- Des malus pourront être appliqués en cas de non-respect du nombre de personnes par groupe, de plagiat ou
de rendu en retard.
