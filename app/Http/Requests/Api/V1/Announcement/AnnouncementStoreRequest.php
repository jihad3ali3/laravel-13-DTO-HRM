<?php

namespace App\Http\Requests\Api\V1\Announcement;

use App\Domain\Announcement\AnnouncementStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|string|in:low,medium,high',
            'is_pinned' => 'nullable|boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AnnouncementStoreDto
    {
        return new AnnouncementStoreDto(
            title: $this->input('title'),
            content: $this->input('content'),
            departmentId: $this->input('department_id') ? (int) $this->input('department_id') : null,
            priority: $this->input('priority'),
            isPinned: $this->boolean('is_pinned', false),
            startDate: $this->input('start_date'),
            endDate: $this->input('end_date'),
            isPublished: $this->boolean('is_published', false),
            createdBy: $this->user()?->id
        );
    }
}
