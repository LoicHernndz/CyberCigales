# Énigme : L'Accès Caché (Web Inspect)

Ce module ajoute une énigme de type "CTF" (Capture The Flag) utilisant les outils de développement du navigateur.

## Déroulement de l'énigme

1. **Phase d'observation** : L'utilisateur doit inspecter le code source de la page d'accueil du faux navigateur (Safari).
2. **Phase de navigation** : Un commentaire HTML révèle l'existence d'un sous-domaine de développement : `dev.cybercigales.fr`.
3. **Phase de manipulation (Inspecter l'élément)** : Sur la page de maintenance, un bouton d'administration est désactivé (`disabled`). L'utilisateur doit modifier le DOM pour supprimer cet attribut et activer le bouton.
4. **Phase finale (Console)** : Le clic sur le bouton déclenche un log dans la console (F12) contenant le mot de passe secret pour le terminal Bash.

## Fichiers modifiés

- `src/Models/InterfaceWeb/InterfaceWebModel.php` : Ajout des nouvelles pages simulées et des indices.
- `src/Controllers/Bash/BashExec.php` : Contrôleur pour l'exécution des commandes (préparation pour la suite).
- `src/config/Routes.php` : Activation de la route `/bash/exec`.

