<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Membership $membership,
        public int $daysRemaining
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

        $message = (new MailMessage)
            ->subject("Votre adhesion OREINA expire dans {$this->daysRemaining} jours")
            ->greeting("Bonjour {$member->first_name},")
            ->line("Votre adhesion a OREINA expire le **{$expirationDate}**.");

        if ($this->daysRemaining <= 7) {
            $message->line("Il ne reste plus que **{$this->daysRemaining} jours** pour renouveler votre adhesion et continuer a beneficier de tous vos avantages.");
        } else {
            $message->line("Pensez a renouveler votre adhesion pour continuer a beneficier de tous vos avantages.");
        }

        return $message
            ->line('**Vos avantages adherent :**')
            ->line('- Acces a la revue scientifique OREINA')
            ->line('- Participation aux sorties de terrain')
            ->line('- Acces aux bases de donnees (BDC, Artemisiae)')
            ->action('Renouveler mon adhesion', $renewalUrl)
            ->line('Merci de votre fidelite et de votre soutien a la connaissance des Lepidopteres de France !')
            ->salutation('Cordialement, L\'equipe OREINA');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'membership_id' => $this->membership->id,
            'days_remaining' => $this->daysRemaining,
            'expiration_date' => $this->membership->end_date->toDateString(),
        ];
    }
}
