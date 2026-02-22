<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class InternshipAppliedNotification extends Notification
{
    use Queueable;

    protected $internship;
    protected $freelancer;
    public function __construct($internship, $freelancer)
    {
        $this->internship = $internship;
        $this->freelancer = $freelancer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): void
    {
        DB::table('notifications')->insert([
        'user_id' => $notifiable->id,
        'type' => 'internship_application',
        'title' => 'New application',
        'body' => $this->freelancer->name . ' applied to your internship: ' . $this->internship->title,
        'link' => '/internships/' . $this->internship->id,
        'related_id' => $this->internship->id,
        'is_read' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
