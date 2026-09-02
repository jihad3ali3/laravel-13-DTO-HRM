<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notification\NotificationSendTestRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService) {}

    public function channels(): JsonResponse
    {
        $channels = $this->notificationService->getChannels();

        return ApiResponse::success($channels, 'Notification channels retrieved successfully.');
    }

    public function index(): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->employee) {
            return ApiResponse::error('Employee profile not found.', 404);
        }

        $notifications = $this->notificationService->getNotifications($user->employee->id);

        return ApiResponse::success($notifications, 'Notifications retrieved successfully.');
    }

    public function markAllRead(): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->employee) {
            return ApiResponse::error('Employee profile not found.', 404);
        }

        $this->notificationService->markAllRead($user->employee->id);

        return ApiResponse::success(null, 'All notifications marked as read.');
    }

    public function markAsRead(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->employee) {
            return ApiResponse::error('Employee profile not found.', 404);
        }

        try {
            $notification = $this->notificationService->markAsRead($id, $user->employee->id);

            return ApiResponse::success($notification, 'Notification marked as read.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function sendTest(NotificationSendTestRequest $request): JsonResponse
    {
        $notification = $this->notificationService->sendTest($request->toDto());

        return ApiResponse::created($notification, 'Test notification dispatched successfully.');
    }
}
