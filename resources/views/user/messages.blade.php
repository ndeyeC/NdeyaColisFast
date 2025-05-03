@extends('layouts.index')

@section('content')
<!-- CSRF token dans le layout, à placer dans <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <h3>Messagerie avec l'administration</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Formulaire pour envoyer un message -->
    <div class="card mb-3">
        <div class="card-header">Envoyer un message</div>
        <div class="card-body">
            <form id="messageForm">
                @csrf
                <input type="hidden" name="receiver_id" value="1"> {{-- ID de l'admin --}}
                <input type="hidden" name="receiver_type" value="App\Models\User">

                <div class="mb-3">
                    <textarea name="message" id="message" class="form-control" rows="4" placeholder="Votre message..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
            <div id="messageError" class="text-danger mt-2" style="display: none;"></div>
        </div>
    </div>

    <!-- Affichage des messages -->
    <div class="card">
        <div class="card-header">Vos messages</div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="messagesContainer">
            @foreach($communications as $comm)
                <div class="mb-3 {{ $comm->sender_id === auth()->id() ? 'text-end' : 'text-start' }}" data-message-id="{{ $comm->id }}">
                    <div class="p-2 rounded {{ $comm->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                        <strong>{{ $comm->sender_id === auth()->id() ? 'Vous' : 'Admin' }}</strong><br>
                        {{ $comm->message }}<br>
                        <small>{{ $comm->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    const currentUserId = {{ auth()->id() }};
    const messagesContainer = document.getElementById("messagesContainer");
    const messageError = document.getElementById("messageError");

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("{{ route('user.messages.send') }}", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                messageError.style.display = 'block';
                messageError.textContent = data.message || "Une erreur est survenue.";
            }
        })
        .catch(error => {
            console.error("Erreur lors de l'envoi du message:", error);
            messageError.style.display = 'block';
            messageError.textContent = "Erreur réseau. Réessayez plus tard.";
        });
    });

    function loadNewMessages(lastMessageId) {
        fetch("{{ route('user.messages.new') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ last_id: lastMessageId })
        })
        .then(response => response.json())
        .then(data => {
            const newMessages = data.communications;

            newMessages.forEach(message => {
                const div = document.createElement("div");
                div.classList.add("mb-3", message.sender_id === currentUserId ? 'text-end' : 'text-start');
                div.setAttribute('data-message-id', message.id);

                div.innerHTML = `
                    <div class="p-2 rounded ${message.sender_id === currentUserId ? 'bg-primary text-white' : 'bg-light'}">
                        <strong>${message.sender_id === currentUserId ? 'Vous' : 'Admin'}</strong><br>
                        ${message.message}<br>
                        <small>${new Date(message.created_at).toLocaleString()}</small>
                    </div>
                `;

                messagesContainer.appendChild(div);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
        })
        .catch(error => {
            console.error("Erreur lors du chargement des nouveaux messages:", error);
        });
    }

    setInterval(() => {
        const lastMessageElement = document.querySelector("#messagesContainer .mb-3:last-child");
        const lastMessageId = lastMessageElement?.getAttribute("data-message-id") || 0;
        loadNewMessages(lastMessageId);
    }, 5000);
</script>
@endsection
