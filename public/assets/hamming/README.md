# JavaScript pour le Carré de Hamming

## Structure des fichiers

Le code JavaScript est organisé en modules ES6 selon les conventions du cours :

- **`Hamming.js`** : Classe utilitaire pour les calculs du carré de Hamming
  - `generateSquare(A_dataBits)` : Génère un carré valide à partir de 4 bits de données
  - `generateRandomSquare()` : Génère un carré aléatoire
  - `generateSquareWithError(A_square)` : Génère un carré avec une erreur
  - `checkErrorPosition(...)` : Vérifie si une position correspond à l'erreur
  - `findErrorPosition(...)` : Trouve la position de l'erreur

- **`HammingGame.js`** : Classe principale pour gérer le jeu
  - Gère l'affichage du carré
  - Gère les interactions utilisateur (clic, clavier)
  - Envoie les requêtes AJAX via Fetch
  - Met à jour le DOM avec les résultats
  - Gère l'accessibilité avec ARIA

- **`main.js`** : Point d'entrée qui initialise le jeu

## Conventions respectées

### Nommage des variables
- `S_` : String (chaîne de caractères)
- `I_` : Integer (nombre entier)
- `B_` : Boolean (booléen)
- `A_` : Array (tableau)
- `O_` : Object (objet)
- `F_` : Function (fonction)

### Techniques utilisées

1. **Classes ES6** : Utilisation de `class` et `export`
2. **Modules ES6** : `import` / `export` pour organiser le code
3. **Fetch API** : Pour les requêtes AJAX asynchrones
4. **Promesses** : Utilisation de `.then()` et `.catch()`
5. **ARIA** : Attributs pour l'accessibilité :
   - `role="button"` sur les cellules
   - `aria-label` pour décrire les éléments
   - `aria-live="polite"` pour les mises à jour asynchrones
   - `aria-disabled` pour désactiver les éléments
   - `tabindex` pour la navigation au clavier
6. **Manipulation du DOM** : Utilisation de `getElementById`, `querySelectorAll`, etc.
7. **Event Listeners** : `addEventListener` pour les événements clic et clavier

## Utilisation

Le script est chargé automatiquement dans la vue PHP via :
```html
<script type="module" src="/assets/hamming/js/main.js"></script>
```

Le JavaScript s'initialise automatiquement au chargement de la page et :
1. Attache les event listeners aux cellules du carré
2. Gère les clics et la navigation au clavier
3. Envoie les réponses au serveur via Fetch
4. Met à jour le DOM avec les résultats

## Accessibilité

Le code respecte les standards d'accessibilité :
- Navigation au clavier (Tab, Enter, Espace)
- Attributs ARIA pour les lecteurs d'écran
- Zones "live" pour les mises à jour asynchrones
- Labels descriptifs pour tous les éléments interactifs


