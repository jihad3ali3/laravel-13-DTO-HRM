<?php

namespace App\Http\Requests\Api\V1\Notification;

use App\Domain\Notification\NotificationSendTestDto;
use Illuminate\Foundation\Http\FormRequest;

class NotificationSendTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'channel' => 'required|string|in:database,email,sms,slack',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): NotificationSendTestDto
    {
        return new NotificationSendTestDto(
            employeeId: (int) $this->input('employee_id'),
            type: $this->input('type'),
            title: $this->input('title'),
            message: $this->input('message'),
            channel: $this->input('channel', 'database')
        );
    }
}
