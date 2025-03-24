<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeddingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $wedding;
    public function __construct($wedding)
    {
        $this->wedding = $wedding;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wedding Created Mail',
        );
    }

    /**
     * Get the message content definition.
     */


    public function build()
    {
        return $this->subject('¡Tu boda ha sido creada!')
            ->view('emails.wedding_created')
            ->with([
                'wedding' => $this->wedding,
                'qrUrl' => asset('storage/qr/qr.png')
            ]);
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
