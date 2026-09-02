<?php

namespace App\Http\Controllers\Api\V1\Announcement;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Announcement\AnnouncementFilterRequest;
use App\Http\Requests\Api\V1\Announcement\AnnouncementStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function __construct(private readonly AnnouncementService $announcementService) {}

    public function index(AnnouncementFilterRequest $request): JsonResponse
    {
        $announcements = $this->announcementService->getAnnouncements($request->toDto());

        return ApiResponse::success($announcements, 'Announcements retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $announcement = $this->announcementService->show($id);

            return ApiResponse::success($announcement, 'Announcement details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(AnnouncementStoreRequest $request): JsonResponse
    {
        $announcement = $this->announcementService->store($request->toDto());

        return ApiResponse::created($announcement, 'Announcement created successfully.');
    }

    public function update(int $id, AnnouncementStoreRequest $request): JsonResponse
    {
        try {
            $announcement = $this->announcementService->update($id, $request->toDto());

            return ApiResponse::success($announcement, 'Announcement updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->announcementService->destroy($id);

            return ApiResponse::success(null, 'Announcement deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
