@csrf
<div class="space-y-6">
    <div>
        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Select Room</label>
        <select id="product_id" name="product_id" required class="w-full px-3 py-2 border @error('product_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-orange-500 focus:border-orange-500">
            <option value="">-- Choose a room --</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" {{ old('product_id', $planRoomDiscount->product_id ?? '') == $room->id ? 'selected' : '' }}>
                    {{ $room->name }} ({{ $room->type->label() }})
                </option>
            @endforeach
        </select>
        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-1">Select Plan</label>
        <select id="plan_id" name="plan_id" required class="w-full px-3 py-2 border @error('plan_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-orange-500 focus:border-orange-500">
            <option value="">-- Choose a plan --</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ old('plan_id', $planRoomDiscount->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                    {{ $plan->name }}
                </option>
            @endforeach
        </select>
        @error('plan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
            <select id="discount_type" name="discount_type" required class="w-full px-3 py-2 border @error('discount_type') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-orange-500 focus:border-orange-500">
                @foreach($discountTypes as $type)
                    <option value="{{ $type->value }}" {{ old('discount_type', $planRoomDiscount->discount_type->value ?? '') == $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            @error('discount_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
            <input type="number" step="0.01" id="discount_value" name="discount_value" value="{{ old('discount_value', $planRoomDiscount->discount_value ?? '') }}" required class="w-full px-3 py-2 border @error('discount_value') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., 10 or 15.50">
            @error('discount_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select id="is_active" name="is_active" required class="w-full px-3 py-2 border @error('is_active') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-orange-500 focus:border-orange-500">
            <option value="1" {{ old('is_active', $planRoomDiscount->is_active ?? '1') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $planRoomDiscount->is_active ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('is_active') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
