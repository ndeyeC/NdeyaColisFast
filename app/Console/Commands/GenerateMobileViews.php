<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class GenerateMobileViews extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mobile:generate';

    /**
     * The description of the console command.
     */
    protected $description = 'Génère les vues HTML mobiles depuis les vues Blade';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Génération des vues mobiles...');

        // Créer le dossier mobile s'il n'existe pas
        $mobileDir = public_path('mobile');
        if (!File::exists($mobileDir)) {
            File::makeDirectory($mobileDir, 0755, true);
        }

        // Créer les sous-dossiers
        $this->createDirectories($mobileDir);

        // Générer les vues principales
        $this->generateWelcomePage($mobileDir);
        $this->generateDashboards($mobileDir);
        $this->generateCommandesPages($mobileDir);
        $this->generateAuthPages($mobileDir);

        // Copier les assets
        $this->copyAssets($mobileDir);

        // Générer le fichier principal Capacitor
        $this->generateCapacitorConfig($mobileDir);

        $this->info('✅ Vues mobiles générées avec succès !');
        $this->info("📱 Dossier mobile : {$mobileDir}");
    }

    private function createDirectories($mobileDir)
    {
        $directories = [
            'css',
            'js', 
            'assets',
            'assets/images'
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($mobileDir . '/' . $dir, 0755, true, true);
        }
    }

    private function generateWelcomePage($mobileDir)
    {
        $this->info('📄 Génération de la page d\'accueil...');

        $welcomeHtml = $this->convertBladeToMobileHtml('welcome', [
            'title' => 'ColisFast - Livraison Rapide',
            'isMobile' => true
        ]);

        File::put($mobileDir . '/index.html', $welcomeHtml);
    }

    private function generateDashboards($mobileDir)
    {
        $this->info('📊 Génération des dashboards...');

        $dashboards = [
            'client' => 'client.dashboard',
            'livreur' => 'livreur.dashboarde', 
            'admin' => 'admin.dashboard'
        ];

        foreach ($dashboards as $role => $view) {
            try {
                $html = $this->convertBladeToMobileHtml($view, [
                    'user' => (object)['role' => $role, 'name' => 'Utilisateur'],
                    'isMobile' => true
                ]);
                
                File::put($mobileDir . "/dashboard-{$role}.html", $html);
                $this->line("✅ Dashboard {$role} généré");
            } catch (\Exception $e) {
                $this->warn("⚠️  Impossible de générer le dashboard {$role}: " . $e->getMessage());
            }
        }
    }

    private function generateCommandesPages($mobileDir)
    {
        $this->info('📦 Génération des pages commandes...');

        $commandePages = [
            'create' => 'commnandes.create',
            'index' => 'commnandes.index'
        ];

        foreach ($commandePages as $page => $view) {
            try {
                $html = $this->convertBladeToMobileHtml($view, [
                    'isMobile' => true,
                    'user' => (object)['role' => 'client']
                ]);
                
                File::put($mobileDir . "/commandes-{$page}.html", $html);
                $this->line("✅ Page commandes-{$page} générée");
            } catch (\Exception $e) {
                $this->warn("⚠️  Page {$page} non trouvée, génération d'une version basique");
                $this->generateBasicCommandePage($mobileDir, $page);
            }
        }
    }

    private function generateAuthPages($mobileDir)
    {
        $this->info('🔐 Génération des pages d\'authentification...');

        $authPages = [
            'login' => 'auth.login',
            'register' => 'auth.register'
        ];

        foreach ($authPages as $page => $view) {
            try {
                $html = $this->convertBladeToMobileHtml($view, ['isMobile' => true]);
                File::put($mobileDir . "/{$page}.html", $html);
                $this->line("✅ Page {$page} générée");
            } catch (\Exception $e) {
                $this->warn("⚠️  Page {$page} non trouvée, génération d'une version mobile basique");
                $this->generateBasicAuthPage($mobileDir, $page);
            }
        }
    }

    private function convertBladeToMobileHtml($viewName, $data = [])
    {
        // Simuler un utilisateur pour les vues qui en ont besoin
        if (!auth()->check() && isset($data['user'])) {
            // Créer un utilisateur fictif pour la génération
            config(['auth.guards.web.provider' => null]);
        }

        // Générer le HTML depuis la vue Blade
        $html = View::make($viewName, $data)->render();

        // Optimisations pour mobile
        $html = $this->optimizeForMobile($html);

        return $html;
    }

    private function optimizeForMobile($html)
    {
        // Ajouter la meta viewport si elle n'existe pas
        if (!str_contains($html, 'viewport')) {
            $html = str_replace(
                '<head>',
                '<head><meta name="viewport" content="width=device-width, initial-scale=1.0">',
                $html
            );
        }

        // Remplacer les liens Laravel par des liens mobiles relatifs
        $html = preg_replace('/href=["\']\/([^"\']*)["\']/', 'href="$1.html"', $html);
        
        // Remplacer les actions de formulaire pour l'API mobile
        $html = preg_replace('/action=["\']\/([^"\']*)["\']/', 'action="api/$1"', $html);

        // Ajouter les scripts mobiles essentiels
        $mobileScripts = '
        <script src="js/mobile-api.js"></script>
        <script src="js/mobile-auth.js"></script>
        <script src="js/capacitor.js"></script>
        ';

        $html = str_replace('</body>', $mobileScripts . '</body>', $html);

        // Optimiser pour Capacitor
        $html = str_replace('{{ asset(', '{{ mobile_asset(', $html);

        return $html;
    }

    private function generateBasicCommandePage($mobileDir, $page)
    {
        $basicHtml = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes - ColisFast</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Commandes - ' . ucfirst($page) . '</h1>
        <div class="bg-white rounded-xl p-6">
            <p>Page en cours de développement...</p>
            <a href="index.html" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                Retour à l\'accueil
            </a>
        </div>
    </div>
</body>
</html>';

        File::put($mobileDir . "/commandes-{$page}.html", $basicHtml);
    }

    private function generateBasicAuthPage($mobileDir, $page)
    {
        $isLogin = $page === 'login';
        $title = $isLogin ? 'Connexion' : 'Inscription';

        $basicHtml = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . ' - ColisFast</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl p-8">
            <h2 class="text-2xl font-bold text-center mb-8">' . $title . '</h2>
            <form id="' . $page . 'Form">
                ' . ($isLogin ? '
                <div class="mb-4">
                    <input type="email" placeholder="Email" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-6">
                    <input type="password" placeholder="Mot de passe" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                ' : '
                <div class="mb-4">
                    <input type="text" placeholder="Nom complet" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <input type="email" placeholder="Email" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                <div class="mb-6">
                    <input type="password" placeholder="Mot de passe" class="w-full px-4 py-3 border rounded-lg" required>
                </div>
                ') . '
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold">
                    ' . $title . '
                </button>
            </form>
        </div>
    </div>
    <script src="js/mobile-auth.js"></script>
</body>
</html>';

        File::put($mobileDir . "/{$page}.html", $basicHtml);
    }

    private function copyAssets($mobileDir)
    {
        $this->info('📁 Copie des assets...');

        // Copier les CSS depuis public/css
        if (File::exists(public_path('css'))) {
            File::copyDirectory(public_path('css'), $mobileDir . '/css');
        }

        // Copier les JS depuis public/js  
        if (File::exists(public_path('js'))) {
            File::copyDirectory(public_path('js'), $mobileDir . '/js');
        }

        // Copier les images
        if (File::exists(public_path('image'))) {
            File::copyDirectory(public_path('image'), $mobileDir . '/assets/images');
        }

        // Copier le logo
        if (File::exists(public_path('logo1.png'))) {
            File::copy(public_path('logo1.png'), $mobileDir . '/assets/logo1.png');
        }
    }

    private function generateCapacitorConfig($mobileDir)
    {
        $this->info('⚙️  Génération de la configuration Capacitor...');

        $capacitorConfig = '{
  "appId": "com.colisfast.app",
  "appName": "ColisFast",
  "webDir": "' . $mobileDir . '",
  "bundledWebRuntime": false,
  "server": {
    "url": "http://127.0.0.1:8000",
    "cleartext": true
  },
  "plugins": {
    "SplashScreen": {
      "launchShowDuration": 2000,
      "backgroundColor": "#667eea"
    }
  }
}';

        File::put(base_path('capacitor.config.json'), $capacitorConfig);
    }
}