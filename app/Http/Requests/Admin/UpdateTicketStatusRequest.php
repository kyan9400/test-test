<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'manager']) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::enum(TicketStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.Illuminate\Validation\Rules\Enum' => 'Invalid status value.',
        ];
    }
}
