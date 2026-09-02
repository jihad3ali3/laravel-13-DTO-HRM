<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\CompanySettingUpdateRequest;
use App\Http\Requests\Api\V1\Setting\DepartmentStoreRequest;
use App\Http\Requests\Api\V1\Setting\DesignationStoreRequest;
use App\Http\Services\SettingService;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService) {}

    public function departments(): View
    {
        $departments = Department::withCount('employees')->paginate(15);

        return view('admin.settings.departments', compact('departments'));
    }

    public function storeDepartment(DepartmentStoreRequest $request): RedirectResponse
    {
        $this->settingService->storeDepartment($request->toDto());

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function updateDepartment(int $id, DepartmentStoreRequest $request): RedirectResponse
    {
        $this->settingService->updateDepartment($id, $request->toDto());

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(int $id): RedirectResponse
    {
        $this->settingService->destroyDepartment($id);

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }

    public function designations(): View
    {
        $designations = Designation::with(['department', 'employees'])->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('admin.settings.designations', compact('designations', 'departments'));
    }

    public function storeDesignation(DesignationStoreRequest $request): RedirectResponse
    {
        $this->settingService->storeDesignation($request->toDto());

        return redirect()->back()->with('success', 'Designation created successfully.');
    }

    public function updateDesignation(int $id, DesignationStoreRequest $request): RedirectResponse
    {
        $this->settingService->updateDesignation($id, $request->toDto());

        return redirect()->back()->with('success', 'Designation updated successfully.');
    }

    public function destroyDesignation(int $id): RedirectResponse
    {
        $this->settingService->destroyDesignation($id);

        return redirect()->back()->with('success', 'Designation deleted successfully.');
    }

    public function company(): View
    {
        $companySettings = $this->settingService->getCompanySettings();

        return view('admin.settings.company', compact('companySettings'));
    }

    public function updateCompany(CompanySettingUpdateRequest $request): RedirectResponse
    {
        $this->settingService->updateCompanySettings($request->toDto());

        return redirect()->back()->with('success', 'Company profile and organization settings updated successfully.');
    }
}
