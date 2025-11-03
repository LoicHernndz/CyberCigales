<?php
namespace Models\Instagram;

/**
 * Modèle pour les données Instagram
 * 
 * Ce modèle gère toutes les données statiques et dynamiques
 * pour les pages Instagram (stories, posts, profils, etc.)
 */
class InstagramModel
{
    /**
     * Récupère les données des stories
     * 
     * @return array Tableau des stories avec leurs informations
     */
    public function getStories(): array
    {
        return [
            [
                'username' => 'mel_133',
                'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
                'is_yours' => false,
                'profile_url' => '/instagram/melina',
                'is_unseen' => true
            ],
            [
                'username' => 'alex_photo',
                'avatar' => 'images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'anna_food',
                'avatar' => 'images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'annie_nature',
                'avatar' => 'images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'brooke_kitchen',
                'avatar' => 'images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg',
                'is_yours' => false
            ],
            [
                'username' => 'christiann_bake',
                'avatar' => 'images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg',
                'is_yours' => false
            ]
        ];
    }
    
    /**
     * Récupère les données des posts du feed
     * 
     * @return array Tableau des posts avec leurs informations
     */
    public function getPosts(): array
    {
        return [
            [
                'id' => 1,
                'username' => 'alex_photo',
                'avatar' => 'images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'location' => 'Paris, France',
                'image' => 'images/instagram/daniel-lincoln-IE2Z11zKsso-unsplash.jpg',
                'likes' => 1234,
                'caption' => 'Magnifique journée à Paris ! 🌟 #paris #photographie #coucherdesoleil',
                'comments' => [
                    [
                        'username' => 'marie_photo',
                        'text' => 'Tellement beau ! 😍'
                    ],
                    [
                        'username' => 'pierre_art',
                        'text' => 'Composition incroyable !'
                    ]
                ],
                'time' => 'Il y a 2 heures'
            ],
            [
                'id' => 2,
                'username' => 'anna_food',
                'avatar' => 'images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'location' => 'Restaurant Le Petit',
                'image' => 'images/instagram/nathan-dumlao-nBJHO6wmRWw-unsplash.jpg',
                'likes' => 892,
                'caption' => 'Le café parfait pour commencer la journée ☕️ #café #matin #modevie',
                'comments' => [
                    [
                        'username' => 'coffee_lover',
                        'text' => 'J\'ai envie d\'un café maintenant !'
                    ]
                ],
                'time' => 'Il y a 4 heures'
            ],
            [
                'id' => 3,
                'username' => 'annie_nature',
                'avatar' => 'images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'location' => 'Jardin Botanique',
                'image' => 'images/instagram/annie-spratt-eZk9w9RBHRo-unsplash.jpg',
                'likes' => 2156,
                'caption' => 'Moment de paix dans la nature 🌿 #nature #paix #vert #botanique',
                'comments' => [
                    [
                        'username' => 'nature_lover',
                        'text' => 'Tellement apaisant !'
                    ],
                    [
                        'username' => 'green_thumb',
                        'text' => 'J\'adore cette ambiance !'
                    ]
                ],
                'time' => 'Il y a 6 heures'
            ]
        ];
    }
    
    /**
     * Récupère les données du profil de Melina
     * 
     * @return array Données du profil
     */
    public function getMelinaProfile(): array
    {
        return [
            'username' => 'mel_133',
            'display_name' => 'Melina',
            'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
            'posts_count' => '3',
            'followers_count' => '89.2K',
            'following_count' => '1,156',
            'bio' => '✨ Fashion & Lifestyle ✨\n📸 Photographer\n🎨 Creative soul\n📍 Paris, France\n MARSEILLE 13\n\n#fashion #lifestyle #photography #paris #creative',
            'website' => 'cybercigales.fr',
            'verified' => true
        ];
    }
    
    /**
     * Récupère les posts du profil de Melina
     * 
     * @return array Posts du profil
     */
    public function getMelinaPosts(): array
    {
        return [
            [
                'id' => 1,
                'image' => '/images/instagram/tyler-delgado-A1kXxn2KVCM-unsplash 2.jpg',
                'type' => 'pinned',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ],
            [
                'id' => 2,
                'image' => '/images/instagram/steve-doig-FaMBWkmvPyY-unsplash.jpg',
                'type' => 'normal',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ],
            [
                'id' => 3,
                'image' => '/images/instagram/faux-profil-amie-hacke/melina_photo_miroir_2.png',
                'type' => 'normal',
                'is_video' => false,
                'likes' => rand(150, 2500),
                'comments' => rand(5, 50)
            ]
        ];
    }
    
    /**
     * Récupère les messages du chat avec Melina
     * 
     * @return array Messages du chat
     */
    public function getMelinaChatMessages(): array
    {
        return [
            [
                'type' => 'received',
                'content' => 'Salut ! Comment ça va ? 😊',
                'time' => '14:30'
            ],
            [
                'type' => 'sent',
                'content' => 'Salut Melina ! Ça va super, merci !',
                'time' => '14:32'
            ],
            [
                'type' => 'received',
                'content' => 'J\'ai vu tes nouvelles photos, elles sont magnifiques ! 📸',
                'time' => '14:35'
            ],
            [
                'type' => 'sent',
                'content' => 'Merci beaucoup ! J\'adore la photographie ✨',
                'time' => '14:37'
            ]
        ];
    }
    
}
