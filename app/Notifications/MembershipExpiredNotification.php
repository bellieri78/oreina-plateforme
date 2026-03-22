<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Membership $membership,
        public int $daysSinceExpiration
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $member = $this->membership->member;
        $expirationDate = $this->membership->end_date->format('d/m/Y');
        $renewalUrl = config('app.url') . '/adhesion';

        return (new MailMessage)
            ->subject('Votre adhesion OREINA a expire - Renouvelez maintenant')
            ->greeting("Bonjour {$member->first_name},")
            ->line("Votre adhesion a OREINA a expire le **{$expirationDate}**.")
            ->line("Vous n'avez plus acces aux avantages reserves aux adherents :")
            ->line('- Revue scientifique OREINA')
            ->line('- Sorties de terrain reservees aux membres')
            ->line('- Bases de donnees BDC et Artemisiae')
            ->line('Renouvelez des maintenant pour retrouver tous vos avantages et continuer a soutenir notre action pour les Lepidopteres de France.')
            ->action('Renouveler mon adhesion', $renewalUrl)
            ->line('Nous esperons vous revoir tres bientot parmi nous !')
            ->salutation('Cordialement, L\'equipe OREINA');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'membership_id' => $this->membership->id,
            'days_since_expiration' => $this->daysSinceExpiration,
            'expiration_date' => $this->membership->end_date->toDateString(),
        ];
    }
}
