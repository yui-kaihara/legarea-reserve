<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Guest $guest)
    {
        $this->guest = $guest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {   
        return new Envelope(
            subject: '申込み完了通知/LEGAREA第'.$this->guest->times.'回交流会',
            from: env('MAIL_FROM_ADDRESS'),
            to: $this->guest->email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.contact'
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
