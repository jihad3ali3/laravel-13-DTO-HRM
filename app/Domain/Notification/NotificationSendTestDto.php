<?php

namespace App\Domain\Notification;

class NotificationSendTestDto
{
    public function __construct(
        public int $employeeId,
        public string $type,
        public string $title,
        public string $message,
        public string $channel = 'database'
    ) {}
}
