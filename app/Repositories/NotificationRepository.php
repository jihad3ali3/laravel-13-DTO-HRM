<?php

namespace App\Repositories;

use App\Domain\Notification\NotificationSendTestDto;
use App\Exceptions\RepositoryException;
use App\Models\Notification;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NotificationRepository
{
    public function getChannels(): array
    {
        return [
            ['id' => 'database', 'name' => 'In-app Notification Center', 'enabled' => true],
            ['id' => 'email', 'name' => 'Email Alerts', 'enabled' => true],
            ['id' => 'sms', 'name' => 'SMS Texts', 'enabled' => false],
            ['id' => 'slack', 'name' => 'Slack Workplace Bot', 'enabled' => false],
        ];
    }

    public function getNotifications(int $employeeId)
    {
        return Notification::where('employee_id', $employeeId)->latest()->get();
    }

    public function markAllRead(int $employeeId): void
    {
        Notification::where('employee_id', $employeeId)
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => Carbon::now(),
            ]);
    }

    /**
     * @throws RepositoryException
     */
    public function markAsRead(int $id, int $employeeId): Notification
    {
        $notification = Notification::where('id', $id)
            ->where('employee_id', $employeeId)
            ->first();

        if (! $notification) {
            throw new RepositoryException('Notification not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $notification->update([
            'status' => 'read',
            'read_at' => Carbon::now(),
        ]);

        return $notification;
    }

    public function sendTest(NotificationSendTestDto $dto): Notification
    {
        return Notification::create([
            'employee_id' => $dto->employeeId,
            'type' => $dto->type,
            'title' => $dto->title,
            'message' => $dto->message,
            'channel' => $dto->channel,
            'status' => 'pending',
        ]);
    }
}
