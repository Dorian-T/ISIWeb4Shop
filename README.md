# ISIWeb4Shop

## Contributeurs

- Jules Ginhac
- Dorian Tonnis


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
    - Le fichier `test.sql` contient des données de test
    - Les identifiants de connexion à la base de données doivent être renseignés dans le fichier `config.php`
- Installer Bootstrap avec la commande `npm install bootstrap`

### Compte déja présent

Il y a déja de créé un compte admin où l'identifiant est jginhac et le mot de passe est 12345.

Il existe aussi un compte utilisateur où l'identifiant est ginhac et le mot de passe est 1234.

Pour se connecter à un de ces comptes il faut aller sur la page de connexion.

Bien sûr vous avez aussi la possiblilté de créer soit un compte utilisateur ou soit un compte admin.

## Fonctionnalités client

### Page d'accueil

*La page d'accueil a été réalisée par Jules.*

Elle permet d'accéder aux différentes catégories de produits.

### Connexion

*La page de connexion a été réalisée par Jules et Dorian.*

Elle permet de se connecter à un compte client ou à un compte admin. Si on n'a pas encore de compte, on peut en créer un en cliquant sur le lien "Inscrivez-vous ici".

Une fois connecté, un lien de déconnexion apparaît à la place du lien de connexion dans le header. Notre nom d'utilisateur y apparaît également.

### Page des produits

*La page des produits a été réalisée par Jules.*

On peut voir tous les produits disponibles sur le site. On peut les filtrer par catégorie.

Sur chaque produit, il y a un bouton permettant d'ajouter une unité du produit au panier. Pour en ajouter plusieurs, il faut cliquer sur la photo du produit pour accéder à sa page de détail.

### Page de détail d'un produit

*La page de détail d'un produit a été réalisée par Jules et Dorian.*

Sur cette page, on peut voir toutes les informations relatives au produit. On peut également choisir combien d'unité du produit on veut ajouter au panier.

En bas de cette page, on peut voir les commentaires des utilisateurs sur le produit et en ajouter. Même si cette fonctionnalité n'était pas demandée, nous avons décidé de l'ajouter vu qu'elle était présente dans la base de données.

### Page du panier

*Le panier a été réalisé par Dorian.*

Sur cette page, on peut voir tous les produits que l'on a ajouté au panier. On peut supprimer chaque produit.

Nous avons fait le choix de diminuer du stock les produits ajoutés au panier pour éviter aux utilisateurs la frustration de mettre un produit dans le panier et de se rendre compte qu'il n'est plus disponible au moment de payer. Pour éviter que certains utilisateurs ne mettent trop de produits dans leur panier et ne les achètent pas, nous avons mis en place un système de réservation. Lorsqu'un panier est créé, il est réservé pour une journée si l'utilisateur n'est pas connecté et pour une semaine s'il est connecté. Si l'utilisateur ne paye pas sa commande dans ce délai, le panier est supprimé et les produits sont remis en stock.

### Commander

*Les pages liées à la commande ont été réalisé par Dorian et Jules.*

#### Adresse

Après avoir appuyé sur le bouton "Commander" dans la page panier, nous sommes redirigés vers une nouvelle page permettant de soit choisir son addresse liée au compte si l'utilisateur est connecté ou sinon de rentrer une nouvelle adresse de livraison. Pour les utilisateurs non connectés, ils peuvent seulement rentrer une nouvelle adresse. 

#### Paiement

Après avoir choisit l'adresse de livraison, nous passons maintenant au paiement. Il y a la possibilité entre 4 paiements différents. Le premier est Paypal, l'utilisateur est redirigé vers le site de Paypal. Le deuxième est Carte de crédit, l'utilisateur est redirigé vers une page où il doit entrer ces informations de carte bancaire. La troisième est chèque, l'utilisateur est redirigé vers une page ou il peut télécharger une facture de sa commande en pdf avec les informations liées à l'envoi du chèque. Et pour finir le dernier moyen de paiement est le virement bancaire. L'utilisateur est redirigé vers une page où il rentre son IBAN est son BIC. Une fois que l'utilisateur a payé, une page de remerciement apparait et il peur revenir à  l'acceuil.

## Fonctionnalités admin

### Connexion

*La page de connexion a été réalisée par Jules et Dorian.*

Pour accéder aux fonctionnalités admin, il suffit de se connecter avec un compte admin. Les liens vers vers les fonctionnaliés admin apparaissent alors dans le header.
Pour créer un compte admin, il faut accéder à la page : `index.php?action=admin&page=register`. Même si ce n'est pas très sécurisé, nous avons laissé cette page accessible à tous pour que le professeur puisse créer un compte admin s'il le souhaite.

### Page des commandes

*La page des commandes a été réalisée par Jules et Dorian.*

Sur cette page, on peut voir toutes les commandes passées sur le site. Un code couleur permet de voir rapidement le statut de la commande.

Les commandes étant de statut 2 (payées) peuvent être validées en appuyant sur le bouton vert ✅.

On peut également voir le détail de chaque commande en cliquant sur le bouton jaune 🔍. On peut alors voir chaque produit commandé et sa quantité, ainsi que l'adresse de livraison.

### Page des produits

*La page des produits a été réalisée par Jules et Dorian.*

Sur cette page, on peut voir tous les produits disponibles sur le site. On peut les modifier ou les supprimer.