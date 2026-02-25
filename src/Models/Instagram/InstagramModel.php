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
                'profile_url' => '/instagram/user/mel_133',
                'is_unseen' => true
            ],
            [
                'username' => 'alex_photo',
                'avatar' => 'images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/alex_photo',
                'is_unseen' => true
            ],
            [
                'username' => 'anna_food',
                'avatar' => 'images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/anna_food',
                'is_unseen' => false
            ],
            [
                'username' => 'annie_nature',
                'avatar' => 'images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/annie_nature',
                'is_unseen' => true
            ],
            [
                'username' => 'brooke_kitchen',
                'avatar' => 'images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/brooke_kitchen',
                'is_unseen' => false
            ],
            [
                'username' => 'christiann_bake',
                'avatar' => 'images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/christiann_bake',
                'is_unseen' => true
            ],
            [
                'username' => 'leo_creative',
                'avatar' => 'images/instagram/leo_visions-n5ojSxRb1Vs-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/leo_creative',
                'is_unseen' => false
            ],
            [
                'username' => 'diliara_style',
                'avatar' => 'images/instagram/diliara-garifullina-I48gnI1Qs5o-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/diliara_style',
                'is_unseen' => true
            ],
            [
                'username' => 'corina_pets',
                'avatar' => 'images/instagram/corina-rainer-sScNrKruEPs-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/corina_pets',
                'is_unseen' => false
            ],
            [
                'username' => 'mike_coffee',
                'avatar' => 'images/instagram/mike-kenneally-TD4DBagg2wE-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/mike_coffee',
                'is_unseen' => true
            ],
            [
                'username' => 'heather_travel',
                'avatar' => 'images/instagram/heather-barnes-CNDiESvWfrk-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/heather_travel',
                'is_unseen' => false
            ],
            [
                'username' => 'monika_cuisine',
                'avatar' => 'images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg',
                'is_yours' => false,
                'profile_url' => '/instagram/user/monika_cuisine',
                'is_unseen' => true
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
        // On s'assure que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $posts = [
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
            ],
            [
                'id' => 4,
                'username' => 'leo_creative',
                'avatar' => 'images/instagram/leo_visions-n5ojSxRb1Vs-unsplash.jpg',
                'location' => 'Studio Créatif',
                'image' => 'images/instagram/leo_visions-XAsr6c40MJM-unsplash.jpg',
                'likes' => 3421,
                'caption' => 'La créativité n\'a pas de limites 🎨 #art #creative #studio #inspiration',
                'comments' => [
                    [
                        'username' => 'art_lover',
                        'text' => 'Superbe travail !'
                    ],
                    [
                        'username' => 'design_pro',
                        'text' => 'Quelle technique ! 🔥'
                    ]
                ],
                'time' => 'Il y a 8 heures'
            ],
            [
                'id' => 5,
                'username' => 'corina_pets',
                'avatar' => 'images/instagram/corina-rainer-sScNrKruEPs-unsplash.jpg',
                'location' => 'Maison',
                'image' => 'images/instagram/clifford-VobvKmG-StA-unsplash 2.jpg',
                'likes' => 4567,
                'caption' => 'Mon meilleur ami 🐕 #dog #cute #bestfriend #animals',
                'comments' => [
                    [
                        'username' => 'dog_parent',
                        'text' => 'Trop mignon ! 😍'
                    ],
                    [
                        'username' => 'animal_lover',
                        'text' => 'Adorable !'
                    ],
                    [
                        'username' => 'puppy_fan',
                        'text' => 'Il est magnifique !'
                    ]
                ],
                'time' => 'Il y a 10 heures'
            ],
            [
                'id' => 6,
                'username' => 'mike_coffee',
                'avatar' => 'images/instagram/mike-kenneally-TD4DBagg2wE-unsplash.jpg',
                'location' => 'Coffee Shop',
                'image' => 'images/instagram/shelley-pauls-I58f47LRQYM-unsplash.jpg',
                'likes' => 1876,
                'caption' => 'Premier café du matin ☕ Le rituel sacré ! #coffee #morning #routine',
                'comments' => [
                    [
                        'username' => 'caffeine_addict',
                        'text' => 'Pareil ici ! ☕'
                    ]
                ],
                'time' => 'Il y a 12 heures'
            ],
            [
                'id' => 7,
                'username' => 'heather_travel',
                'avatar' => 'images/instagram/heather-barnes-CNDiESvWfrk-unsplash.jpg',
                'location' => 'Montagnes',
                'image' => 'images/instagram/janosch-diggelmann-8xLel9jx3fE-unsplash.jpg',
                'likes' => 5234,
                'caption' => 'Vue imprenable 🏔️ Rien de tel que la montagne ! #travel #mountains #nature #adventure',
                'comments' => [
                    [
                        'username' => 'wanderlust',
                        'text' => 'C\'est où ? C\'est magnifique !'
                    ],
                    [
                        'username' => 'hiker_life',
                        'text' => 'J\'ai besoin de ça dans ma vie !'
                    ]
                ],
                'time' => 'Il y a 14 heures'
            ],
            [
                'id' => 8,
                'username' => 'monika_cuisine',
                'avatar' => 'images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg',
                'location' => 'Ma Cuisine',
                'image' => 'images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg',
                'likes' => 2890,
                'caption' => 'Repas fait maison 🍽️ La cuisine c\'est l\'amour ! #homemade #food #cooking #yummy',
                'comments' => [
                    [
                        'username' => 'foodie_paris',
                        'text' => 'Recette svp ! 🙏'
                    ],
                    [
                        'username' => 'chef_amateur',
                        'text' => 'Ça a l\'air délicieux !'
                    ]
                ],
                'time' => 'Il y a 16 heures'
            ],
            [
                'id' => 9,
                'username' => 'diliara_style',
                'avatar' => 'images/instagram/diliara-garifullina-I48gnI1Qs5o-unsplash.jpg',
                'location' => 'Centre-ville',
                'image' => 'images/instagram/cristina-anne-costello-4jsmBl30x_A-unsplash.jpg',
                'likes' => 3156,
                'caption' => 'Style du jour 💫 #fashion #style #ootd #streetstyle',
                'comments' => [
                    [
                        'username' => 'fashion_week',
                        'text' => 'J\'adore ton style !'
                    ],
                    [
                        'username' => 'style_icon',
                        'text' => 'Où as-tu acheté ça ? 😍'
                    ]
                ],
                'time' => 'Il y a 18 heures'
            ],
            [
                'id' => 10,
                'username' => 'brooke_kitchen',
                'avatar' => 'images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg',
                'location' => 'Petit-déjeuner',
                'image' => 'images/instagram/aliona-gumeniuk-jeAjT87nbjM-unsplash.jpg',
                'likes' => 1543,
                'caption' => 'Brunch du dimanche 🥐 Le meilleur moment de la semaine ! #brunch #sunday #foodie',
                'comments' => [
                    [
                        'username' => 'brunch_lover',
                        'text' => 'J\'ai tellement faim maintenant !'
                    ]
                ],
                'time' => 'Hier'
            ]
        ];

        // On insère le post secret de Melina s'il a été débloqué via le journal
        if (isset($_SESSION['instagram_mel_post_unlocked']) && $_SESSION['instagram_mel_post_unlocked'] === true) {
            array_unshift($posts, [
                'id' => 999,
                'username' => 'mel_133',
                'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
                'location' => 'Inconnu',
                'image' => '/images/instagram/faux-profil-amie-hacke/melina_photo_miroir.png', // Fallback or thumbnail if needed, but video will play
                'is_video' => true,
                'video' => '/images/videos/instagram/hacker-terminal-promo.mov',
                'likes' => 842,
                'caption' => '🚨 NOUVELLE OPPORTUNITÉ CRYPTO ! 🚨 Rejoignez ma plateforme SECURISEE pour faire exploser vos profits. 📈 Ne laissez pas les banques contrôler votre argent ! Lien en bio.💸💎 #crypto #investissement #libertefinanciere #hackthesystem',
                'comments' => [],
                'time' => 'À l\'instant'
            ]);
        }

        return $posts;
    }

    /**
     * Base de données de tous les profils utilisateurs
     * 
     * @return array Tableau associatif des profils par username
     */
    public function getAllUserProfiles(): array
    {
        return [
            'alex_photo' => [
                'username' => 'alex_photo',
                'display_name' => 'Alexandre Schmidt',
                'avatar' => '/images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg',
                'posts_count' => '47',
                'followers_count' => '12.4K',
                'following_count' => '892',
                'bio' => "📸 Photographe passionné\n🌆 Urbex & Street Photography\n📍 Paris / Lyon\n✨ Capturer l'instant parfait",
                'website' => '',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/daniel-lincoln-IE2Z11zKsso-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/emre-NZMeJsrMC8U-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/folco-masi-erUcICg2LYE-unsplash 2.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'anna_food' => [
                'username' => 'anna_food',
                'display_name' => 'Anna 🍴',
                'avatar' => '/images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg',
                'posts_count' => '156',
                'followers_count' => '34.7K',
                'following_count' => '1,203',
                'bio' => "🍳 Food Blogger\n☕ Coffee addict\n🥐 Brunch lover\n📍 Paris\n📧 contact@annafood.fr",
                'website' => 'annafood.fr',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/nathan-dumlao-nBJHO6wmRWw-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/aliona-gumeniuk-jeAjT87nbjM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'annie_nature' => [
                'username' => 'annie_nature',
                'display_name' => 'Annie 🌿',
                'avatar' => '/images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg',
                'posts_count' => '89',
                'followers_count' => '28.1K',
                'following_count' => '567',
                'bio' => "🌱 Nature & Botanique\n🌸 Amante des jardins\n📸 Photo nature\n🌍 Éco-responsable\n🌻 La nature guérit tout",
                'website' => '',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/annie-spratt-eZk9w9RBHRo-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/nature-uninterrupted-photography-v-3NQ3pmWkY-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'brooke_kitchen' => [
                'username' => 'brooke_kitchen',
                'display_name' => 'Brooke | Chef à domicile',
                'avatar' => '/images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg',
                'posts_count' => '234',
                'followers_count' => '67.3K',
                'following_count' => '432',
                'bio' => "👩‍🍳 Chef à domicile\n🍽️ Recettes healthy\n📖 Nouveau livre disponible !\n🎬 YouTube: BrookeKitchen\n📍 Marseille",
                'website' => 'brookekitchen.com',
                'verified' => true,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/aliona-gumeniuk-jeAjT87nbjM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'christiann_bake' => [
                'username' => 'christiann_bake',
                'display_name' => 'Christian 🧁',
                'avatar' => '/images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg',
                'posts_count' => '178',
                'followers_count' => '45.2K',
                'following_count' => '789',
                'bio' => "🎂 Pâtissier amateur\n🍰 Desserts & Gâteaux\n📸 Food photography\n🇫🇷 Made in France\n✨ Sucre & Passion",
                'website' => '',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/christiann-koepke-AigxB1zfRVo-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/aliona-gumeniuk-jeAjT87nbjM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/shelley-pauls-I58f47LRQYM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'leo_creative' => [
                'username' => 'leo_creative',
                'display_name' => 'Léo Vision 🎨',
                'avatar' => '/images/instagram/leo_visions-n5ojSxRb1Vs-unsplash.jpg',
                'posts_count' => '312',
                'followers_count' => '156K',
                'following_count' => '234',
                'bio' => "🎨 Artiste digital\n✏️ Illustrateur freelance\n🖼️ NFT Creator\n💼 Dispo pour commissions\n📧 leo@creative.art",
                'website' => 'leocreative.art',
                'verified' => true,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/leo_visions-XAsr6c40MJM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/leo_visions-n5ojSxRb1Vs-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/cristina-anne-costello-4jsmBl30x_A-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'diliara_style' => [
                'username' => 'diliara_style',
                'display_name' => 'Diliara ✨',
                'avatar' => '/images/instagram/diliara-garifullina-I48gnI1Qs5o-unsplash.jpg',
                'posts_count' => '421',
                'followers_count' => '89.5K',
                'following_count' => '1,567',
                'bio' => "👗 Fashion & Lifestyle\n💄 Beauty tips\n🛍️ Shopping addict\n📍 Paris / Milan\n💌 Collab: diliara@style.com",
                'website' => 'diliarastyle.com',
                'verified' => true,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/cristina-anne-costello-4jsmBl30x_A-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/diliara-garifullina-I48gnI1Qs5o-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/heather-barnes-CNDiESvWfrk-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'corina_pets' => [
                'username' => 'corina_pets',
                'display_name' => 'Corina 🐾',
                'avatar' => '/images/instagram/corina-rainer-sScNrKruEPs-unsplash.jpg',
                'posts_count' => '267',
                'followers_count' => '112K',
                'following_count' => '890',
                'bio' => "🐕 Dog mom x3\n🐱 Cat lady\n🏥 Vétérinaire\n📸 Pet photography\n🐾 Adoptez, n'achetez pas !",
                'website' => '',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/clifford-VobvKmG-StA-unsplash 2.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/corina-rainer-sScNrKruEPs-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/heidi-the-sheltie-looking-out-window.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'mike_coffee' => [
                'username' => 'mike_coffee',
                'display_name' => 'Mike ☕',
                'avatar' => '/images/instagram/mike-kenneally-TD4DBagg2wE-unsplash.jpg',
                'posts_count' => '89',
                'followers_count' => '23.4K',
                'following_count' => '456',
                'bio' => "☕ Barista certifié\n🫘 Coffee roaster\n📍 Coffee shops Paris\n🎯 Latte art champion 2024\n☕ La vie commence après le café",
                'website' => 'mikecoffee.fr',
                'verified' => false,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/shelley-pauls-I58f47LRQYM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/nathan-dumlao-nBJHO6wmRWw-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/mike-kenneally-TD4DBagg2wE-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'heather_travel' => [
                'username' => 'heather_travel',
                'display_name' => 'Heather 🌍',
                'avatar' => '/images/instagram/heather-barnes-CNDiESvWfrk-unsplash.jpg',
                'posts_count' => '534',
                'followers_count' => '245K',
                'following_count' => '1,234',
                'bio' => "✈️ 45 pays visités\n🗺️ Travel blogger\n📸 Photographe voyage\n🎒 Digital nomad\n🌏 Prochaine destination: Japon 🇯🇵",
                'website' => 'heathertravel.com',
                'verified' => true,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/janosch-diggelmann-8xLel9jx3fE-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/daniel-lincoln-IE2Z11zKsso-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/emre-NZMeJsrMC8U-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'monika_cuisine' => [
                'username' => 'monika_cuisine',
                'display_name' => 'Monika 🍽️',
                'avatar' => '/images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg',
                'posts_count' => '198',
                'followers_count' => '56.8K',
                'following_count' => '678',
                'bio' => "👩‍🍳 Cuisine du monde\n🥗 Healthy & Gourmand\n📺 Émission sur M6\n📖 Mon livre en librairie\n💌 Pro: monika@cuisine.fr",
                'website' => 'monikacuisine.fr',
                'verified' => true,
                'posts' => [
                    ['id' => 1, 'image' => '/images/instagram/brooke-lark-qdyBKWSzpSI-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 2, 'image' => '/images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                    ['id' => 3, 'image' => '/images/instagram/aliona-gumeniuk-jeAjT87nbjM-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                ]
            ],
            'mel_133' => [
                'username' => 'mel_133',
                'display_name' => 'Melina',
                'avatar' => '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png',
                'posts_count' => (isset($_SESSION['instagram_mel_post_unlocked']) && $_SESSION['instagram_mel_post_unlocked']) ? '4' : '3',
                'followers_count' => '89.2K',
                'following_count' => '1,156',
                'bio' => "✨ Fashion & Lifestyle ✨\n📸 Photographer\n🎨 Creative soul\n📍 Paris, France\nMARSEILLE 13\n\n#fashion #lifestyle #photography #paris #creative",
                'website' => 'cybercigales.fr',
                'verified' => true,
                'posts' => (function () {
                    $basePosts = [
                        ['id' => 1, 'image' => '/images/instagram/Cesar.png', 'type' => 'normal', 'is_video' => false],
                        ['id' => 3, 'image' => '/images/instagram/steve-doig-FaMBWkmvPyY-unsplash.jpg', 'type' => 'normal', 'is_video' => false],
                        ['id' => 4, 'image' => '/images/instagram/faux-profil-amie-hacke/melina_photo_miroir_2.png', 'type' => 'normal', 'is_video' => false],
                    ];
                    if (isset($_SESSION['instagram_mel_post_unlocked']) && $_SESSION['instagram_mel_post_unlocked']) {
                        // On insère le post vidéo piraté APRÈS César mais AVANT le miroir 2
                        array_splice($basePosts, 1, 0, [
                            [
                                'id' => 2,
                                'video' => '/images/videos/instagram/Hacker-promo-plateforme-crypto.mp4',
                                'type' => 'mp4',
                                'is_video' => true
                            ]
                        ]);
                        // Et au-dessus, le "nouveau" post de la vidéo de promo
                        array_unshift($basePosts, [
                            'id' => 999,
                            'image' => '/images/instagram/faux-profil-amie-hacke/melina_photo_miroir.png',
                            'type' => 'mov',
                            'is_video' => true,
                            'video' => '/images/videos/instagram/hacker-terminal-promo.mov'
                        ]);
                    }
                    return $basePosts;
                })()
            ],
        ];
    }

    /**
     * Récupère le profil d'un utilisateur par son username
     * 
     * @param string $username Le nom d'utilisateur
     * @return array|null Données du profil ou null si non trouvé
     */
    public function getUserProfile(string $username): ?array
    {
        $profiles = $this->getAllUserProfiles();
        return $profiles[$username] ?? null;
    }

    /**
     * Messages de chat génériques par utilisateur
     * 
     * @param string $username Le nom d'utilisateur
     * @return array Messages du chat
     */
    public function getUserChatMessages(string $username): array
    {
        $chatMessages = [
            'alex_photo' => [
                ['type' => 'received', 'content' => 'Hey ! Tu as vu ma dernière photo ? 📸', 'time' => '10:15'],
                ['type' => 'sent', 'content' => 'Oui superbe ! C\'est où ?', 'time' => '10:18'],
                ['type' => 'received', 'content' => 'À Paris, près du Sacré-Cœur au lever du soleil', 'time' => '10:20'],
                ['type' => 'received', 'content' => 'Il faut qu\'on fasse un shooting ensemble un jour !', 'time' => '10:21'],
            ],
            'anna_food' => [
                ['type' => 'received', 'content' => 'Coucou ! J\'ai trouvé un super resto 🍴', 'time' => '12:30'],
                ['type' => 'sent', 'content' => 'Ah cool ! C\'est où ?', 'time' => '12:35'],
                ['type' => 'received', 'content' => 'Dans le 11ème, cuisine fusion asiatique', 'time' => '12:36'],
                ['type' => 'received', 'content' => 'On y va ce weekend ? 😊', 'time' => '12:37'],
            ],
            'annie_nature' => [
                ['type' => 'received', 'content' => 'Salut ! Tu connais le jardin des plantes ? 🌿', 'time' => '09:00'],
                ['type' => 'sent', 'content' => 'Oui j\'adore cet endroit !', 'time' => '09:15'],
                ['type' => 'received', 'content' => 'J\'y vais demain matin pour prendre des photos', 'time' => '09:16'],
                ['type' => 'received', 'content' => 'Tu veux venir ? Les orchidées sont en fleurs 🌸', 'time' => '09:17'],
            ],
            'brooke_kitchen' => [
                ['type' => 'received', 'content' => 'Hello ! Tu as essayé ma recette ? 👩‍🍳', 'time' => '18:00'],
                ['type' => 'sent', 'content' => 'Pas encore mais c\'est prévu !', 'time' => '18:10'],
                ['type' => 'received', 'content' => 'Dis-moi ce que tu en penses', 'time' => '18:11'],
                ['type' => 'received', 'content' => 'J\'ai une nouvelle recette qui arrive demain 🍽️', 'time' => '18:12'],
            ],
            'christiann_bake' => [
                ['type' => 'received', 'content' => 'Salut ! Tu aimes les macarons ? 🧁', 'time' => '15:30'],
                ['type' => 'sent', 'content' => 'Oh oui j\'adore !', 'time' => '15:35'],
                ['type' => 'received', 'content' => 'Je viens d\'en faire à la pistache', 'time' => '15:36'],
                ['type' => 'received', 'content' => 'Je t\'en garde quelques-uns ? 💚', 'time' => '15:37'],
            ],
            'leo_creative' => [
                ['type' => 'received', 'content' => 'Hey ! Je travaille sur un nouveau projet 🎨', 'time' => '20:00'],
                ['type' => 'sent', 'content' => 'Trop cool ! C\'est quoi ?', 'time' => '20:05'],
                ['type' => 'received', 'content' => 'Une série d\'illustrations sur les villes françaises', 'time' => '20:06'],
                ['type' => 'received', 'content' => 'Je te montre un preview ? ✨', 'time' => '20:07'],
            ],
            'diliara_style' => [
                ['type' => 'received', 'content' => 'Coucou ! Les soldes ont commencé 🛍️', 'time' => '11:00'],
                ['type' => 'sent', 'content' => 'Ah oui ? Des bons plans ?', 'time' => '11:10'],
                ['type' => 'received', 'content' => 'Oui plein ! Je te partage mes trouvailles', 'time' => '11:11'],
                ['type' => 'received', 'content' => 'On fait du shopping ensemble samedi ? 💃', 'time' => '11:12'],
            ],
            'corina_pets' => [
                ['type' => 'received', 'content' => 'Regarde mon nouveau chiot ! 🐕', 'time' => '16:00'],
                ['type' => 'sent', 'content' => 'Omg trop mignon !! 😍', 'time' => '16:02'],
                ['type' => 'received', 'content' => 'Il s\'appelle Caramel', 'time' => '16:03'],
                ['type' => 'received', 'content' => 'Tu veux venir le voir ce weekend ? 🐾', 'time' => '16:04'],
            ],
            'mike_coffee' => [
                ['type' => 'received', 'content' => 'Salut ! Tu connais le nouveau coffee shop ? ☕', 'time' => '08:30'],
                ['type' => 'sent', 'content' => 'Non, c\'est lequel ?', 'time' => '08:35'],
                ['type' => 'received', 'content' => 'Café Lumière, dans le Marais', 'time' => '08:36'],
                ['type' => 'received', 'content' => 'Leur latte art est incroyable ! On y va ? ☕✨', 'time' => '08:37'],
            ],
            'heather_travel' => [
                ['type' => 'received', 'content' => 'Hey ! Je pars au Japon le mois prochain ✈️', 'time' => '19:00'],
                ['type' => 'sent', 'content' => 'Wow génial ! Tu y vas pour combien de temps ?', 'time' => '19:05'],
                ['type' => 'received', 'content' => '3 semaines ! Tokyo, Kyoto, Osaka', 'time' => '19:06'],
                ['type' => 'received', 'content' => 'Tu as des recommandations ? 🇯🇵', 'time' => '19:07'],
            ],
            'monika_cuisine' => [
                ['type' => 'received', 'content' => 'Coucou ! Mon nouveau livre sort bientôt 📖', 'time' => '14:00'],
                ['type' => 'sent', 'content' => 'Super ! C\'est quoi le thème ?', 'time' => '14:10'],
                ['type' => 'received', 'content' => 'Cuisine méditerranéenne revisitée', 'time' => '14:11'],
                ['type' => 'received', 'content' => 'Je t\'envoie un exemplaire dédicacé ? 💝', 'time' => '14:12'],
            ],
            'mel_133' => [
                ['type' => 'received', 'content' => 'Salut ! Comment ça va ? 😊', 'time' => '14:30'],
                ['type' => 'sent', 'content' => 'Salut Melina ! Ça va super, merci !', 'time' => '14:32'],
                ['type' => 'received', 'content' => 'J\'ai vu tes nouvelles photos, elles sont magnifiques ! 📸', 'time' => '14:35'],
                ['type' => 'sent', 'content' => 'Merci beaucoup ! J\'adore la photographie ✨', 'time' => '14:37'],
            ],
        ];

        return $chatMessages[$username] ?? [
            ['type' => 'received', 'content' => 'Salut ! 👋', 'time' => '12:00'],
            ['type' => 'sent', 'content' => 'Hey ! Ça va ?', 'time' => '12:05'],
            ['type' => 'received', 'content' => 'Oui super et toi ?', 'time' => '12:06'],
        ];
    }

}
