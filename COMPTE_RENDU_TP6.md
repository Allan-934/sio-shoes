# Compte Rendu — TP 6 Symfony : Gestion des Sous-catégories

**Projet :** SIO-Shoes (E-commerce)  
**Étudiant :** Allan Decugis  

Dans ce TP, nous avons créé l'entité `SubCategory` reliée en relation `ManyToOne` avec `Category`, puis exécuté la migration Doctrine pour mettre à jour la base de données.

Un CRUD complet a ensuite été généré et mis en forme avec Bootstrap, en configurant la sélection de la catégorie parente via un `EntityType`.

Enfin, l'accès à la gestion des sous-catégories a été restreint aux utilisateurs possédant le rôle `ROLE_ADMIN`, intégré dans le menu déroulant de la barre de navigation, et complété par des messages flash pour confirmer l'ajout, la modification et la suppression.
