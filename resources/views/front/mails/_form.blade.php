<div>
    <label for="mail_type" class="block text-sm font-medium text-gray-700 mb-1">Service Type <span class="text-red-500">*</span></label>
    <select id="mail_type" name="mail_type" required class="w-full px-4 py-2 border @error('mail_type') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        <option value="">Select a service...</option>
        @foreach($mailTypes as $type)
            <option value="{{ $type->value }}" {{ old('mail_type', $mail->mail_type?->value) == $type->value ? 'selected' : '' }}>
                {{ $type->label() }}
            </option>
        @endforeach
    </select>
    @error('mail_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-1">Sender/Courier Name <span class="text-red-500">*</span></label>
        <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name', $mail->sender_name) }}" required class="w-full px-4 py-2 border @error('sender_name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., Amazon, John Doe">
        @error('sender_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number (Optional)</label>
        <input type="text" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $mail->tracking_number) }}" class="w-full px-4 py-2 border @error('tracking_number') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., 1Z9999W99999999999">
        @error('tracking_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="tracking_url" class="block text-sm font-medium text-gray-700 mb-1">Tracking URL (Optional)</label>
    <input type="url" id="tracking_url" name="tracking_url" value="{{ old('tracking_url', $mail->tracking_url) }}" class="w-full px-4 py-2 border @error('tracking_url') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="e.g., https://carrier.com/tracking/...">
    @error('tracking_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description / Instructions <span class="text-red-500">*</span></label>
    <textarea id="description" name="description" rows="5" required class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Provide any details here. For forwarding, please include the full address. For scans, specify which documents.">{{ old('description', $mail->description) }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="pt-3">
    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
        <i class="fas fa-save mr-2"></i> {{ $mail->exists ? 'Update Request' : 'Submit Request' }}
    </button>
</div>

