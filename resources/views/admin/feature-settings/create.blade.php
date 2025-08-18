@extends('layouts.admin')

@section('title', 'Create New Feature Setting')

@section('css')
<style>
    /* Icon Picker Modal Styles */
    .icon-picker-modal {
        max-height: 70vh; /* Limit height */
    }
    .icon-picker-list {
        max-height: calc(70vh - 150px); /* Adjust based on header/footer of modal */
        overflow-y: auto;
    }
    .icon-picker-item {
        cursor: pointer;
        padding: 0.75rem;
        border-radius: 0.375rem; /* rounded-md */
        transition: background-color 0.2s ease-in-out;
    }
    .icon-picker-item:hover {
        background-color: #f3f4f6; /* gray-100 */
    }
    .icon-picker-item i {
        font-size: 1.5rem; /* text-2xl */
        width: 2.5rem; /* w-10 for alignment */
        text-align: center;
    }
    #icon-preview {
        font-size: 1.5rem; /* text-2xl */
        min-width: 2.5rem; /* Ensure space even if icon is empty */
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-blue-700 text-white shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-tools mr-2"></i> NURUD Admin
            </a>
            <a href="{{ route('admin.feature-settings.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                <i class="fas fa-arrow-left mr-1"></i> Back to Feature Settings
            </a>
        </nav>
    </header>

    <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-blue-800">Create New Feature Setting</h2>
            </div>

            <form method="POST" action="{{ route('admin.feature-settings.store') }}" class="p-8 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Feature Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                           placeholder="e.g., High-Speed Wi-Fi">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Feature Description <span class="text-red-500">*</span></label>
                    <input type="text" id="description" name="description" value="{{ old('description') }}" 
                           class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                           placeholder="Very Good High-Speed Wi-Fi">
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Input with Picker --}}
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon (CSS Class) <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-3">
                        <input type="text" id="icon" name="icon" value="{{ old('icon') }}" required
                               class="flex-grow px-4 py-2 border @error('icon') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out"
                               placeholder="e.g., fas fa-wifi">
                        <div id="icon-preview" class="p-2 border border-gray-300 rounded-lg bg-gray-50 flex items-center justify-center">
                            {{-- Preview will appear here --}}
                        </div>
                        <button type="button" id="open-icon-picker" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out text-sm">
                            <i class="fas fa-icons mr-1"></i> Select
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Enter a Font Awesome class (e.g., `fas fa-wifi`).</p>
                    @error('icon')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i> Create Feature Setting
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

{{-- Icon Picker Modal --}}
<div id="iconPickerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50 hidden">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white icon-picker-modal">
        <div class="flex justify-between items-center pb-3 border-b">
            <p class="text-xl font-semibold text-blue-800">Select an Icon</p>
            <button id="close-icon-picker" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="my-4">
            <input type="text" id="icon-search" placeholder="Search icons by name..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div id="icon-picker-list" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 text-center icon-picker-list">
            {{-- Icons will be loaded here by JavaScript --}}
            <p class="col-span-full text-gray-500">Loading icons...</p>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');
    const openIconPickerButton = document.getElementById('open-icon-picker');
    const closeIconPickerButton = document.getElementById('close-icon-picker');
    const iconPickerModal = document.getElementById('iconPickerModal');
    const iconSearchInput = document.getElementById('icon-search');
    const iconPickerList = document.getElementById('icon-picker-list');

    let allIcons = []; // To store all loaded icons

    // Function to update icon preview
    function updateIconPreview(iconClass) {
        if (iconPreview) {
            if (iconClass && iconClass.trim() !== '') {
                iconPreview.innerHTML = `<i class="${iconClass.trim()}"></i>`;
            } else {
                iconPreview.innerHTML = ''; // Clear preview if input is empty
            }
        }
    }

    // Initial preview update if there's an old value (e.g., after validation error)
    if (iconInput) {
        updateIconPreview(iconInput.value);

        // Update preview when user types into the input field
        iconInput.addEventListener('input', function () {
            updateIconPreview(this.value);
        });
    }

    // Fetch icons from JSON
    fetch("{{ asset('json/fontawesome-icons.json') }}")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            allIcons = data;
            renderIcons(allIcons);
        })
        .catch(error => {
            console.error("Could not load Font Awesome icons:", error);
            if (iconPickerList) iconPickerList.innerHTML = '<p class="col-span-full text-red-500">Error loading icons. Please check the console.</p>';
        });

    // Function to render icons in the picker
    function renderIcons(iconsToRender) {
        if (!iconPickerList) return;
        iconPickerList.innerHTML = ''; // Clear current icons
        if (iconsToRender.length === 0) {
            iconPickerList.innerHTML = '<p class="col-span-full text-gray-500">No icons found matching your search.</p>';
            return;
        }
        iconsToRender.forEach(icon => {
            const iconDiv = document.createElement('div');
            iconDiv.className = 'icon-picker-item flex flex-col items-center justify-center p-2 hover:bg-gray-100 rounded-md';
            iconDiv.dataset.iconClass = icon.class;
            iconDiv.innerHTML = `<i class="${icon.class} text-2xl mb-1"></i><span class="text-xs text-gray-600 truncate w-full">${icon.name}</span>`;
            iconDiv.addEventListener('click', function () {
                if (iconInput) {
                    iconInput.value = this.dataset.iconClass;
                    updateIconPreview(this.dataset.iconClass); // Update preview
                }
                if (iconPickerModal) iconPickerModal.classList.add('hidden'); // Close modal
            });
            iconPickerList.appendChild(iconDiv);
        });
    }

    // Open Icon Picker Modal
    if (openIconPickerButton && iconPickerModal) {
        openIconPickerButton.addEventListener('click', function () {
            iconPickerModal.classList.remove('hidden');
            renderIcons(allIcons); // Render all icons initially or based on current search
            if (iconSearchInput) iconSearchInput.value = ''; // Clear search on open
            if (iconSearchInput) iconSearchInput.focus();
        });
    }

    // Close Icon Picker Modal
    if (closeIconPickerButton && iconPickerModal) {
        closeIconPickerButton.addEventListener('click', function () {
            iconPickerModal.classList.add('hidden');
        });
    }
    // Close modal on escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && iconPickerModal && !iconPickerModal.classList.contains('hidden')) {
            iconPickerModal.classList.add('hidden');
        }
    });


    // Filter icons on search
    if (iconSearchInput) {
        iconSearchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const filteredIcons = allIcons.filter(icon =>
                icon.name.toLowerCase().includes(searchTerm) ||
                icon.class.toLowerCase().includes(searchTerm)
            );
            renderIcons(filteredIcons);
        });
    }
});
</script>
@endsection
