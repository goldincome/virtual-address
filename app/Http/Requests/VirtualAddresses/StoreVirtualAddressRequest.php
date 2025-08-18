<?php
namespace App\Http\Requests\VirtualAddresses;


use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVirtualAddressRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('plans')->ignore($this->plan), 
            ],
            'is_active' => 'required|boolean',
            //'yearly_monthly_price' => 'required|numeric|min:0|gte:price',
            'main_product_image' => 'required|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'additional_images' => 'nullable|array|max:5',
            'additional_images.*' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048',
            'features' => 'required|array|min:1',
            //'features.*.name' => 'required|string|max:255',
            //'features.*.value' => 'required|string|max:255',
            'features.*.is_active' => 'required|boolean',
            'features.*.icon_class' => 'required|string|max:255',
        ];
    }
}
