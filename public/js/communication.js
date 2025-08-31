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