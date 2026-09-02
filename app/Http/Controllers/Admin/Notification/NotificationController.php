<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notification\NotificationSendTestRequest;
use App\Http\Services\NotificationService;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService) {}

    public function index(): View
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        $employeeId = $employee ? $employee->id : 1;

        $notifications = Notification::with('employee')
            ->latest('id')
            ->paginate(20);

        $channels = $this->notificationService->getChannels();
        $employees = Employee::active()->orderBy('first_name')->get();
        $unreadCount = Notification::whereNull('read_at')->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'channels',
            'employees',
            'unreadCount'
        ));
    }

    public function markAllRead(): RedirectResponse
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function sendTest(NotificationSendTestRequest $request): RedirectResponse
    {
        $this->notificationService->sendTest($request->toDto());

        return redirect()->back()->with('success', 'Notification dispatched successfully.');
    }
}
