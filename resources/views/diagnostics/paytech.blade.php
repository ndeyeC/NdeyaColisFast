<!-- resources/views/diagnostics/paytech.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Diagnostic PayTech</h1>
    
    <div class="card mb-4">
        <div class="card-header">Configuration</div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Clé API configurée</th>
                    <td>{{ $config['api_key_present'] ? '✅ Oui' : '❌ Non' }}</td>
                </tr>
                <tr>
                    <th>Secret API configuré</th>
                    <td>{{ $config['api_secret_present'] ? '✅ Oui' : '❌ Non' }}</td>
                </tr>
                <tr>
                    <th>URL de base</th>
                    <td>{{ $config['base_url'] }}</td>
                </tr>
                <tr>
                    <th>Devise</th>
                    <td>{{ $config['currency'] }}</td>
                </tr>
                <tr>
                    <th>Environnement PayTech</th>
                    <td>{{ $config['env'] }}</td>
                </tr>
                <tr>
                    <th>Environnement Application</th>
                    <td>{{ $config['app_env'] }}</td>
                </tr>
                <tr>
                    <th>URL Application</th>
                    <td>{{ $config['app_url'] }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Vérification de la configuration</div>
        <div class="card-body">
            <div class="alert {{ $results['config_check']['success'] ? 'alert-success' : 'alert-danger' }}">
                @if($results['config_check']['success'])
                    ✅ Configuration correcte
                @else
                    ❌ Problèmes de configuration détectés
                @endif
            </div>
            
            @if(!$results['config_check']['success'])
                <ul>
                    @foreach($results['config_check']['issues'] as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Test de connectivité</div>
        <div class="card-body">
            <div class="alert {{ $results['connectivity']['success'] ? 'alert-success' : 'alert-danger' }}">
                @if($results['connectivity']['success'])
                    ✅ Connexion réussie ({{ $results['connectivity']['time_ms'] }} ms)
                @else
                    ❌ Échec de connexion: {{ $results['connectivity']['message'] }}
                @endif
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Test JSON</div>
        <div class="card-body">
            <div class="alert {{ $results['json_test']['success'] ? 'alert-success' : 'alert-danger' }}">
                @if($results['json_test']['success'])
                    ✅ Requête JSON réussie ({{ $results['json_test']['time_ms'] }} ms)
                @else
                    ❌ Échec de la requête JSON
                @endif
            </div>
            
            <h5>Réponse:</h5>
            <pre>{{ json_encode($results['json_test']['response'] ?? [], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Test Form</div>
        <div class="card-body">
            <div class="alert {{ $results['form_test']['success'] ? 'alert-success' : 'alert-danger' }}">
                @if($results['form_test']['success'])
                    ✅ Requête Form réussie ({{ $results['form_test']['time_ms'] }} ms)
                @else
                ❌ Échec de la requête Form
                @endif
            </div>

            <h5>Réponse:</h5>
            <pre>{{ json_encode($results['form_test']['response'] ?? [], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
