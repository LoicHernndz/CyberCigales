# Guide : Créer un nouvel utilisateur Instagram

Ce guide explique comment ajouter un nouvel utilisateur fictif à l'interface Instagram du projet CyberCigales.

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Structure des données](#structure-des-données)
3. [Ajouter un utilisateur en story](#ajouter-un-utilisateur-en-story)
4. [Ajouter un post au feed](#ajouter-un-post-au-feed)
5. [Ajouter une image](#ajouter-une-image)
6. [Checklist rapide](#checklist-rapide)

---

## Vue d'ensemble

Les utilisateurs Instagram sont gérés dans un seul fichier :

```
src/Models/Instagram/InstagramModel.php
```

Ce fichier contient plusieurs méthodes qui retournent des tableaux PHP :
- `getStories()` : utilisateurs qui apparaissent dans la barre de stories
- `getPosts()` : posts qui apparaissent dans le feed principal
- `getMelinaProfile()` : profil de Melina (personnage clé du scénario)
- `getMelinaPosts()` : posts sur le profil de Melina
- `getMelinaChatMessages()` : messages dans le chat avec Melina

---

## Structure des données

### Structure d'une Story

```php
[
    'username' => 'nom_utilisateur',           // Nom d'utilisateur (obligatoire)
    'avatar' => 'images/instagram/photo.jpg',  // Chemin vers l'avatar (obligatoire)
    'is_yours' => false,                       // true si c'est "votre" story
    'profile_url' => '/instagram/profil',      // URL vers le profil (optionnel)
    'is_unseen' => true                        // true = cercle coloré (non vue)
]
```

### Structure d'un Post

```php
[
    'id' => 1,                                              // ID unique (obligatoire)
    'username' => 'nom_utilisateur',                        // Nom d'utilisateur (obligatoire)
    'avatar' => 'images/instagram/avatar.jpg',              // Avatar de l'auteur (obligatoire)
    'location' => 'Paris, France',                          // Lieu (optionnel)
    'image' => 'images/instagram/post.jpg',                 // Image du post (obligatoire)
    'likes' => 1234,                                        // Nombre de likes
    'caption' => 'Texte du post #hashtag',                  // Légende avec hashtags
    'comments' => [                                         // Tableau de commentaires
        [
            'username' => 'commentateur',
            'text' => 'Super post !'
        ]
    ],
    'time' => 'Il y a 2 heures'                            // Temps relatif
]
```

---

## Ajouter un utilisateur en story

### Étape 1 : Préparer l'image avatar

1. Choisir ou créer une image carrée (recommandé : 150x150px minimum)
2. Placer l'image dans : `public/images/instagram/`
3. Utiliser un nom descriptif : `prenom-nom-description.jpg`

### Étape 2 : Ajouter la story

Ouvrir `src/Models/Instagram/InstagramModel.php` et ajouter un nouvel élément dans le tableau de `getStories()` :

```php
public function getStories(): array
{
    return [
        // ... stories existantes ...
        
        // NOUVEAU : Ajouter ici
        [
            'username' => 'nouveau_user',
            'avatar' => 'images/instagram/nouvelle-image.jpg',
            'is_yours' => false
        ]
    ];
}
```

---

## Ajouter un post au feed

### Étape 1 : Préparer les images

1. **Image du post** : format portrait ou carré (recommandé : 1080x1080px ou 1080x1350px)
2. **Avatar** : peut réutiliser un avatar existant ou en créer un nouveau
3. Placer les images dans : `public/images/instagram/`

### Étape 2 : Ajouter le post

Ouvrir `src/Models/Instagram/InstagramModel.php` et ajouter un nouvel élément dans le tableau de `getPosts()` :

```php
public function getPosts(): array
{
    return [
        // ... posts existants ...
        
        // NOUVEAU : Ajouter ici
        [
            'id' => 11,  // Incrémenter l'ID
            'username' => 'nouveau_user',
            'avatar' => 'images/instagram/avatar-nouveau.jpg',
            'location' => 'Lyon, France',
            'image' => 'images/instagram/nouveau-post.jpg',
            'likes' => 1500,
            'caption' => 'Ma nouvelle publication ! 🎉 #nouveaupost #exemple',
            'comments' => [
                [
                    'username' => 'ami_123',
                    'text' => 'Super !'
                ]
            ],
            'time' => 'Il y a 1 heure'
        ]
    ];
}
```

---

## Ajouter une image

### Sources d'images gratuites recommandées

- [Unsplash](https://unsplash.com) - Photos haute qualité, libres de droits
- [Pexels](https://pexels.com) - Photos et vidéos gratuites
- [Pixabay](https://pixabay.com) - Images libres de droits

### Convention de nommage

```
prenom-nom-description.jpg
```

Exemples :
- `marie-dupont-selfie-plage.jpg`
- `paul-martin-cafe-matin.jpg`

### Emplacement des fichiers

```
public/
└── images/
    └── instagram/
        ├── avatars/           # Pour les photos de profil
        ├── posts/             # Pour les images de posts
        └── faux-profil-amie-hacke/  # Images de Melina
```

> **Note** : Les images actuelles sont à la racine de `public/images/instagram/`. Vous pouvez garder cette structure ou organiser en sous-dossiers.

---

## Checklist rapide

### Ajouter une story

- [ ] Image avatar prête (carrée, min 150x150px)
- [ ] Image placée dans `public/images/instagram/`
- [ ] Nouvel élément ajouté dans `getStories()` de `InstagramModel.php`
- [ ] Champs obligatoires : `username`, `avatar`, `is_yours`

### Ajouter un post

- [ ] Image du post prête
- [ ] Avatar prêt (ou réutiliser un existant)
- [ ] Images placées dans `public/images/instagram/`
- [ ] Nouvel élément ajouté dans `getPosts()` de `InstagramModel.php`
- [ ] ID unique et incrémenté
- [ ] Champs obligatoires : `id`, `username`, `avatar`, `image`, `likes`, `caption`, `comments`, `time`

---

## Exemple complet

Voici un exemple complet d'ajout d'un nouvel utilisateur avec story et post :

### 1. Fichiers à créer/ajouter

```
public/images/instagram/
├── sarah-martin-avatar.jpg      # Avatar (utilisé pour story + post)
└── sarah-martin-voyage.jpg      # Image du post
```

### 2. Code à ajouter dans `InstagramModel.php`

```php
// Dans getStories()
[
    'username' => 'sarah_voyage',
    'avatar' => 'images/instagram/sarah-martin-avatar.jpg',
    'is_yours' => false,
    'profile_url' => '/instagram/user/sarah_voyage',  // Lien vers le profil
    'is_unseen' => true
]

// Dans getPosts()
[
    'id' => 12,
    'username' => 'sarah_voyage',
    'avatar' => 'images/instagram/sarah-martin-avatar.jpg',
    'location' => 'Barcelone, Espagne',
    'image' => 'images/instagram/sarah-martin-voyage.jpg',
    'likes' => 2341,
    'caption' => 'Vacances à Barcelone ! 🇪🇸☀️ #voyage #espagne #barcelone #vacances',
    'comments' => [
        [
            'username' => 'travel_fan',
            'text' => 'Profite bien ! 🌴'
        ],
        [
            'username' => 'bestie_forever',
            'text' => 'Tu me manques ! 😢'
        ]
    ],
    'time' => 'Il y a 3 heures'
]
```

---

## Créer un profil complet avec messagerie

Depuis la mise à jour, **tous les utilisateurs ont automatiquement un profil et une messagerie** grâce au système de routes dynamiques.

### Comment ça fonctionne ?

1. **Routes dynamiques** : Les URLs `/instagram/user/{username}` et `/instagram/user/{username}/chat` sont gérées automatiquement
2. **Données centralisées** : Tous les profils sont définis dans `getAllUserProfiles()` de `InstagramModel.php`

### Ajouter un nouvel utilisateur avec profil complet

#### Étape 1 : Ajouter à la liste des stories

Dans `getStories()` :

```php
[
    'username' => 'nouveau_user',
    'avatar' => 'images/instagram/nouvelle-image.jpg',
    'is_yours' => false,
    'profile_url' => '/instagram/user/nouveau_user',  // URL automatique !
    'is_unseen' => true
]
```

#### Étape 2 : Ajouter le profil complet

Dans `getAllUserProfiles()` :

```php
'nouveau_user' => [
    'username' => 'nouveau_user',
    'display_name' => 'Nouveau Utilisateur',
    'avatar' => '/images/instagram/nouvelle-image.jpg',
    'posts_count' => '42',
    'followers_count' => '5.2K',
    'following_count' => '321',
    'bio' => "📸 Ma bio ici\n📍 Ma ville\n✨ Ma passion",
    'website' => 'monsite.com',  // Laisser vide si pas de site
    'verified' => false,  // true pour l'icône de vérification
    'posts' => [
        ['id' => 1, 'image' => '/images/instagram/post1.jpg', 'type' => 'normal', 'is_video' => false],
        ['id' => 2, 'image' => '/images/instagram/post2.jpg', 'type' => 'pinned', 'is_video' => false],
    ]
]
```

#### Étape 3 : Ajouter les messages du chat (optionnel)

Dans `getUserChatMessages()`, ajouter une entrée dans le tableau `$chatMessages` :

```php
'nouveau_user' => [
    ['type' => 'received', 'content' => 'Salut ! 👋', 'time' => '10:00'],
    ['type' => 'sent', 'content' => 'Hello !', 'time' => '10:05'],
    ['type' => 'received', 'content' => 'Comment vas-tu ?', 'time' => '10:06'],
]
```

> **Note** : Si vous n'ajoutez pas de messages personnalisés, des messages par défaut seront utilisés.

### Structure des URLs

| URL | Description |
|-----|-------------|
| `/instagram` | Page d'accueil avec stories et feed |
| `/instagram/melina` | Profil de Melina (route spéciale) |
| `/instagram/melina/chat` | Chat avec Melina |
| `/instagram/user/{username}` | Profil d'un utilisateur |
| `/instagram/user/{username}/chat` | Chat avec un utilisateur |

---

## Questions fréquentes

### Les images ne s'affichent pas, pourquoi ?

Vérifiez :
1. Le chemin est correct (avec ou sans `/` au début)
2. L'extension du fichier correspond (.jpg, .png)
3. Le fichier existe bien dans `public/images/instagram/`

### Comment changer l'ordre des stories/posts ?

L'ordre d'affichage correspond à l'ordre dans le tableau PHP. Déplacez simplement l'élément à la position souhaitée.

---

*Document créé pour le projet CyberCigales - Dernière mise à jour : Janvier 2026*

