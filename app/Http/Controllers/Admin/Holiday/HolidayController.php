<?php

namespace App\Http\Controllers\Admin\Holiday;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Holiday\HolidayStoreRequest;
use App\Http\Services\HolidayService;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function __construct(private readonly HolidayService $holidayService) {}

    public function index(Request $request): View
    {
        $selectedYear = (int) $request->input('year', Carbon::now()->year);
        $holidays = Holiday::where('holiday_year', $selectedYear)
            ->orderBy('holiday_date', 'asc')
            ->paginate(20);

        return view('admin.holidays.index', compact('holidays', 'selectedYear'));
    }

    public function store(HolidayStoreRequest $request): RedirectResponse
    {
        $this->holidayService->store($request->toDto());

        return redirect()->back()->with('success', 'Holiday created successfully.');
    }

    public function update(int $id, HolidayStoreRequest $request): RedirectResponse
    {
        $this->holidayService->update($id, $request->toDto());

        return redirect()->back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->holidayService->destroy($id);

        return redirect()->back()->with('success', 'Holiday deleted successfully.');
    }
}
