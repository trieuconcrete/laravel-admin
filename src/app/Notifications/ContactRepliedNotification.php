<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The contact instance.
     *
     * @var \App\Models\Contact
     */
    protected $contact;

    /**
     * The reply message.
     *
     * @var string
     */
    protected $replyMessage;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Contact $contact, string $replyMessage)
    {
        $this->contact = $contact;
        $this->replyMessage = $replyMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bạn đã phản hồi liên hệ: ' . $this->contact->subject)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Bạn vừa gửi phản hồi cho liên hệ từ ' . $this->contact->name)
            ->line('**Chủ đề:** ' . $this->contact->subject)
            ->line('**Nội dung phản hồi:**')
            ->line($this->replyMessage)
            ->action('Xem chi tiết', url('/admin/contacts/' . $this->contact->id))
            ->line('Cảm ơn bạn đã xử lý liên hệ này!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->name,
            'contact_email' => $this->contact->email,
            'subject' => $this->contact->subject,
            'reply_message' => $this->replyMessage,
            'replied_at' => now(),
        ];
    }
}