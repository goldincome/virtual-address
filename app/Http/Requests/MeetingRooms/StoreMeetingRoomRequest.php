<?php
namespace App\Http\Requests\MeetingRooms;


use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRoomRequest extends FormRequest
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
            'intro' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
            'main_product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'additional_images' => 'nullable|array|max:5',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features' => 'required|array|min:1',
            //'features.*.description' => 'required|string|max:255',
            //'features.*.value' => 'required|string|max:255',
            'features.*.icon_class' => 'required|string|max:255',
            'features.*.is_active' => 'required|boolean',
        ];
    }
}
