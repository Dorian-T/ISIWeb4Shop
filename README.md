# ISIWeb4Shop


## TODO

- [x] delivery adress ne marche pas (il faut en ajouter une nouvelle et sélectionner celle du compte) (Dorian)
- [x] update le statut de la commande + id adress
- [ ] update le statut de la commande + type payement
- [ ] facture de chèque (Jules)
- [x] ajouter une page de détail de commande pour les admins (produits + adresse de livraison) (Dorian)
- [ ] fusion des paniers quand on se connecte en plein milieu d'une commande
- [ ] le README (Dorian + Jules)


## Installation

### Prérequis

- PHP
- Composer
- MySQL
- NodeJS

### Installation

- Cloner le projet
- Installer les dépendances avec `composer install`
- Créer une base de données :
    - Le fichier `DB.sql` crée la base de données et les tables
    - Le fichier `test.sql` insère des données de test
    - Les identifiants de connexion à la base de données doivent être renseignés dans le fichier `config.php`
- Installer Bootstrap avec la commande `npm install bootstrap`


## Fonctionnalités

...


Il y a une page admin à l'adresse index.php?action=admin&page=register