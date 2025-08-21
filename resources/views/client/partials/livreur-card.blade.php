<!-- resources/views/client/partials/livreur-card.blade.php -->
<div class="bg-white border rounded-lg p-4 shadow-sm">
    <div class="flex items-center">
        <div class="rounded-full w-12 h-12 bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
            <i class="fas fa-user text-xl"></i>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800">{{ $livreur->name }}</h4>
            <p class="text-sm text-gray-500">
                  @if($livreur->evaluations?->count() > 0)
           {{ number_format($livreur->evaluations->avg('note'), 1) }}/5
         <i class="fas fa-star text-yellow-400"></i>
          ({{ $livreur->evaluations->count() }} avis)
          @else
       Aucun avis
         @endif

            </p>
            <p class="text-sm text-red-600">En ligne</p>
        </div>
    </div>
</div>