<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpeningDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'duration_ids' => ['required', 'array'],
            'duration_ids.*' => ['required', 'integer', 'exists:opening_days,id'],
            'day_name' => ['nullable', 'array'],
            'open_time' => ['required', 'array'],
            'open_time.*' => ['required'],
            'close_time' => ['required', 'array'],
            'close_time.*' => ['required', 'after:open_time.*'],
        ];
    }
}
