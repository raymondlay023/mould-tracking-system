<?php

namespace App\Notifications\Maintenance;

use App\Models\Mould;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PmDueAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $mould;
    public $reason; // 'date' or 'shot'

    /**
     * Create a new notification instance.
     */
    public function __construct(Mould $mould, string $reason = 'shot')
    {
        $this->mould = $mould;
        $this->reason = $reason;
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
        $msg = $this->reason === 'shot' 
            ? "Mould {$this->mould->code} is due for PM (Shot Limit)" 
            : "Mould {$this->mould->code} is due for PM (Date Limit)";

        return [
            'title' => 'PM Alert',
            'body' => $msg,
            'action_url' => route('alerts.pm_due'),
            'type' => 'warning',
        ];
    }
}
