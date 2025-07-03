@extends('layouts.admin')

@section('title', 'Communications')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header text-white" style="background-color: #6c757d;">
                <h5 class="mb-0">Conversations</h5>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs" id="conversationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true" style="color: #495057;">
                            Clients
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="livreurs-tab" data-bs-toggle="tab" data-bs-target="#livreurs" type="button" role="tab" aria-controls="livreurs" aria-selected="false" style="color: #495057;">
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
                                            <strong style="color: #495057;">{{ $user->name }}</strong>
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
                                            <strong style="color: #495057;">{{ $livreur->name }}</strong>
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
        <div class="card mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center" 
                 style="background-color: #6c757d;">
                <h5 class="mb-0" id="conversation-title">Messages</h5>
            </div>
            <div class="card-body">
                <div class="chat-container" id="chat-messages" 
                     style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background-color: #fafafa;">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comments fa-3x mb-3" style="color: #6c757d;"></i>
                        <p>Sélectionnez une conversation pour afficher les messages</p>
                    </div>
                </div>

                <form id="message-form" class="mt-4">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="message" id="message-input" 
                               class="form-control" 
                               placeholder="Écrire un message..." 
                               required
                               style="border: 1px solid #ced4da; border-radius: 8px 0 0 8px;">
                        <input type="hidden" name="receiver_id" id="receiver_id">
                        <input type="hidden" name="receiver_type" id="receiver_type">
                     <button type="submit" class="btn fw-bold" 
                   style="background-color: #6c757d; border-color: #6c757d; color: white; border-radius: 0 8px 8px 0; transition: all 0.3s ease;">
                     <i class="fas fa-paper-plane"></i> Envoyer
                 </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                 style="background-color: #17a2b8;">
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
                         style="{{ $notification->is_read ? '' : 'background-color: #f8f9fa; border-left: 3px solid #17a2b8;' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: #495057;">{{ $notification->message }}</span>
                            <div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                <button class="btn btn-sm btn-link notification-delete" 
                                        data-id="{{ $notification->id }}"
                                        style="color: #6c757d; padding: 2px 6px;">
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
        
        <div class="card mt-3" id="livreur-info-panel" style="display: none;">
            <div class="card-header text-white" style="background-color: #6f42c1;">
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
            <div class="modal-header text-white" style="background-color: #6c757d;">
                <h5 class="modal-title" id="livreurDetailsModalLabel">Détails du livreur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="livreur-details-content">
                <!-- Livreur details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal"
                        style="background-color: #6c757d; color: white; border-radius: 6px;">
                    Fermer
                </button>
                <button type="button" class="btn fw-bold chat-with-livreur" data-bs-dismiss="modal"
                        style="background-color: #28a745; color: white; border-radius: 6px;">
                    <i class="fas fa-comments"></i> Envoyer un message
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour les éléments actifs et les interactions */
.conversation-item:hover, 
.livreur-item:hover {
    border-left: 3px solid #6c757d !important;
    background-color: #f8f9fa !important;
}

.conversation-item.active,
.livreur-item.active {
    border-left: 3px solid #28a745 !important;
    background-color: #e8f5e8 !important;
}

