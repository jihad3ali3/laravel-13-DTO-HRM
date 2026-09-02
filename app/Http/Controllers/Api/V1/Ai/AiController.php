<?php

namespace App\Http\Controllers\Api\V1\Ai;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Services\AiService;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private readonly AiService $aiService) {}

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $result = $this->aiService->chat($request->input('message'));

        return ApiResponse::success($result, 'AI chat assistant response.');
    }

    public function scanReceipt(Request $request): JsonResponse
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = $request->file('receipt')->store('temp_scan', 'public');
        $result = $this->aiService->scanReceipt($filePath);

        return ApiResponse::success($result, 'Receipt scanned successfully.');
    }

    public function generateReview(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_cycle' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $employee = Employee::find($request->input('employee_id'));
        $name = $employee ? $employee->first_name.' '.$employee->last_name : 'Employee';

        $result = $this->aiService->generateReview($name, $request->input('review_cycle'), (int) $request->input('rating'));

        return ApiResponse::success($result, 'AI generated review draft successfully.');
    }

    public function parseResume(Request $request): JsonResponse
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $filePath = $request->file('resume')->store('temp_resumes', 'public');
        $result = $this->aiService->parseResume($filePath);

        return ApiResponse::success($result, 'Resume parsed successfully.');
    }

    public function craftAnnouncement(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'priority' => 'required|string|in:low,medium,high',
            'instructions' => 'nullable|string',
        ]);

        $result = $this->aiService->craftAnnouncement(
            $request->input('title'),
            $request->input('priority'),
            $request->input('instructions')
        );

        return ApiResponse::success($result, 'Announcement crafted successfully.');
    }
}
