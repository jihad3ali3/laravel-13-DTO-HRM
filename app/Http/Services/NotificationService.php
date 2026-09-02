<?php

namespace App\Http\Services;

use App\Domain\Notification\NotificationSendTestDto;
use App\Repositories\NotificationRepository;

readonly class NotificationService
{
    public function __construct(private NotificationRepository $notificationRepository) {}

    public function getChannels(): array
    {
        return $this->notificationRepository->getChannels();
    }

    public function getNotifications(int $employeeId)
    {
        return $this->notificationRepository->getNotifications($employeeId);
    }

    public function markAllRead(int $employeeId): void
    {
        $this->notificationRepository->markAllRead($employeeId);
    }

    public function markAsRead(int $id, int $employeeId)
    {
        return $this->notificationRepository->markAsRead($id, $employeeId);
    }

    public function sendTest(NotificationSendTestDto $dto)
    {
        return $this->notificationRepository->sendTest($dto);
    }
}
