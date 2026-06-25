<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public bool $isMobile = false;

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
        if (!Auth::check()) {
            return;
        }

        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->updateCount();
            
            // Redirect if action_url exists
            if (isset($notification->data['action_url'])) {
                $url = $notification->data['action_url'];
                
                // If the component is rendered inside the mobile layout
                if ($this->isMobile) {
                    // Map desktop URLs to mobile URLs
                    if (str_contains($url, '/maintenance/work-orders')) {
                        if (preg_match('/\/maintenance\/work-orders\/([^\/]+)\/complete/', $url, $matches)) {
                            $url = route('mobile.jobs.complete', ['event' => $matches[1]]);
                        } else {
                            $url = route('mobile.maintenance-tasks');
                        }
                    } elseif (str_contains($url, '/alerts/pm-due')) {
                        $url = route('mobile.dashboard');
                    } elseif (preg_match('/\/moulds\/([a-f0-9\-]{36})/', $url, $matches)) {
                        $url = route('mobile.mould-detail', ['mould' => $matches[1]]);
                    } elseif (str_contains($url, '/dashboard')) {
                        $url = route('mobile.dashboard');
                    } elseif (str_contains($url, '/profile')) {
                        $url = route('mobile.profile.edit');
                    }
                }
                
                return redirect($url);
            }
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->updateCount();
    }
}
