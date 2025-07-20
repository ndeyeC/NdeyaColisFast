@extends('layouts.admin')

@section('title', 'Communications')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header text-white" style="background-color: #dc3545;">
                <h5 class="mb-0">Conversations</h5>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs" id="conversationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id Bronze
                        id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true" style="color: #343a40;">
                            Clients
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="livreurs-tab" data-bs-toggle="tab" data-bs-target="#livreurs" type="button" role="tab" aria-controls="livreurs" aria-selected="false" style="color: #343a40;">
                            Livreurs
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="conversationTabsContent">
                    <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            @forelse($users as $user)
                                <a href="#" class="list-group-item list-group-item-action conversation-item" 
                                   data-receiver-id="{{ $user->user_id }}" 
                                   data-receiver-type="App\Models\User"
                                   style="border-left: 3px solid transparent; transition: all 0.3s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong style="color: #343a40;">{{ $user->name }}</strong>
                                            <small class="d-block text-muted">{{ $user->email }}</small>
                                        </div>
                                        <span class="badge rounded-pill unread-count" 
                                              data-user-id="{{ $user->user_id }}"
                                              style="background-color: #ffc107; color: #212529;">0</span>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-center text-muted">
                                    Aucune conversation avec des clients
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="livreurs" role="tabpanel" aria-labelledby="livreurs-tab">
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            @forelse($livreurs as $livreur)
                                <a href="#" class="list-group-item list-group-item-action livreur-item" 
                                   data-livreur-id="{{ $livreur->user_id }}"
                                   style="border-left: 3px solid transparent; transition: all 0.3s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong style="color: #343a40;">{{ $livreur->name }}</strong>
                                            <small class="d-block text-muted">{{ $livreur->numero_telephone }}</small>
                                            <small class="d-block text-muted">{{ $livreur->email }}</small>
                                        </div>
                                        <span class="badge rounded-pill" 
                                              style="background-color: {{ $livreur->is_active ? '#28a745' : '#6c757d' }}; color: white;">
                                            {{ $livreur->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-center text-muted">
                                    Aucun livreur disponible
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center" 
                 style="background-color: #dc3545;">
                <h5 class="mb-0" id="conversation-title">Messages</h5>
            </div>
            <div class="card-body">
                <div class="chat-container" id="chat-messages" 
                     style="max-height: 400px; overflow-y: auto; background: #f9fafb; border-radius: 8px; padding: 15px;">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-envelope-open-text fa-2x mb-2 text-danger"></i>
                        <p>Sélectionnez une conversation pour afficher les messages</p>
                    </div>
                </div>

                <form id="message-form" class="mt-4 d-flex gap-2">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiver_id">
                    <input type="hidden" name="receiver_type" id="receiver_type">
                    <textarea name="message" id="message-input" 
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

    <div class="col-md-3">
        <div class="card shadow-sm rounded-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                 style="background-color: #dc3545;">
                <h5 class="mb-0">Notifications ({{ $notifications->count() }})</h5>
                <button id="mark-all-read" class="btn btn-sm" 
                        style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 6px;">
                    <i class="fas fa-check-double"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush notification-container" style="max-height: 500px; overflow-y: auto;">
                    @forelse($notifications as $notification)
                    <div class="list-group-item notification-item {{ $notification->is_read ? '' : 'fw-bold' }}"
                         data-id="{{ $notification->id }}"
                         style="{{ $notification->is_read ? '' : 'background-color: #f9fafb; border-left: 3px solid #dc3545;' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #343a40;">{{ $notification->message }}</span>
                            <div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                <button class="btn btn-sm btn-link notification-delete" 
                                        data-id="{{ $notification->id }}"
                                        style="color: #dc3545; padding: 2px 6px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted d-block">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</small>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        Aucune nouvelle notification
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="card mt-3 shadow-sm rounded-4" id="livreur-info-panel" style="display: none;">
            <div class="card-header text-white" style="background-color: #dc3545;">
                <h5 class="mb-0">Détails du livreur</h5>
            </div>
            <div class="card-body">
                <div id="livreur-info-content">
                    <!-- Livreur info will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails Livreur -->
<div class="modal fade" id="livreurDetailsModal" tabindex="-1" aria-labelledby="livreurDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #dc3545;">
                <h5 class="modal-title" id="livreurDetailsModalLabel">Détails du livreur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="livreur-details-content">
                <!-- Livreur details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    Fermer
                </button>
                <button type="button" class="btn btn-success chat-with-livreur" data-bs-dismiss="modal">
                    <i class="fas fa-comments"></i> Envoyer un message
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .chat-container {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .notification-item {
        transition: all 0.3s ease;
    }
    
    .notification-item:hover {
        background-color: #f1f1f1;
    }
    
    .notification-delete {
        visibility: hidden;
        padding: 0;
        margin: 0 0 0 10px;
    }
    
    .notification-item:hover .notification-delete {
        visibility: visible;
    }
    
    .conversation-item:hover, 
    .livreur-item:hover {
        border-left: 3px solid #dc3545 !important;
        background-color: #fff1f1 !important;
    }

    .conversation-item.active,
    .livreur-item.active {
        border-left: 3px solid #dc3545 !important;
        background-color: #ffe6e6 !important;
    }

    .nav-tabs .nav-link.active {
        background-color: #f9fafb !important;
        border-color: #dee2e6 #dee2e6 #f9fafb !important;
        color: #343a40 !important;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6 !important;
        color: #343a40 !important;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .message-bubble {
        position: relative;
        max-width: 70%;
        margin-bottom: 15px;
        padding: 10px 15px;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .message-bubble.sender {
        background-color: #dc3545;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .message-bubble.sender::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        border-left: 10px solid #dc3545;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
    }

    .message-bubble.receiver {
        background-color: #ffffff;
        color: #343a40;
        border-bottom-left-radius: 4px;
    }

    .message-bubble.receiver::after {
        content: '';
        position: absolute;
        left: -10px;
        top: 50%;
        transform: translateY(-50%);
        border-right: 10px solid #ffffff;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
    }

    .message-content {
        margin-bottom: 5px;
        word-wrap: break-word;
    }

    .message-meta {
        font-size: 0.75rem;
        opacity: 0.7;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentReceiverId = null;
    let currentReceiverType = null;
    let lastMessageId = 0;
    let messagePollingInterval = null;
    let currentUserId = $('meta[name="user-id"]').attr('content');

    $(document).on('click', '.conversation-item', function(e) {
        e.preventDefault();
        const receiverId = $(this).data('receiverId');
        if (!receiverId) {
            alert("Impossible de charger cette conversation: identifiant manquant");
            return;
        }
        selectRecipient($(this), 'client');
    });

    $(document).on('click', '.livreur-item', function(e) {
        e.preventDefault();
        const livreurId = $(this).data('livreurId');
        if (!livreurId) {
            alert("Impossible de charger cette conversation: identifiant manquant");
            return;
        }
        selectRecipient($(this), 'livreur');
    });

    function selectRecipient(element, type) {
        $('.conversation-item, .livreur-item').removeClass('active');
        element.addClass('active');
        if (type === 'client') {
            currentReceiverId = element.data('receiverId');
            currentReceiverType = 'App\\Models\\User';
        } else {
            currentReceiverId = element.data('livreurId');
            currentReceiverType = 'App\\Models\\Livreur';
        }
        if (!currentReceiverId) {
            alert("Erreur: Identifiant du destinataire manquant");
            return;
        }
        $('#receiver_id').val(currentReceiverId);
        $('#receiver_type').val(currentReceiverType);
        const name = element.find('strong').text();
        $('#conversation-title').text('Messages avec ' + name);
        loadConversation(currentReceiverId, currentReceiverType);
        element.find('.unread-count').text('0');
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        messagePollingInterval = setInterval(checkForNewMessages, 3000);
        if (type === 'livreur') {
            loadLivreurDetails(currentReceiverId);
            $('#livreur-info-panel').show();
        } else {
            $('#livreur-info-panel').hide();
        }
    }

    function loadConversation(receiverId, receiverType) {
        if (!receiverId || !receiverType) {
            $('#chat-messages').html(`
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-envelope-open-text fa-2x mb-2 text-danger"></i>
                    <p>Impossible de charger la conversation<br><b>Veuillez sélectionner un destinataire valide</b></p>
                </div>
            `);
            return;
        }
        $('#chat-messages').html('<div class="text-center py-5"><div class="spinner-border text-danger" role="status"></div><p class="mt-2 text-muted">Chargement des messages...</p></div>');
        $.ajax({
            url: "/admin/communications/conversation",
            method: 'GET',
            data: { receiver_id: receiverId, receiver_type: receiverType },
            success: function(response) {
                if (response.communications) {
                    displayMessages(response.communications);
                    lastMessageId = response.communications.length > 0 ? response.communications[response.communications.length - 1].id : 0;
                } else {
                    $('#chat-messages').html(`
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-envelope-open-text fa-2x mb-2 text-danger"></i>
                            <p>Aucun message pour le moment.<br><b>Commencez la conversation !</b></p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#chat-messages').html(`
                    <div class="alert alert-danger shadow-sm">
                        Erreur lors du chargement des messages
                    </div>
                `);
            }
        });
    }

    function displayMessages(messages) {
        let html = '';
        if (!messages || messages.length === 0) {
            html = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-envelope-open-text fa-2x mb-2 text-danger"></i>
                    <p>Aucun message pour le moment.<br><b>Commencez la conversation !</b></p>
                </div>
            `;
        } else {
            messages.forEach(msg => {
                const isSender = msg.sender_type.includes('User') && msg.sender_id == currentUserId;
                const messageTime = new Date(msg.created_at).toLocaleString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                html += `
                    <div class="d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'} mb-3 fade-in" 
                         data-message-id="${msg.id}">
                        <div class="message-bubble ${isSender ? 'sender' : 'receiver'}">
                            <div class="message-content">${msg.message}</div>
                            <div class="message-meta">
                                <i class="fas fa-clock"></i>
                                <span>${messageTime}</span>
                                ${isSender ? '<i class="fas fa-check-double text-light"></i>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $('#chat-messages').html(html);
        scrollChatToBottom();
    }

    function scrollChatToBottom() {
        const chatContainer = document.getElementById('chat-messages');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    $(document).on('submit', '#message-form', function(e) {
        e.preventDefault();
        if (!$('#receiver_id').val() || !$('#receiver_type').val()) {
            $('#messageError').text('Veuillez sélectionner un destinataire').show();
            return;
        }
        const messageText = $('#message-input').val().trim();
        if (!messageText) {
            $('#messageError').text('Veuillez entrer un message').show();
            return;
        }
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Envoi...');
        $.ajax({
            url: "/admin/communications",
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                message: messageText,
                receiver_id: $('#receiver_id').val(),
                receiver_type: $('#receiver_type').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#message-input').val('');
                    $('#messageError').hide();
                    const msg = response.communication;
                    const messageTime = new Date().toLocaleString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    const html = `
                        <div class="d-flex justify-content-end mb-3 fade-in" 
                             data-message-id="temp-${Date.now()}">
                            <div class="message-bubble sender">
                                <div class="message-content">${msg.message}</div>
                                <div class="message-meta">
                                    <i class="fas fa-clock"></i>
                                    <span>${messageTime}</span>
                                    <i class="fas fa-check-double text-light"></i>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#chat-messages').append(html);
                    scrollChatToBottom();
                    lastMessageId = msg.id;
                } else {
                    $('#messageError').text(response.message || 'Une erreur est survenue').show();
                }
            },
            error: function(xhr, status, error) {
                $('#messageError').text('Erreur réseau. Réessayez plus tard.').show();
            },
            complete: function() {
                submitButton.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Envoyer');
            }
        });
    });

    function checkForNewMessages() {
        if (!currentReceiverId || !currentReceiverType || lastMessageId === 0) return;
        $.ajax({
            url: "/admin/communications/new",
            method: 'GET',
            data: {
                receiver_id: currentReceiverId,
                receiver_type: currentReceiverType,
                last_id: lastMessageId
            },
            success: function(response) {
                if (response.communications && response.communications.length > 0) {
                    response.communications.forEach(msg => {
                        if (!$(`[data-message-id="${msg.id}"]`).length) {
                            if (msg.sender_id === currentUserId) {
                                $(`[data-message-id^="temp-"]`).remove();
                            }
                            const isSender = msg.sender_type.includes('User') && msg.sender_id == currentUserId;
                            const messageTime = new Date(msg.created_at).toLocaleString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                            const html = `
                                <div class="d-flex ${isSender ? 'justify-content-end' : 'justify-content-start'} mb-3 fade-in" 
                                     data-message-id="${msg.id}">
                                    <div class="message-bubble ${isSender ? 'sender' : 'receiver'}">
                                        <div class="message-content">${msg.message}</div>
                                        <div class="message-meta">
                                            <i class="fas fa-clock"></i>
                                            <span>${messageTime}</span>
                                            ${isSender ? '<i class="fas fa-check-double text-light"></i>' : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#chat-messages').append(html);
                            scrollChatToBottom();
                            lastMessageId = msg.id;
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de vérification des nouveaux messages:', error);
            }
        });
    }

    function loadLivreurDetails(livreurId) {
        if (!livreurId) {
            $('#livreur-info-content').html('<div class="alert alert-danger shadow-sm">ID livreur manquant</div>');
            return;
        }
        $.ajax({
            url: `/admin/livreurs/${livreurId}/json`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.livreur) {
                    const livreur = response.livreur;
                    const html = `
                        <div class="card-body">
                            <h5 class="card-title" style="color: #343a40;">${livreur.name}</h5>
                            <p class="card-text text-muted"><i class="fas fa-phone me-2"></i>${livreur.phone || 'Non renseigné'}</p>
                            <p class="card-text text-muted"><i class="fas fa-envelope me-2"></i>${livreur.email}</p>
                            <p class="card-text">
                                <i class="fas fa-circle me-2 ${livreur.is_active ? 'text-success' : 'text-muted'}"></i>
                                <span style="color: ${livreur.is_active ? '#28a745' : '#6c757d'};">${livreur.is_active ? 'Actif' : 'Inactif'}</span>
                            </p>
                            <hr style="border-color: #dee2e6;">
                            <p class="card-text"><small class="text-muted">Livraisons effectuées: ${livreur.livraisons_count || 0}</small></p>
                            <p class="card-text"><small class="text-muted">Évaluation moyenne: ${livreur.rating_avg || 'Aucune'}</small></p>
                        </div>
                    `;
                    $('#livreur-info-content').html(html);
                } else {
                    $('#livreur-info-content').html('<div class="alert alert-danger shadow-sm">Informations livreur incomplètes</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#livreur-info-content').html('<div class="alert alert-danger shadow-sm">Erreur lors du chargement des détails</div>');
            }
        });
    }

    $(document).on('click', '.notification-item', function() {
        const notificationId = $(this).data('id');
        $.ajax({
            url: `/admin/notifications/mark-read/${notificationId}`,
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                $(`[data-id="${notificationId}"]`).removeClass('fw-bold').css('background-color', '');
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du marquage de notification:', error);
            }
        });
    });

    $(document).on('click', '#mark-all-read', function() {
        $.ajax({
            url: "/admin/notifications/mark-all-read",
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                $('.notification-item').removeClass('fw-bold').css('background-color', '');
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du marquage de toutes les notifications:', error);
            }
        });
    });

    $(document).on('click', '.notification-delete', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const notificationId = $(this).data('id');
        const notificationElement = $(this).closest('[data-id]');
        const deleteButton = $(this);
        if (confirm('Voulez-vous supprimer cette notification ?')) {
            deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                url: `/admin/notifications/delete/${notificationId}`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function() {
                    notificationElement.fadeOut(300, function() { $(this).remove(); });
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la suppression: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    deleteButton.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

    function refreshUnreadCounts() {
        $.ajax({
            url: "/admin/communications/unread-counts",
            method: 'GET',
            success: function(response) {
                if (response.counts) {
                    Object.keys(response.counts).forEach(function(userId) {
                        $(`span.unread-count[data-user-id="${userId}"]`).text(response.counts[userId]);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de récupération des compteurs de messages non lus:', error);
            }
        });
    }

    setInterval(refreshUnreadCounts, 10000);
    setTimeout(refreshUnreadCounts, 1000);

    // Ensure chat scrolls to bottom on load
    window.onload = function() {
        scrollChatToBottom();
    };
});
</script>
@endsection