# Compte Rendu — TP 7 Symfony : Gestion des Produits

**Projet :** SIO-Shoes (E-commerce)  
**Étudiant :** Allan Decugis  

Dans ce TP, nous avons créé l'entité `Product` avec ses propriétés (`name`, `description`, `price`), ainsi qu'une contrainte d'unicité sur le nom (`UniqueEntity`). Nous avons ensuite établi une relation `ManyToMany` entre `Product` et `SubCategory`, puis exécuté les migrations Doctrine correspondantes.

Un CRUD complet a été mis en place et stylisé avec Bootstrap (notamment la limitation de l'affichage de la description avec le filtre Twig `slice(0, 100)` et l'affichage des sous-catégories associées).

Enfin, l'accès à la gestion des produits a été restreint aux utilisateurs ayant le rôle `ROLE_EDITOR` (avec héritage pour les administrateurs via `role_hierarchy`), un lien d'accès a été ajouté dans la barre de navigation, et des messages flash ont été configurés pour confirmer l'ajout, la modification et la suppression d'un produit.
