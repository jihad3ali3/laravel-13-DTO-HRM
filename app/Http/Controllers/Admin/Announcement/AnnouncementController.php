<?php

namespace App\Http\Controllers\Admin\Announcement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Announcement\AnnouncementFilterRequest;
use App\Http\Requests\Api\V1\Announcement\AnnouncementStoreRequest;
use App\Http\Services\AnnouncementService;
use App\Models\Announcement;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(private readonly AnnouncementService $announcementService) {}

    public function index(AnnouncementFilterRequest $request): View
    {
        $announcements = Announcement::with(['department:id,name', 'creator:id,name'])
            ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->department_id))
            ->latest('is_pinned')
            ->latest('start_date')
            ->paginate(12);

        $departments = Department::where('is_active', true)->get();

        $pinnedCount = Announcement::where('is_pinned', true)->count();
        $totalCount = Announcement::where('is_published', true)->count();

        return view('admin.announcements.index', compact('announcements', 'departments', 'pinnedCount', 'totalCount'));
    }

    public function store(AnnouncementStoreRequest $request): RedirectResponse
    {
        $this->announcementService->store($request->toDto());

        return redirect()->back()->with('success', 'Notice published successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->announcementService->destroy($id);

        return redirect()->back()->with('success', 'Notice removed successfully.');
    }
}
