<?php

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Http\Services\AiService;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiController extends Controller
{
    public function __construct(private readonly AiService $aiService) {}

    public function index(): View
    {
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('admin.ai.index', compact('employees'));
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string']);
        $result = $this->aiService->chat($request->message);

        return response()->json($result);
    }

    public function craftAnnouncement(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'priority' => 'required|string',
            'additional_instructions' => 'nullable|string',
        ]);

        $result = $this->aiService->craftAnnouncement(
            $request->title,
            $request->priority,
            $request->additional_instructions
        );

        return response()->json($result);
    }

    public function generateReview(Request $request): JsonResponse
    {
        $request->validate([
            'employee_name' => 'required|string',
            'cycle' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $result = $this->aiService->generateReview(
            $request->employee_name,
            $request->cycle,
            $request->rating
        );

        return response()->json($result);
    }

    public function scanReceipt(Request $request): JsonResponse
    {
        $filePath = null;
        if ($request->hasFile('receipt')) {
            $filePath = $request->file('receipt')->store('temp_ai', 'public');
        }

        $result = $this->aiService->scanReceipt($filePath);

        return response()->json($result);
    }

    public function parseResume(Request $request): JsonResponse
    {
        $filePath = null;
        if ($request->hasFile('resume')) {
            $filePath = $request->file('resume')->store('temp_ai', 'public');
        }

        $result = $this->aiService->parseResume($filePath);

        return response()->json($result);
    }
}
