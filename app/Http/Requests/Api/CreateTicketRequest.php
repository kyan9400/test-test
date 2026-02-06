<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
            'subject' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string', 'max:5000'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone must be in E.164 format (e.g., +1234567890).',
            'files.max' => 'You can upload up to 5 files.',
            'files.*.max' => 'Each file must not exceed 10MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone number',
            'subject' => 'subject',
            'text' => 'message',
        ];
    }
}
