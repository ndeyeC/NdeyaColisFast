@extends('layouts.admin')

@section('title', 'Modifier le tarif')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Modifier le tarif #{{ $tarif->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.tarifs.update', $tarif->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Zone de livraison -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Zone de livraison</label>
                    <input type="text" 
                           name="zone" 
                           class="form-control @error('zone') is-invalid @enderror"
                           value="{{ old('zone', $tarif->zone) }}"
                           required>
                    @error('zone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type de livraison -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Type de service</label>
                    <input type="text" 
                           name="type_livraison" 
                           class="form-control @error('type_livraison') is-invalid @enderror"
                           value="{{ old('type_livraison', $tarif->type_livraison) }}"
                           required>
                    @error('type_livraison')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tranches -->
            <div class="row mb-4">
                <!-- Tranche distance -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tranche de distance</label>
                    <select name="tranche_distance" 
                            class="form-select @error('tranche_distance') is-invalid @enderror" required>
                        @foreach($distances as $key => $value)
                            <option value="{{ $key }}" 
                                @selected(old('tranche_distance', $tarif->tranche_distance) == $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('tranche_distance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type de zone -->
<div class="col-md-6">
    <label class="form-label fw-bold">Type de zone</label>
    <select name="type_zone" 
            class="form-select @error('type_zone') is-invalid @enderror" 
            required>
        <option value="">Sélectionnez...</option>
        <option value="intra_urbaine" @selected(old('type_zone', $tarif->type_zone) == 'intra_urbaine')>Intra-urbaine</option>
        <option value="region_proche" @selected(old('type_zone', $tarif->type_zone) == 'region_proche')>Région proche</option>
        <option value="region_eloignee" @selected(old('type_zone', $tarif->type_zone) == 'region_eloignee')>Région éloignée</option>
        <option value="extra_urbaine" @selected(old('type_zone', $tarif->type_zone) == 'extra_urbaine')>Extra-urbaine</option>
    </select>
    @error('type_zone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                <!-- Tranche poids -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tranche de poids</label>
                    <select name="tranche_poids" 
                            class="form-select @error('tranche_poids') is-invalid @enderror" required>
                        @foreach($poids as $key => $value)
                            <option value="{{ $key }}" 
                                @selected(old('tranche_poids', $tarif->tranche_poids) == $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('tranche_poids')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Prix -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Prix (FCFA)</label>
                    <div class="input-group">
                        <input type="number" 
                               name="prix" 
                               class="form-control @error('prix') is-invalid @enderror"
                               value="{{ old('prix', $tarif->prix) }}"
                               min="0"
                               step="50"
                               required>
                        <span class="input-group-text">FCFA</span>
                        @error('prix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.tarifs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection