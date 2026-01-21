<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        }
    }

    public function render()
    {
        $notifications = Auth::check() 
            ? Auth::user()->unreadNotifications()->latest()->take(5)->get() 
            : collect([]);

        return view('livewire.partials.notification-bell', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->updateCount();
            
            // Redirect if action_url exists
            if (isset($notification->data['action_url'])) {
                return redirect($notification->data['action_url']);
            }
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->updateCount();
    }
}
