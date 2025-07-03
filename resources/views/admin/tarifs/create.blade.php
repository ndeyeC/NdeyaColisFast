@extends('layouts.admin')

@section('title', 'Créer un nouveau tarif')

@section('content')
<div class="card">
    <!-- <div class="card-header bg-primary text-white"> -->
        <div class="card-header bg-danger text-white">

        <!-- <h5 class="mb-0">Nouveau tarif de livraison</h5> -->
             <h5 class="mb-0 fw-bold">Nouveau tarif de livraison</h5>

    </div>
    <div class="card-body">
        <form action="{{ route('admin.tarifs.store') }}" method="POST">
            @csrf

            <!-- Zone de livraison -->
            <div class="row mb-4">
                <!-- Sélection de la région -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Région de référence</label>
                    <select name="zone" class="form-select @error('zone') is-invalid @enderror" required>
                        <option value="">Sélectionnez une région</option>
                        @foreach(['Dakar', 'Thiès', 'Kaolack', 'Mbour', 'Louga', 'Ziguinchor', 'Saint-Louis', 'Rufisque', 'Pikine', 'Guédiawaye', 'Matam', 'Tambacounda', 'Kolda', 'Fatick','Kedouguou','Sedhiou','Diourbel','Bambilor'] as $region)
                            <option value="{{ $region }}" @selected(old('zone') == $region)>
                                {{ $region }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type de zone -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Type de zone</label>
                    <select name="type_zone" class="form-select @error('type_zone') is-invalid @enderror" required>
                        <option value="">Sélectionnez...</option>
                        <option value="intra_urbaine" @selected(old('type_zone') == 'intra_urbaine')>Intra-urbaine (même ville)</option>
                        <option value="region_proche" @selected(old('type_zone') == 'region_proche')>Région proche (≤100km)</option>
                        <option value="region_eloignee" @selected(old('type_zone') == 'region_eloignee')>Région éloignée (≤300km)</option>
                        <!-- <option value="extra_urbaine" @selected(old('type_zone') == 'extra_urbaine')>Extra-urbaine (>300km)</option> -->
                    </select>
                    @error('type_zone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Type de livraison -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Type de service</label>
                    <select name="type_livraison" class="form-select @error('type_livraison') is-invalid @enderror" required>
                        <option value="">Sélectionnez...</option>
                        <option value="Standard" @selected(old('type_livraison') == 'Standard')>Standard (48h)</option>
                        <option value="Express" @selected(old('type_livraison') == 'Express')>Express (24h)</option>
                        <option value="Fragile" @selected(old('type_livraison') == 'Fragile')>Fragile (emballage spécial)</option>
                    </select>
                    @error('type_livraison')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tranches et Prix (reste identique à votre version) -->
            <div class="row mb-4">
                <!-- Tranche distance -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tranche de distance</label>
                    <select name="tranche_distance" class="form-select @error('tranche_distance') is-invalid @enderror" required>
                        <option value="">Sélectionnez...</option>
                        @foreach($distances as $key => $value)
                            <option value="{{ $key }}" @selected(old('tranche_distance') == $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('tranche_distance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tranche poids -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tranche de poids</label>
                    <select name="tranche_poids" class="form-select @error('tranche_poids') is-invalid @enderror" required>
                        <option value="">Sélectionnez...</option>
                        @foreach($poids as $key => $value)
                            <option value="{{ $key }}" @selected(old('tranche_poids') == $key)>
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
                               value="{{ old('prix') }}"
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
                <a href="{{ route('admin.tarifs.index') }}" class="btn btn-outline-danger fw-bold">
               <i class="fas fa-arrow-left"></i> Annuler
                </a>

                <!-- <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button> -->

                <button type="submit" class="btn btn-danger fw-bold">
                  <i class="fas fa-save"></i> Enregistrer
          </button>

            </div>
        </form>
    </div>
</div>
@endsection