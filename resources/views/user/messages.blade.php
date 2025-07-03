@extends('layouts.index')

@section('content')
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
                <input type="hidden" name="receiver_id" value="{{ getAdminId() }}">
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
            @if(count($communications) > 0)
                @foreach($communications as $comm)
                    <div class="mb-3 {{ $comm->sender_id === auth()->id() ? 'text-end' : 'text-start' }}" data-message-id="{{ $comm->id }}">
                        <div class="p-2 rounded {{ $comm->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                            <strong>{{ $comm->sender_id === auth()->id() ? 'Vous' : 'Admin' }}</strong><br>
                            {{ $comm->message }}<br>
                            <small>{{ $comm->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center p-3" id="noMessagesPlaceholder">
                    <p>Aucun message pour le moment. Commencez la conversation!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const currentUserId = {{ auth()->id() }};
    const messagesContainer = document.getElementById("messagesContainer");
    const messageError = document.getElementById("messageError");
    const noMessagesPlaceholder = document.getElementById("noMessagesPlaceholder");

    // Faire défiler jusqu'au dernier message au chargement
    window.onload = function() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        // Utiliser la route corrigée
         fetch("{{ route('user.messages.send') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprimer le placeholder s'il existe
                if (noMessagesPlaceholder) {
                    noMessagesPlaceholder.remove();
                }
                
                // Réinitialiser le champ message
                const messageText = document.getElementById('message').value;
                document.getElementById('message').value = '';
                
                // Ajouter le message directement
                const now = new Date();
                const formattedDate = now.toLocaleDateString('fr-FR') + ' ' + 
                                     String(now.getHours()).padStart(2, '0') + ':' + 
                                     String(now.getMinutes()).padStart(2, '0');
                
                const div = document.createElement("div");
                div.classList.add("mb-3", "text-end");
                // Ajouter un ID temporaire pour éviter les doublons
                div.setAttribute('data-message-id', 'temp-' + Date.now());
                div.innerHTML = `
                    <div class="p-2 rounded bg-primary text-white">
                        <strong>Vous</strong><br>
                        ${messageText}<br>
                        <small>${formattedDate}</small>
                    </div>
                `;

                messagesContainer.appendChild(div);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Masquer message d'erreur s'il était affiché
                messageError.style.display = 'none';
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

    function loadNewMessages() {
        // Trouver le dernier message avec un vrai ID (pas temporaire)
        const messageElements = document.querySelectorAll("#messagesContainer [data-message-id]");
        let lastMessageId = 0;
        
        messageElements.forEach(element => {
            const messageId = element.getAttribute("data-message-id");
            if (messageId && !messageId.startsWith('temp-')) {
                const id = parseInt(messageId);
                if (id > lastMessageId) {
                    lastMessageId = id;
                }
            }
        });
        
      fetch("{{ route('user.messages.check') }}", {
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
            
            if (newMessages && newMessages.length > 0) {
                // Supprimer le placeholder s'il existe
                if (noMessagesPlaceholder) {
                    noMessagesPlaceholder.remove();
                }
                
                newMessages.forEach(message => {
                    // Vérifier si le message n'est pas déjà affiché
                    if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                        // Supprimer les messages temporaires du même utilisateur si c'est son propre message
                        if (message.sender_id === currentUserId) {
                            const tempMessages = document.querySelectorAll('[data-message-id^="temp-"]');
                            tempMessages.forEach(temp => temp.remove());
                        }
                        
                        const div = document.createElement("div");
                        div.classList.add("mb-3", message.sender_id === currentUserId ? 'text-end' : 'text-start');
                        div.setAttribute('data-message-id', message.id);

                        const messageDate = new Date(message.created_at);
                        const formattedDate = messageDate.toLocaleDateString('fr-FR') + ' ' + 
                                           String(messageDate.getHours()).padStart(2, '0') + ':' + 
                                           String(messageDate.getMinutes()).padStart(2, '0');

                        div.innerHTML = `
                            <div class="p-2 rounded ${message.sender_id === currentUserId ? 'bg-primary text-white' : 'bg-light'}">
                                <strong>${message.sender_id === currentUserId ? 'Vous' : 'Admin'}</strong><br>
                                ${message.message}<br>
                                <small>${formattedDate}</small>
                            </div>
                        `;

                        messagesContainer.appendChild(div);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                });
            }
        })
        .catch(error => {
            console.error("Erreur lors du chargement des nouveaux messages:", error);
        });
    }

    // Vérifier les nouveaux messages toutes les 3 secondes
    setInterval(loadNewMessages, 3000);
    
    // Charger les nouveaux messages une première fois après 1 seconde
    setTimeout(loadNewMessages, 1000);
</script>
@endsection