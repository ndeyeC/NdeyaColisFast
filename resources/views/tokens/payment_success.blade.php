@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Succès !</strong>
            <span class="block sm:inline">Votre paiement a été effectué avec succès. Vos jetons ont été ajoutés à votre compte.</span>
        </div>
        <div class="text-center">
            <a href="{{ route('tokens.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Retourner à mes jetons
            </a>
        </div>
    </div>
@endsection