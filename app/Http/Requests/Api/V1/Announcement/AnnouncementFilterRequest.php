<?php

namespace App\Http\Requests\Api\V1\Announcement;

use App\Domain\Announcement\AnnouncementFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AnnouncementFilterDto
    {
        $user = $this->user();
        $publishedOnly = true;
        if ($user && $user->hasPermissionTo('announcements.manage')) {
            $publishedOnly = false;
        }

        $departmentId = $this->input('department_id');
        if ($user && ! $user->hasPermissionTo('announcements.manage') && $user->employee) {
            $departmentId = $user->employee->department_id;
        }

        return new AnnouncementFilterDto(
            departmentId: $departmentId ? (int) $departmentId : null,
            publishedOnly: $publishedOnly
        );
    }
}
