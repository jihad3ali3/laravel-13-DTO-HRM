<?php

namespace App\Http\Controllers\Api\V1\Holiday;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Holiday\HolidayStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\HolidayService;
use Illuminate\Http\JsonResponse;

class HolidayController extends Controller
{
    public function __construct(private readonly HolidayService $holidayService) {}

    public function index(): JsonResponse
    {
        $holidays = $this->holidayService->getHolidays();

        return ApiResponse::success($holidays, 'Holidays retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $holiday = $this->holidayService->show($id);

            return ApiResponse::success($holiday, 'Holiday retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(HolidayStoreRequest $request): JsonResponse
    {
        $holiday = $this->holidayService->store($request->toDto());

        return ApiResponse::created($holiday, 'Holiday created successfully.');
    }

    public function update(int $id, HolidayStoreRequest $request): JsonResponse
    {
        try {
            $holiday = $this->holidayService->update($id, $request->toDto());

            return ApiResponse::success($holiday, 'Holiday updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->holidayService->destroy($id);

            return ApiResponse::success(null, 'Holiday deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
