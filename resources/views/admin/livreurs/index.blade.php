@extends('layouts.admin')

@section('title', 'Livreurs en attente de validation')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Type de véhicule</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($livreurs as $livreur)
                    <tr>
                        <td>{{ $livreur->name }}</td>
                        <td>{{ $livreur->email }}</td>
                        <td>{{ $livreur->numero_telephone }}</td>
                        <td>{{ $livreur->vehicule ?? 'Non spécifié' }}</td>
                        <td>
                        <a href="{{ route('admin.livreurs.show', $livreur->user_id) }}" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i> Détails
</a>
<form action="{{ route('admin.livreurs.destroy', $livreur->user_id) }}" method="POST" style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livreur ?');">
        <i class="fas fa-trash"></i> Supprimer
    </button>
</form>

</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <!-- Pagination dynamique si applicable -->
            {{ $livreurs->links() }}
        </div>
    </div>
</div>
@endsection
