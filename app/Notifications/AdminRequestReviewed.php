<?php

namespace App\Notifications;

use App\Models\AdminRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminRequestReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public AdminRequest $adminRequest
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Your request was reviewed',
            'type' => $this->adminRequest->type,
            'status' => $this->adminRequest->status,
            'request_id' => $this->adminRequest->id,
        ];
    }
}

