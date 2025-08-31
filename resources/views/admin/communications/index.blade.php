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


<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

@endsection

@section('scripts')
    <script src="{{ asset('js/communication.js') }}"></script>
     

@endsection