.nav-tabs .nav-link.active {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 #dee2e6 #f8f9fa !important;
    color: #495057 !important;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6 !important;
    color: #495057 !important;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.notification-item:hover {
    background-color: #f8f9fa !important;
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
/**
 * ColisFast Admin Panel JavaScript
 * 
 * Ce fichier contient toutes les fonctionnalités JavaScript nécessaires
 * pour le fonctionnement du panneau d'administration, en particulier
 * pour la section communications.
 */

// Attendre que le document soit prêt
/**
 * ColisFast Admin Panel JavaScript
 * 
 * Ce fichier contient toutes les fonctionnalités JavaScript nécessaires
 * pour le fonctionnement du panneau d'administration, en particulier
 * pour la section communications.
 */

$(document).ready(function() {
    // Variables globales pour le chat
    let currentReceiverId = null;
    let currentReceiverType = null;
    let lastMessageId = 0;
    let messagePollingInterval = null;
    let currentUserId = $('meta[name="user-id"]').attr('content');

    // Gestion du clic sur un client
    $(document).on('click', '.conversation-item', function(e) {
        e.preventDefault();
        const receiverId = $(this).data('receiverId'); // Utiliser camelCase pour la récupération
        
        console.log("Clic sur client détecté:", receiverId);
        
        if (!receiverId) {
            console.error("L'attribut data-receiver-id est manquant ou vide");
            alert("Impossible de charger cette conversation: identifiant manquant");
            return;
        }
        
        selectRecipient($(this), 'client');
    });

    // Gestion du clic sur un livreur
    $(document).on('click', '.livreur-item', function(e) {
        e.preventDefault();
        const livreurId = $(this).data('livreurId'); // Utiliser camelCase pour la récupération
        
        console.log("Clic sur livreur détecté:", livreurId);
        
        if (!livreurId) {
            console.error("L'attribut data-livreur-id est manquant ou vide");
            alert("Impossible de charger cette conversation: identifiant manquant");
            return;
        }
        
        selectRecipient($(this), 'livreur');
    });

    // Fonction pour sélectionner un destinataire
    function selectRecipient(element, type) {
        console.log("selectRecipient appelé avec type:", type);
        
        // Enlever la sélection actuelle
        $('.conversation-item, .livreur-item').removeClass('active');
        
        // Ajouter la classe active
        element.addClass('active');
        
        // Définir le destinataire avec plus de logging
        if (type === 'client') {
            currentReceiverId = element.data('receiverId'); // camelCase
            currentReceiverType = 'App\\Models\\User';
        } else {
            currentReceiverId = element.data('livreurId'); // camelCase
            currentReceiverType = 'App\\Models\\Livreur';
        }
        
        // Vérification de sécurité pour l'ID
        if (!currentReceiverId) {
            console.error("Impossible de récupérer l'ID du destinataire");
            alert("Erreur: Identifiant du destinataire manquant");
            return;
        }
        
        console.log("ID sélectionné:", currentReceiverId);
        console.log("Type sélectionné:", currentReceiverType);
        
        // Mettre à jour le formulaire
        $('#receiver_id').val(currentReceiverId);
        $('#receiver_type').val(currentReceiverType);
        
        console.log("Champs cachés mis à jour:", 
                    "ID:", $('#receiver_id').val(),
                    "Type:", $('#receiver_type').val());
        
        // Mettre à jour le titre
        const name = element.find('strong').text();
        $('#conversation-title').text('Messages avec ' + name);
        
        // Charger les messages
        console.log("Chargement de la conversation...");
        loadConversation(currentReceiverId, currentReceiverType);
        
        // Reset the unread count for this conversation
        element.find('.unread-count').text('0');
        
        // Arrêter l'intervalle existant avant d'en créer un nouveau
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        
        // Commencer à vérifier les nouveaux messages
        messagePollingInterval = setInterval(checkForNewMessages, 5000);
        
        // Afficher les détails du livreur si c'est un livreur
        if (type === 'livreur') {
            try {
                loadLivreurDetails(currentReceiverId);
                $('#livreur-info-panel').show();
            } catch (e) {
                console.error('Erreur lors du chargement des détails du livreur', e);
                $('#livreur-info-panel').hide();
            }
        } else {
            $('#livreur-info-panel').hide();
        }
    }

    // Fonction pour charger une conversation
    function loadConversation(receiverId, receiverType) {
        console.log("LoadConversation appelée avec:", receiverId, receiverType);
        
        if (!receiverId || !receiverType) {
            console.error("Pas d'ID ou de type de destinataire!");
            $('#chat-messages').html(`
                <div class="alert" style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; border-radius: 8px; padding: 16px;">
                    <p>Impossible de charger la conversation</p>
                    <small>Veuillez sélectionner un destinataire valide</small>
                </div>
            `);
            return;
        }
        
        // Montrer un indicateur de chargement
        $('#chat-messages').html('<div class="text-center my-5"><div class="spinner-border" style="color: #6c757d;" role="status"></div><p class="mt-2" style="color: #6c757d;">Chargement des messages...</p></div>');
        
        // Données pour la requête
        const requestData = {
            receiver_id: receiverId,
            receiver_type: receiverType
        };
        
        console.log("Données envoyées à l'API:", requestData);
        
        $.ajax({
            url: "/admin/communications/conversation",
            method: 'GET',
            data: requestData,
            success: function(response) {
                console.log("Réponse complète reçue:", response);
                
                if (response.communications) {
                    displayMessages(response.communications);
                    
                    // Obtenir l'ID du dernier message pour le polling
                    if (response.communications && response.communications.length > 0) {
                        lastMessageId = response.communications[response.communications.length - 1].id;
                        console.log("Dernier ID message:", lastMessageId);
                    } else {
                        lastMessageId = 0;
                        console.log("Aucun message dans cette conversation");
                    }
                } else {
                    console.error("La réponse n'a pas le format attendu:", response);
                    $('#chat-messages').html(`
                        <div class="alert" style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; border-radius: 8px; padding: 16px;">
                            <p>Format de réponse incorrect</p>
                            <small>Veuillez contacter l'administrateur système</small>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de chargement de la conversation', xhr.status, error);
                console.error('Réponse:', xhr.responseText);
                $('#chat-messages').html(`
                    <div class="alert" style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 16px;">
                        <p>Erreur lors du chargement des messages (${xhr.status})</p>
                        <small>${error}</small>
                    </div>
                `);
            }
        });
    }

    // Fonction pour afficher les messages
    function displayMessages(messages) {
        let html = '';
        
        if (!messages || messages.length === 0) {
            html = `
                <div class="text-center py-5" style="color: #6c757d;">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>Aucun message dans cette conversation</p>
                </div>
            `;
        } else {
            messages.forEach(msg => {
                const isSender = msg.sender_type.includes('User') && msg.sender_id == currentUserId;
                const messageTime = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Couleurs douces : gris clair pour l'expéditeur, vert pâle pour le destinataire
                const bgColor = isSender ? '#f8f9fa' : '#e8f5e8';
                const textColor = isSender ? '#495057' : '#2d5a2d';
                const borderColor = isSender ? '#dee2e6' : '#c3e6c3';
                
                html += `
                    <div class="mb-3 p-3 fade-in" 
                         style="max-width: 80%; ${isSender ? 'margin-left: auto;' : ''} 
                                background-color: ${bgColor}; 
                                color: ${textColor}; 
                                border: 1px solid ${borderColor}; 
                                border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>${isSender ? 'Vous' : (msg.sender_type.includes('User') ? 'Client' : 'Livreur')}</strong>
                            <small style="color: #6c757d;">${messageTime}</small>
                        </div>
                        <p class="mb-0">${msg.message}</p>
                    </div>
                `;
            });
        }
        
        $('#chat-messages').html(html);
        scrollChatToBottom();
    }

    // Fonction pour faire défiler le chat vers le bas
    function scrollChatToBottom() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    // Fonction pour envoyer un message
    $(document).on('submit', '#message-form', function(e) {
        e.preventDefault();
        
        console.log("Tentative d'envoi de message");
        console.log("ID:", currentReceiverId);
        console.log("Type:", currentReceiverType);
        console.log("Hidden ID:", $('#receiver_id').val());
        console.log("Hidden Type:", $('#receiver_type').val());
        
        // Vérifier si les informations du destinataire sont définies
        if (!$('#receiver_id').val() || !$('#receiver_type').val()) {
            // Essayer de les définir à nouveau à partir des variables globales
            if (currentReceiverId && currentReceiverType) {
                $('#receiver_id').val(currentReceiverId);
                $('#receiver_type').val(currentReceiverType);
            } else {
                alert('Veuillez sélectionner un destinataire avant d\'envoyer un message');
                return;
            }
        }
        
        const messageText = $('#message-input').val().trim();
        if (!messageText) {
            alert('Veuillez entrer un message');
            return;
        }
        
        // Désactiver le bouton d'envoi pour éviter les soumissions multiples
        const submitButton = $(this).find('button[type="submit"]');
        const originalText = submitButton.html();
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Envoi...');
        
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
                console.log("Réponse d'envoi de message:", response);
                
                // Réactiver le bouton d'envoi
                submitButton.prop('disabled', false).html(originalText);
                
                if (response.success) {
                    // Effacer l'input
                    $('#message-input').val('');
                    
                    // Ajouter le message au chat
                    const msg = response.communication;
                    const messageTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    const html = `
                        <div class="mb-3 p-3 fade-in" 
                             style="max-width: 80%; margin-left: auto; 
                                    background-color: #f8f9fa; 
                                    color: #495057; 
                                    border: 1px solid #dee2e6; 
                                    border-radius: 12px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>Vous</strong>
                                <small style="color: #6c757d;">${messageTime}</small>
                            </div>
                            <p class="mb-0">${msg.message}</p>
                        </div>
                    `;
                    
                    $('#chat-messages').append(html);
                    scrollChatToBottom();
                    
                    // Mettre à jour l'ID du dernier message
                    lastMessageId = msg.id;
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur d\'envoi de message', xhr.status, error);
                console.error('Réponse complète:', xhr.responseText);
                alert('Erreur lors de l\'envoi du message: ' + error);
                
                // Réactiver le bouton d'envoi même en cas d'erreur
                submitButton.prop('disabled', false).html(originalText);
            }
        });
    });

    // Fonction pour vérifier les nouveaux messages
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
                    console.log("Nouveaux messages reçus:", response.communications.length);
                    
                    // Ajouter les nouveaux messages au chat
                    response.communications.forEach(msg => {
                        const isSender = msg.sender_type.includes('User') && msg.sender_id == currentUserId;
                        const messageTime = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        // Couleurs douces
                        const bgColor = isSender ? '#f8f9fa' : '#e8f5e8';
                        const textColor = isSender ? '#495057' : '#2d5a2d';
                        const borderColor = isSender ? '#dee2e6' : '#c3e6c3';
                        
                        const html = `
                            <div class="mb-3 p-3 fade-in" 
                                 style="max-width: 80%; ${isSender ? 'margin-left: auto;' : ''} 
                                        background-color: ${bgColor}; 
                                        color: ${textColor}; 
                                        border: 1px solid ${borderColor}; 
                                        border-radius: 12px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>${isSender ? 'Vous' : (msg.sender_type.includes('User') ? 'Client' : 'Livreur')}</strong>
                                    <small style="color: #6c757d;">${messageTime}</small>
                                </div>
                                <p class="mb-0">${msg.message}</p>
                            </div>
                        `;
                        
                        $('#chat-messages').append(html);
                    });
                    
                    // Mettre à jour l'ID du dernier message
                    lastMessageId = response.communications[response.communications.length - 1].id;
                    
                    // Défiler vers le bas
                    scrollChatToBottom();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de vérification des nouveaux messages', xhr.status, error);
                console.error('Réponse:', xhr.responseText);
            }
        });
    }

    // Fonction pour charger les détails d'un livreur
    function loadLivreurDetails(livreurId) {
        console.log("Chargement des détails pour le livreur ID:", livreurId);
        
        if (!livreurId) {
            console.error("ID livreur manquant");
            $('#livreur-info-content').html('<div class="alert" style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; border-radius: 8px; padding: 16px;">ID livreur manquant</div>');
            return;
        }
        
        $.ajax({
            url: `/admin/livreurs/${livreurId}/json`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log("Réponse détails livreur:", response);
                
                if (response && response.livreur) {
                    const livreur = response.livreur;
                    let html = `
                        <div class="card-body">
                            <h5 class="card-title" style="color: #495057;">${livreur.name}</h5>
                            <p class="card-text" style="color: #6c757d;"><i class="fas fa-phone me-2"></i>${livreur.phone || 'Non renseigné'}</p>
                            <p class="card-text" style="color: #6c757d;"><i class="fas fa-envelope me-2"></i>${livreur.email}</p>
                            <p class="card-text">
                                <i class="fas fa-circle me-2 ${livreur.is_active ? 'text-success' : 'text-muted'}"></i>
                                <span style="color: ${livreur.is_active ? '#28a745' : '#6c757d'};">${livreur.is_active ? 'Actif' : 'Inactif'}</span>
                            </p>
                            <hr style="border-color: #dee2e6;">
                            <p class="card-text"><small style="color: #6c757d;">Livraisons effectuées: ${livreur.livraisons_count || 0}</small></p>
                            <p class="card-text"><small style="color: #6c757d;">Évaluation moyenne: ${livreur.rating_avg || 'Aucune'}</small></p>
                        </div>
                    `;
                    $('#livreur-info-content').html(html);
                } else {
                    $('#livreur-info-content').html('<div class="alert" style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; border-radius: 8px; padding: 16px;">Informations livreur incomplètes</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de chargement des détails du livreur', xhr.status, error);
                console.error('Réponse:', xhr.responseText);
                $('#livreur-info-content').html(`<div class="alert" style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 8px; padding: 16px;">Erreur lors du chargement des détails (${xhr.status})</div>`);
            }
        });
    }

    // Gestion des notifications
    $(document).on('click', '.notification-item', function() {
        const notificationId = $(this).data('id');
        console.log("Notification cliquée:", notificationId);
        
        // Marquer la notification comme lue
        $.ajax({
            url: `/admin/notifications/mark-read/${notificationId}`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                // Supprimer le style bold
                $(`[data-id="${notificationId}"]`).removeClass('fw-bold bg-light');
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du marquage de notification comme lue', xhr.status, error);
                console.error('Réponse:', xhr.responseText);
            }
        });
    });

    // Marquer toutes les notifications comme lues
    $(document).on('click', '#mark-all-read', function() {
        console.log("Marquer toutes les notifications comme lues");
        
        $.ajax({
            url: "/admin/notifications/mark-all-read",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log("Toutes les notifications marquées comme lues:", response);
                // Supprimer le style bold de toutes les notifications
                $('.notification-item').removeClass('fw-bold bg-light');
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du marquage de toutes les notifications comme lues', xhr.status, error);
                console.error('Réponse:', xhr.responseText);
            }
        });
    });

    // Supprimer une notification - Modifié avec prévention de la propagation
    $(document).on('click', '.notification-delete', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const notificationId = $(this).data('id');
        const notificationElement = $(this).closest('[data-id]'); // Meilleure sélection de l'élément
        const deleteButton = $(this); // Référence au bouton
        
        console.log("Tentative de suppression de notification:", notificationId);
        
        if (confirm('Voulez-vous supprimer cette notification ?')) {
            // Ajout d'un indicateur de chargement
            deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: `/admin/notifications/delete/${notificationId}`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function(response) {
                    console.log("Notification supprimée avec succès:", response);
                    // Animation de disparition plus fluide
                    notificationElement.fadeOut(300, function() {
                        $(this).remove();
                        // Optionnel : Mettre à jour le compteur de notifications
                        updateNotificationCount();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la suppression:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    // Réactiver le bouton
                    deleteButton.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    
                    // Message d'erreur plus informatif
                    let errorMessage = "Erreur lors de la suppression";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ": " + xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    // S'assurer que le bouton est réactivé dans tous les cas
                    deleteButton.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

    // Fonction optionnelle pour mettre à jour le compteur
    function updateNotificationCount() {
        const countElement = $('.notification-count');
        if (countElement.length) {
            const currentCount = parseInt(countElement.text()) || 0;
            countElement.text(Math.max(0, currentCount - 1));
        }
    }
    
    // Nouvelle fonction pour rafraîchir les compteurs de messages non lus
    function refreshUnreadCounts() {
        $.ajax({
            url: "/admin/communications/unread-counts",
            method: 'GET',
            success: function(response) {
                console.log("Réponse des compteurs non lus:", response);
                if (response.counts) {
                    // Mettre à jour chaque compteur utilisateur
                    Object.keys(response.counts).forEach(function(userId) {
                        const count = response.counts[userId];
                        $(`span.unread-count[data-user-id="${userId}"]`).text(count);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur de récupération des compteurs de messages non lus', xhr.status, error);
            }
        });
    }

    // Appeler cette fonction périodiquement pour maintenir les compteurs à jour
    setInterval(refreshUnreadCounts, 10000);
    
    // Appeler immédiatement pour initialiser les compteurs
    refreshUnreadCounts();
});
</script>
@endsection

@section('styles')
<style>
    .chat-container {
        background-color: #f8f9fa;
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
    
    .conversation-item.active {
        background-color: #e9ecef;
        border-left: 3px solid #0d6efd;
    }
    
    .livreur-item.active {
        background-color: #e9ecef;
        border-left: 3px solid #0d6efd;
    }
</style>
@endsection