<?php

namespace App\Http\Requests\MailSettings;

use App\Enums\MailTypeEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMailSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Change to true if you have authorization logic for this route
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'plan_id' => [
                'required',
                'exists:plans,id',
                Rule::unique('mail_settings')->where(function ($query) {
                    return $query->where('mail_type', $this->mail_type);
                }),
            ],
            'mail_type' => [
                'required',
                Rule::in(MailTypeEnum::toArray()),
                Rule::unique('mail_settings')->where(function ($query) {
                    return $query->where('plan_id', $this->plan_id);
                }),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'interval' => ['required', Rule::in(['month', 'year', 'week', 'day'])],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.unique' => 'The selected plan and mail type combination already exists.',
            'mail_type.unique' => 'The selected plan and mail type combination already exists.',
            'plan_id.required' => 'A plan is required.',
            'mail_type.required' => 'A mail type is required.',
            'mail_type.in' => 'The selected mail type is invalid.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'interval.required' => 'The billing interval is required.',
            'interval.in' => 'The selected interval is invalid.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status must be true or false.',
        ];
    }
}