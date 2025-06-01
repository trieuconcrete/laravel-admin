<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ContactReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The contact instance.
     *
     * @var \App\Models\Contact
     */
    public $contact;

    /**
     * The reply message.
     *
     * @var string
     */
    public $replyMessage;

    /**
     * The admin who is replying.
     *
     * @var \App\Models\User
     */
    public $admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contact $contact, string $replyMessage, $admin = null)
    {
        $this->contact = $contact;
        $this->replyMessage = $replyMessage;
        $this->admin = $admin ?: auth()->user();
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@nguyentrieu.site'),
                config('mail.from.name', 'Nguyen Trieu Support')
            ),
            replyTo: [
                new Address(
                    $this->admin->email ?? config('mail.from.address'),
                    $this->admin->name ?? config('mail.from.name')
                ),
            ],
            subject: 'Re: ' . $this->contact->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.contact-reply',
            with: [
                'contactName' => $this->contact->name,
                'originalMessage' => $this->contact->message,
                'replyMessage' => $this->replyMessage,
                'adminName' => $this->admin->name ?? 'Support Team',
                'subject' => $this->contact->subject,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}