<div class="col-md-3 mb-4">
    <div class="card bg-primary text-white h-100 shadow">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase fw-bold">Livreurs</h6>
                <h2 class="mb-0">{{ $totalLivreurs }}</h2>
            </div>
            <i class="fas fa-users fa-2x"></i>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card bg-warning text-white h-100 shadow">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase fw-bold">En attente</h6>
                <h2 class="mb-0">{{ $livreursEnAttente }}</h2>
            </div>
            <i class="fas fa-clock fa-2x"></i>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card bg-success text-white h-100 shadow">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase fw-bold">Livraisons</h6>
                <h2 class="mb-0">{{ $totalLivraisons }}</h2>
            </div>
            <i class="fas fa-truck fa-2x"></i>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card bg-danger text-white h-100 shadow">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-uppercase fw-bold">Revenus</h6>
                <h2 class="mb-0">{{ number_format($revenusTotaux, 0, ',', ' ') }} CFA</h2>
            </div>
            <i class="fas fa-money-bill-wave fa-2x"></i>
        </div>
    </div>
</div>
