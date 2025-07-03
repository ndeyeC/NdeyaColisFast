<?php

namespace App\Notifications;

use App\Models\Commnande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportColisfast extends Notification
{
    use Queueable;

    public $commande;

    /**
     * Créer une nouvelle instance de notification.
     */
    public function __construct(Commnande $commande)
    {
        $this->commande = $commande;
    }

    /**
     * Obtenir les canaux de notification.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail']; // Envoi de l'email
    }

    /**
     * Obtenir le message de notification pour la méthode mail.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle commande')
            ->line('Une nouvelle commande a été créée.')
            ->line('Client : ' . $this->commande->user->name)  // Si tu as une relation utilisateur avec la commande
            ->line('Adresse de départ : ' . $this->commande->adresse_depart)
            ->line('Adresse d\'arrivée : ' . $this->commande->adresse_arrivee)
            ->line('Montant : ' . $this->commande->prix_final . ' F')
            ->action('Voir la commande', url('/livreur/commandes/' . $this->commande->id))
            ->line('Merci.');
    }
}
 