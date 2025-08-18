<?php

namespace App\Http\Requests;

use App\Enums\MailTypeEnum;
use App\Enums\MailStatusEnum;
use Illuminate\Validation\Rule;
use App\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assuming admin users are authorized
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'mail_type' => ['required', Rule::in(MailTypeEnum::toArray())],
            'sender_name' => ['required', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'tracking_url' => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string'],
            'mail_status' => ['required', Rule::in(MailStatusEnum::toArray())],
            'price' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in(PaymentStatusEnum::toArray())],
            'recieved_at' => ['required', 'date'],
            'forwarded_at' => ['nullable', 'date', 'after_or_equal:recieved_at'],
            'scanned_at' => ['nullable', 'date', 'after_or_equal:recieved_at'],
        ];
    }
}
