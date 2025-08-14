<?php

namespace App\Mail;

use App\Models\Contingency;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContingencyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contingency;
    public $contingencyUrl;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Contingency $contingency, User $user)
    {
        $this->contingency = $contingency;
        $this->user = $user;
        $this->contingencyUrl = $this->generatePanelUrl($contingency, $user);
    }

    /**
     * Genera la URL del panel correcto según el rol del usuario
     */
    private function generatePanelUrl(Contingency $contingency, User $user): string
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Determinar el panel según el rol del usuario
        if (in_array('super_admin', $userRoles) || in_array('administrador', $userRoles)) {
            // Redirigir al panel de admin para ver la contingencia
            return url("/admin/contingencies/{$contingency->id}");
        } elseif (in_array('operador', $userRoles)) {
            // Redirigir al panel de operator para ver la contingencia
            return url("/operator/contingencies/{$contingency->id}");
        } elseif (in_array('gestor', $userRoles)) {
            // Los gestores van al panel manager con filtro de contingencia
            return url("/manager?filters[contingency_id]={$contingency->id}");
        }
        
        // Por defecto, usar el enlace público del QR
        return url("/contingencias/{$contingency->slug}/qr");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva Contingencia Creada - {$this->contingency->contingency_id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-contingency',
            with: [
                'contingency' => $this->contingency,
                'contingencyUrl' => $this->contingencyUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
