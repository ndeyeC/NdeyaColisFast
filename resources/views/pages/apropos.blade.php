{{-- resources/views/pages/apropos.blade.php --}}
@extends('layouts.page')

@section('title', $data['title'])

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-gray-900 to-gray-700 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">À propos de colisFast</h1>
            <p class="text-xl md:text-2xl opacity-90">{{ $data['story']['mission'] }}</p>
        </div>
    </div>
</div>

<!-- Story Section -->
<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Notre histoire</h2>
                <div class="space-y-6 text-lg text-gray-600">
                    <p>{{ $data['story']['founding'] }}</p>
                    <p>{{ $data['story']['mission'] }}</p>
                    <p>{{ $data['story']['vision'] }}</p>
                </div>
            </div>
            <div class="bg-gray-100 p-8 rounded-lg">
                <!-- <img src="/api/placeholder/500/400" alt="Équipe colisFast" class="w-full rounded-lg"> -->
                <img src="{{ asset('image/logo2.jpg') }}" alt="Équipe colisFast" class="w-full rounded-lg">

            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="py-20 bg-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4">Nos chiffres clés</h2>
            <p class="text-xl opacity-90">La confiance de nos clients en chiffres</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($data['stats'] as $stat)
            <div class="text-center">
                <div class="text-4xl md:text-6xl font-bold mb-2">{{ $stat['number'] }}</div>
                <div class="text-lg opacity-90">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos valeurs</h2>
            <p class="text-xl text-gray-600">Ce qui guide nos actions au quotidien</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($data['values'] as $title => $description)
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4 text-red-600">{{ $title }}</h3>
                <p class="text-gray-600">{{ $description }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Team Section -->
<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Notre équipe</h2>
            <p class="text-xl text-gray-600">Des professionnels passionnés à votre service</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-bold mb-2">Ben Sara Nguom</h3>
                <p class="text-gray-600 mb-2">CEO Colisfast</p>
                <p class="text-sm text-gray-500">Expert en logistique</p>
            </div>
            
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-bold mb-2">M.Elysee Minstrie </h3>
                <p class="text-gray-600 mb-2">Admin</p>
                <p class="text-sm text-gray-500">Ingénieur<../p>
            </div>
            
            <div class="text-center">
                <div class="w-32 h-32 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-bold mb-2">Cheikh</h3>
                <p class="text-gray-600 mb-2">Responsable ....</p>
                <p class="text-sm text-gray-500">Coordonne nos équipes de livraison sur le terrain</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-20 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-6">Rejoignez l'aventure colisFast</h2>
        <p class="text-xl mb-8 opacity-90">Découvrez nos opportunités de carrière</p>
        <a href="{{ route('carrieres') }}" class="bg-red-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-red-700 transition-colors">
            Voir les postes
        </a>
    </div>
</div>
@endsection