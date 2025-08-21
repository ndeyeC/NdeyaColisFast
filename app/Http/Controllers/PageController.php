<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function livraisonExpress()
    {
        $data = [
            'title' => 'Livraison Express - colisFast',
            'description' => 'Service de livraison express en moins de 2h à Dakar et banlieue',
            'features' => [
                'Livraison en moins de 2 heures',
                'Suivi en temps réel',
                'Notification SMS et email',
                'Paiement sécurisé',
                'Service client 24/7'
            ],
            'pricing' => [
                ['zone' => 'Dakar Centre', 'price' => '2000', 'time' => '30-60 min'],
                ['zone' => 'Banlieue proche', 'price' => '2500', 'time' => '60-90 min'],
                ['zone' => 'Banlieue éloignée', 'price' => '3000', 'time' => '90-120 min'],
            ]
        ];
        
        return view('pages.services.livraison-express', compact('data'));
    }

    public function livraisonProgrammee()
    {
        $data = [
            'title' => 'Livraison Programmée - colisFast',
            'description' => 'Planifiez vos livraisons selon vos besoins',
            'features' => [
                'Programmation jusqu\'à 30 jours à l\'avance',
                'Créneaux horaires flexibles',
                'Confirmation 24h avant',
                'Modification possible jusqu\'à 2h avant',
                'Tarifs préférentiels'
            ],
            'timeSlots' => [
                ['slot' => '8h00 - 10h00', 'supplement' => '0 FCFA'],
                ['slot' => '10h00 - 12h00', 'supplement' => '0 FCFA'],
                ['slot' => '14h00 - 16h00', 'supplement' => '0 FCFA'],
                ['slot' => '16h00 - 18h00', 'supplement' => '0 FCFA'],
                ['slot' => '18h00 - 20h00', 'supplement' => '500 FCFA'],
            ]
        ];
        
        return view('pages.services.livraison-programmee', compact('data'));
    }

    public function solutionsEcommerce()
    {
        $data = [
            'title' => 'Solutions E-commerce - colisFast',
            'description' => 'Solutions complètes pour votre boutique en ligne',
            'solutions' => [
                [
                    'name' => 'Pack Starter',
                    'price' => '15000',
                    'features' => [
                        'Jusqu\'à 50 livraisons/mois',
                        'API de base',
                        'Tableau de bord simple',
                        'Support email'
                    ]
                ],
                [
                    'name' => 'Pack Business',
                    'price' => '35000',
                    'features' => [
                        'Jusqu\'à 200 livraisons/mois',
                        'API complète',
                        'Tableau de bord avancé',
                        'Support prioritaire',
                        'Étiquettes personnalisées'
                    ]
                ],
                [
                    'name' => 'Pack Enterprise',
                    'price' => 'Sur mesure',
                    'features' => [
                        'Livraisons illimitées',
                        'API personnalisée',
                        'Intégration sur mesure',
                        'Support dédié',
                        'SLA garanti'
                    ]
                ]
            ]
        ];
        
        return view('pages.services.solutions-ecommerce', compact('data'));
    }

    public function apropos()
    {
        $data = [
            'title' => 'À propos - colisFast',
            'story' => [
                'founding' => 'Fondée en 2023, colisFast est née de la volonté de révolutionner la livraison au Sénégal.',
                'mission' => 'Notre mission : rendre la livraison accessible, rapide et fiable pour tous les Sénégalais.',
                'vision' => 'Devenir la référence de la livraison en Afrique de l\'Ouest d\'ici 2030.'
            ],
            'values' => [
                'Rapidité' => 'Nous nous engageons à livrer dans les délais promis',
                'Fiabilité' => 'Vos colis arrivent toujours à destination',
                'Innovation' => 'Nous utilisons les dernières technologies',
                'Proximité' => 'Nous connaissons le terrain sénégalais'
            ],
            'stats' => [
                ['number' => '10,000+', 'label' => 'Colis livrés'],
                ['number' => '500+', 'label' => 'Clients satisfaits'],
                ['number' => '15', 'label' => 'Zones couvertes'],
                ['number' => '99%', 'label' => 'Taux de réussite']
            ]
        ];
        
        return view('pages.apropos', compact('data'));
    }

    public function carrieres()
    {
        $data = [
            'title' => 'Carrières - colisFast',
            'description' => 'Rejoignez l\'équipe colisFast et participez à la révolution de la livraison au Sénégal',
            'positions' => [
                [
                    'title' => 'Chauffeur-Livreur',
                    'type' => 'CDI',
                    'location' => 'Dakar',
                    'description' => 'Effectuer les livraisons dans le respect des délais et de la qualité de service.'
                ],
                [
                    'title' => 'Développeur Full Stack',
                    'type' => 'CDI',
                    'location' => 'Dakar',
                    'description' => 'Développer et maintenir notre plateforme web et mobile.'
                ],
                [
                    'title' => 'Responsable Logistique',
                    'type' => 'CDI',
                    'location' => 'Dakar',
                    'description' => 'Optimiser nos processus de livraison et gérer notre réseau.'
                ]
            ],
            'benefits' => [
                'Salaire compétitif',
                'Assurance santé',
                'Formation continue',
                'Environnement dynamique',
                'Possibilité d\'évolution'
            ]
        ];
        
        return view('pages.carrieres', compact('data'));
    }

    public function blog()
    {
        $data = [
            'title' => 'Blog - colisFast',
            'articles' => [
                [
                    'title' => 'L\'évolution de la livraison au Sénégal',
                    'excerpt' => 'Comment le secteur de la livraison évolue dans notre pays...',
                    'date' => '2025-01-15',
                    'author' => 'Équipe colisFast',
                    'slug' => 'evolution-livraison-senegal'
                ],
                [
                    'title' => '5 conseils pour emballer vos colis',
                    'excerpt' => 'Découvrez nos conseils pour bien emballer vos colis...',
                    'date' => '2025-01-10',
                    'author' => 'Équipe colisFast',
                    'slug' => 'conseils-emballer-colis'
                ],
                [
                    'title' => 'Notre expansion à Thiès',
                    'excerpt' => 'Nous sommes fiers d\'annoncer notre arrivée à Thiès...',
                    'date' => '2025-01-05',
                    'author' => 'Direction colisFast',
                    'slug' => 'expansion-thies'
                ]
            ]
        ];
        
        return view('pages.blog', compact('data'));
    }

    public function conditions()
    {
        $data = [
            'title' => 'Conditions d\'utilisation - colisFast',
            'lastUpdate' => '2025-01-01'
        ];
        
        return view('pages.legal.conditions', compact('data'));
    }

    public function confidentialite()
    {
        $data = [
            'title' => 'Politique de confidentialité - colisFast',
            'lastUpdate' => '2025-01-01'
        ];
        
        return view('pages.legal.confidentialite', compact('data'));
    }

    public function mentions()
    {
        $data = [
            'title' => 'Mentions légales - colisFast',
            'company' => [
                'name' => 'colisFast SARL',
                'address' => 'Dakar, Sénégal',
                'phone' => '+221 XX XXX XX XX',
                'email' => 'contact@colisfast.sn',
                'rccm' => 'SN-DKR-XXXX-X-XXXXXX'
            ]
        ];
        
        return view('pages.legal.mentions', compact('data'));
    }

    public function cookies()
    {
        $data = [
            'title' => 'Politique de cookies - colisFast',
            'lastUpdate' => '2025-01-01'
        ];
        
        return view('pages.legal.cookies', compact('data'));
    }

    public function sitemap()
    {
        $data = [
            'title' => 'Plan du site - colisFast',
            'sections' => [
                'Services' => [
                    'Livraison express',
                    'Livraison programmée',
                    'Solutions e-commerce'
                ],
                'Entreprise' => [
                    'À propos',
                    'Carrières',
                    'Blog'
                ],
                'Légal' => [
                    'Conditions d\'utilisation',
                    'Politique de confidentialité',
                    'Mentions légales',
                    'Politique de cookies'
                ]
            ]
        ];
        
        return view('pages.sitemap', compact('data'));
    }
}
?>