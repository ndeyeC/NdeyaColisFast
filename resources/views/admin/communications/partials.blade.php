<!-- resources/views/admin/communications/partials/messages.blade.php -->
@foreach($communications as $message)
<div class="mb-3 p-3 rounded {{ $message->is_admin ? 'bg-light' : 'bg-primary text-white' }}" 
     style="max-width: 80%; {{ $message->is_admin ? 'margin-left: auto;' : '' }}">
    <div class="d-flex justify-content-between">
        <strong>
            @if($message->is_admin)
                Admin
            @else
                {{ $message->user->name }}
                @if($message->livraison_id)
                    (Livraison #{{ $message->livraison_id }})
                @endif
            @endif
        </strong>
        <small>{{ $message->created_at->diffForHumans() }}</small>
    </div>
    <p class="mb-0">{{ $message->message }}</p>
</div>
@endforeach