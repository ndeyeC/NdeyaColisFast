@extends('layouts.app')

@section('title', 'Accès refusé')

@section('content')
<div class="container text-center mt-5">
    <h1>403 - Accès refusé</h1>
    <p>Vous n'avez pas la permission d'accéder à cette page.</p>
    <a href="{{ route('login') }}" class="btn btn-primary">Retour à la connexion</a>
</div>
@endsection

