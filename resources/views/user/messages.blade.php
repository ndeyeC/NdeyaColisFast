@extends('layouts.index')

@section('content')
<div class="container py-4">
    <!-- ✅ Titre -->
     <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-3">
    <i class="fas fa-arrow-left me-1"></i> Retour
    </a>

    <div class="text-center mb-4">
        
        <h2 class="fw-bold text-danger">
            💬 Messagerie avec l'administration
        </h2>
        <p class="text-muted">Discutez directement avec notre équipe support</p>
    </div>

    <!-- ✅ Message de succès -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">

            <!-- ✅ Zone des messages -->
            <div class="card shadow-sm rounded-4 mb-3">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-comments"></i> Conversation
                </div>
                <div class="card-body p-3" 
                     style="max-height: 400px; overflow-y: auto; background: #f9fafb;" 
                     id="messagesContainer">
                     
                    @if(count($communications) > 0)
                        @foreach($communications as $comm)
                            <div class="d-flex {{ $comm->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-3" data-message-id="{{ $comm->id }}">
                                <div class="p-3 rounded-3 shadow-sm 
                                    {{ $comm->sender_id === auth()->id() 
                                        ? 'bg-danger text-white' 
                                        : 'bg-light text-dark' }} 
                                    " style="max-width: 70%">
                                    
                                    <strong class="d-block small mb-1">
                                        {{ $comm->sender_id === auth()->id() ? 'Vous' : 'Admin' }}
                                    </strong>
                                    <div>{{ $comm->message }}</div>
                                    <small class="d-block mt-1 text-light opacity-75">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $comm->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted" id="noMessagesPlaceholder">
                            <i class="fas fa-envelope-open-text fa-2x mb-2 text-danger"></i>
                            <p>Aucun message pour le moment.<br><b>Commencez la conversation !</b></p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ✅ Formulaire d’envoi -->
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <form id="messageForm" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ getAdminId() }}">
                        <input type="hidden" name="receiver_type" value="App\Models\User">

                        <textarea 
                            name="message" 
                            id="message" 
                            class="form-control rounded-3 shadow-sm" 
                            rows="2" 
                            placeholder="Écrivez votre message..." 
                            required></textarea>

                        <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center px-4">
                            <i class="fas fa-paper-plane me-1"></i> Envoyer
                        </button>
                    </form>
                    <div id="messageError" class="text-danger mt-2" style="display: none;"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ✅ Script -->
<script>
    const currentUserId = {{ auth()->id() }};
    const messagesContainer = document.getElementById("messagesContainer");
    const messageError = document.getElementById("messageError");
    const noMessagesPlaceholder = document.getElementById("noMessagesPlaceholder");

    window.onload = function() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

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
                if (noMessagesPlaceholder) noMessagesPlaceholder.remove();
                
                const messageText = document.getElementById('message').value;
                document.getElementById('message').value = '';
                
                const now = new Date();
                const formattedDate = now.toLocaleDateString('fr-FR') + ' ' + 
                                     String(now.getHours()).padStart(2, '0') + ':' + 
                                     String(now.getMinutes()).padStart(2, '0');
                
                const div = document.createElement("div");
                div.classList.add("d-flex", "justify-content-end", "mb-3");
                div.setAttribute('data-message-id', 'temp-' + Date.now());
                div.innerHTML = `
                    <div class="p-3 rounded-3 shadow-sm bg-danger text-white" style="max-width: 70%">
                        <strong class="d-block small mb-1">Vous</strong>
                        ${messageText}
                        <small class="d-block mt-1 text-light opacity-75">
                            <i class="fas fa-clock me-1"></i>${formattedDate}
                        </small>
                    </div>
                `;

                messagesContainer.appendChild(div);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
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
        const messageElements = document.querySelectorAll("#messagesContainer [data-message-id]");
        let lastMessageId = 0;
        
        messageElements.forEach(element => {
            const messageId = element.getAttribute("data-message-id");
            if (messageId && !messageId.startsWith('temp-')) {
                const id = parseInt(messageId);
                if (id > lastMessageId) lastMessageId = id;
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
                if (noMessagesPlaceholder) noMessagesPlaceholder.remove();
                
                newMessages.forEach(message => {
                    if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                        if (message.sender_id === currentUserId) {
                            document.querySelectorAll('[data-message-id^="temp-"]').forEach(temp => temp.remove());
                        }
                        
                        const div = document.createElement("div");
                        div.classList.add("d-flex", message.sender_id === currentUserId ? 'justify-content-end' : 'justify-content-start', "mb-3");
                        div.setAttribute('data-message-id', message.id);

                        const messageDate = new Date(message.created_at);
                        const formattedDate = messageDate.toLocaleDateString('fr-FR') + ' ' + 
                                           String(messageDate.getHours()).padStart(2, '0') + ':' + 
                                           String(messageDate.getMinutes()).padStart(2, '0');

                        div.innerHTML = `
                            <div class="p-3 rounded-3 shadow-sm ${message.sender_id === currentUserId ? 'bg-danger text-white' : 'bg-light'}" style="max-width: 70%">
                                <strong class="d-block small mb-1">${message.sender_id === currentUserId ? 'Vous' : 'Admin'}</strong>
                                ${message.message}
                                <small class="d-block mt-1 text-muted">
                                    <i class="fas fa-clock me-1"></i>${formattedDate}
                                </small>
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

    setInterval(loadNewMessages, 3000);
    setTimeout(loadNewMessages, 1000);
</script>
@endsection
