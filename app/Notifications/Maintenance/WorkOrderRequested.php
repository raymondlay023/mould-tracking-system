<?php

namespace App\Notifications\Maintenance;

use App\Models\MaintenanceEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(MaintenanceEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Work Order',
            'body' => "Work Order requested for Mould {$this->event->mould->code}",
            'action_url' => route('maintenance.work-orders'),
            'type' => 'info', // info, warning, error
        ];
    }
}
