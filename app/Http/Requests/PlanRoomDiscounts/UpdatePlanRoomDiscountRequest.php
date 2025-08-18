<?php

namespace App\Http\Requests\PlanRoomDiscounts;

use App\Enums\DiscountTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRoomDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Assuming admin is authorized
    }

    public function rules(): array
    {
        $planRoomDiscount = $this->route('plan_room_discount');

        // Ensure that the model was actually found before proceeding.
        if (!$planRoomDiscount) {
            // if the route model binding fails.
            return []; // Return empty rules to prevent further errors.
        }

        return [
            'plan_id' => [
                'required',
                'exists:plans,id',
                Rule::unique('plan_room_discounts')->where(function ($query) {
                    return $query->where('product_id', $this->product_id);
                })->ignore($planRoomDiscount->id),
            ],
            'product_id' => 'required|exists:products,id',
            'discount_type' => ['required', Rule::in(array_column(DiscountTypeEnum::cases(), 'value'))],
            'discount_value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'plan_id.unique' => 'This plan already has a discount for the selected room.',
        ];
    }
}
