@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <header class="bg-blue-700 text-white shadow-sm">
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="#" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                    <i class="fas fa-building mr-2"></i> Charlton Virtual Office Admin
                </a>
                <a href="{{ route('admin.plans.index') }}" class="text-sm hover:text-orange-300 transition duration-300">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Meeting Room
                </a>
            </nav>
        </header>

        <main class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-3xl bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-blue-800">Edit Meeting Room</h2>
                </div>

                <form method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.meeting-rooms.update', $meetingRoom) }}" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $meetingRoom->name) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label for="price"
                                class="block text-sm font-medium text-gray-700 mb-2">Price({{ config('cashier.currency') }})</label>
                            <div class="relative">
                                <input type="number" step="0.01" id="price" name="price"
                                    value="{{ old('price', $meetingRoom->price) }}" required
                                    class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        <div>
                            <label for="intro" class="block text-sm font-medium text-gray-700 mb-2">short intro</label>
                            <textarea id="intro" name="intro" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">{{ old('intro', $meetingRoom->intro) }}</textarea>
                        </div>

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">{{ old('description', $meetingRoom->description) }}</textarea>
                        </div>

                        <!-- Main Image -->
                        {{-- Main Product Image Section --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Main Product Image</label>
                            <div class="mt-1 flex justify-center items-center px-6 pt-5 pb-6 border-2 @error('main_product_image') border-red-500 @else border-gray-300 @enderror border-dashed rounded-lg"
                                id="main-image-dropzone">

                                {{-- Existing Image --}}
                                @if ($mainImage = $meetingRoom->getFirstMedia('meeting_room_primary_image'))
                                    <div class="relative w-32 h-32 mx-auto" data-uuid="{{ $mainImage->uuid }}">
                                        <img src="{{ $mainImage->getUrl() }}"
                                            class="w-full h-full object-cover rounded-lg shadow-md">
                                        <button type="button"
                                            class="absolute -top-2 -right-2 text-red-600 bg-white rounded-full p-0.5 shadow-sm"
                                            onclick="removeExistingMainImage('{{ $mainImage->uuid }}')">
                                            <!-- Updated function name -->
                                            <i class="fas fa-times-circle text-lg"></i>
                                        </button>
                                        <input type="hidden" name="existing_main_image" value="{{ $mainImage->uuid }}">
                                    </div>
                                @endif

                                {{-- Dropzone Content --}}
                                <div class="text-center absolute inset-0 flex items-center justify-center bg-white bg-opacity-75"
                                    id="main-image-content"
                                    style="{{ $meetingRoom->getFirstMedia('plan_primary_image') ? 'background: rgba(255,255,255,0.9)' : '' }}">
                                    <div class="w-full p-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex justify-center text-sm text-gray-600">
                                            <label
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                                <span>Drag file or click to upload</span>
                                                <input id="main_product_image" name="main_product_image" type="file"
                                                    class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                            </div>
                            @error('main_product_image')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Images -->
                        <div>
                            <div class="mt-1 relative border-2 @error('additional_images.*') border-red-500 @else @error('additional_images') border-red-500 @else border-gray-300 @enderror @enderror border-dashed rounded-lg px-6 pt-5 pb-6"
                                id="dropzone">
                                <div class="flex flex-wrap gap-4" id="preview-container">
                                    @foreach ($meetingRoom->getMedia('meeting_room_additional_images') as $media)
                                        <div class="relative w-24 h-24 group" data-uuid="{{ $media->uuid }}">
                                            <img src="{{ $media->getUrl() }}"
                                                class="w-full h-full object-cover rounded-lg shadow-md">
                                            <button type="button"
                                                class="absolute -top-2 -right-2 text-red-600 bg-white rounded-full p-0.5 shadow-sm"
                                                onclick="removeExistingImage('{{ $media->uuid }}')">
                                                <i class="fas fa-times-circle text-lg"></i>
                                            </button>
                                            <input type="hidden" name="existing_additional_images[]"
                                                value="{{ $media->uuid }}">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Always visible dropzone overlay -->
                                <div class="text-center absolute inset-0 flex items-center justify-center bg-white bg-opacity-75"
                                    id="dropzone-content"
                                    style="{{ $meetingRoom->getMedia('meeting_room_additional_images')->count() ? 'background: rgba(255,255,255,0.9)' : '' }}">
                                    <div class="w-full p-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex justify-center text-sm text-gray-600">
                                            <label
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                                <span>Drag files or click to upload</span>
                                                <input id="additional_images" name="additional_images[]" type="file"
                                                    multiple class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 5 files, 2MB each</p>
                                    </div>
                                </div>
                            </div>

                            @error('additional_images')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @foreach ($errors->get('additional_images.*') as $message)
                                <p class="mt-1 text-xs text-red-600">{{ $message[0] }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Features</h3>
                        <div id="features-container" class="space-y-4">
                            @foreach (old('features', $meetingRoom->features) as $index => $feature)
                                <div class="feature-group">
                                    <div class="flex flex-wrap gap-4 items-end">
                                        <div class="W-full sm:w-1/2">
                                            <label for="feature-icon"
                                                class="block font-medium text-sm text-gray-900 mb-2">
                                                Select Feature
                                            </label>
                                            <select name="features[{{ $index }}][icon_class]" id="feature-icon"
                                                required class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                autocomplete="off">
                                                <option value="">Select Icon</option>
                                                @foreach ($featureSettings as $featureSetting)
                                                    <option value="{{ $featureSetting->id }}"
                                                        data-icon="{{ $featureSetting->icon }}"
                                                        {{ $feature->feature_setting_id === $featureSetting->id ? 'selected' : '' }}>
                                                        {{ $featureSetting->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Is Active?
                                            </label>
                                            <select name="features[{{ $index }}][is_active]" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <option value="1" {{ $feature->is_activated ? 'selected' : '' }}>Yes
                                                </option>
                                                <option value="0" {{ !$feature->is_activated ? 'selected' : '' }}>No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="w-auto">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2 invisible">Remove</label>
                                            <button type="button"
                                                class="text-red-500 hover:text-red-700 remove-feature px-3 py-2 border border-gray-300 rounded-lg">
                                                <i class="fas fa-times text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-feature"
                            class="mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i> Add Feature
                        </button>
                    </div>

                    <div class="mt-8">
                        <div class="px-4 py-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Enabled</label>
                            <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="1" {{ $meetingRoom->is_active ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$meetingRoom->is_active ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                            Update Meeting Room
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection

@section('css')
    <style>
        #dropzone {
            position: relative;
            min-height: 150px;
        }

        #dropzone-content {
            pointer-events: none;
            /* Allows click-through to underlying elements */
            transition: all 0.3s ease;
        }

        #dropzone:hover #dropzone-content {
            background: rgba(255, 255, 255, 0.95) !important;
        }

        #preview-container {
            position: relative;
            z-index: 1;
        }

        #main-image-dropzone {
            position: relative;
            min-height: 150px;
        }

        #main-image-content {
            pointer-events: none;
            /* Allow click-through */
            opacity: 0.3;
            transition: opacity 0.2s ease;
        }

        #main-image-dropzone:hover #main-image-content {
            opacity: 0.9;
            background: rgba(255, 255, 255, 0.95);
        }

        #main-image-dropzone [data-uuid] {
            position: relative;
            z-index: 2;
            /* Keep existing image above overlay */
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize feature index based on existing features
            let featureIndex = {{ count(old('features', $meetingRoom->features)) }};
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('additional_images');
            const previewContainer = document.getElementById('preview-container');
            const dropzoneContent = document.getElementById('dropzone-content');
            const container = document.getElementById('features-container');

            document.getElementById('add-feature').addEventListener('click', function() {
                const newFeature = document.createElement('div');
                newFeature.className = 'feature-group';
                newFeature.innerHTML = `
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="W-full sm:w-1/2">
                        <label for="feature-icon" class="block font-medium text-sm text-gray-900 mb-2">
                            Select Feature
                        </label>
                        <select name="features[${featureIndex}][icon_class]" id="feature-icon" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg" autocomplete="off">
                            <option value="">Select Icon</option>
                            @foreach ($featureSettings as $featureSetting)
                                <option value="{{ $featureSetting->id }}" data-icon="{{ $featureSetting->icon }}">
                                    {{ $featureSetting->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Is Active?
                        </label>
                        <select name="features[${featureIndex}][is_active]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="w-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-2 invisible">Remove</label>
                        <button type="button"
                            class="text-red-500 hover:text-red-700 remove-feature px-3 py-2 border border-gray-300 rounded-lg">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
            `;
                container.appendChild(newFeature);
                featureIndex++;
            });

            // Open file dialog when clicking anywhere in the dropzone
            dropzone.addEventListener('click', (e) => {
                if (e.target.closest('.group')) return; // Don't trigger if clicking existing images
                fileInput.click();
            });

            // Drag and drop handlers
            const handleDrag = (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-orange-500', 'bg-orange-50');
            };

            const handleDragLeave = (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-orange-500', 'bg-orange-50');
            };

            const handleDrop = (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-orange-500', 'bg-orange-50');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFiles(files);
                    fileInput.files = files;
                }
            };

            ['dragover', 'dragenter'].forEach(event => {
                dropzone.addEventListener(event, handleDrag);
            });

            ['dragleave', 'dragend'].forEach(event => {
                dropzone.addEventListener(event, handleDragLeave);
            });

            dropzone.addEventListener('drop', handleDrop);

            // File input change handler
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFiles(e.target.files);
                }
            });

            // Feature removal handling
            container.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-feature');
                if (removeBtn) {
                    removeBtn.closest('.feature-group').remove();
                }
            });

            // Add new image removal function
            window.removeNewImage = (indexToRemove) => {
                const dt = new DataTransfer();
                const currentFiles = Array.from(fileInput.files);

                currentFiles.forEach((file, index) => {
                    if (index !== indexToRemove) {
                        dt.items.add(file);
                    }
                });

                fileInput.files = dt.files;

                // Remove preview element
                document.querySelectorAll('[data-new-index]').forEach(preview => {
                    if (parseInt(preview.dataset.newIndex) === indexToRemove) {
                        preview.remove();
                    }
                });

                // Show dropzone if no images left
                if (document.querySelectorAll('#preview-container > div').length === 0) {
                    document.getElementById('dropzone-content').style.display = 'block';
                }
            }
            // Fix image display and removal
            window.removeExistingImage = function(uuid) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'removed_images[]';
                input.value = uuid;
                document.querySelector('form').appendChild(input);

                const preview = document.querySelector(`[data-uuid="${uuid}"]`);
                if (preview) {
                    preview.remove();
                }

                // Show dropzone if no images left
                if (document.querySelectorAll('#preview-container > div').length === 0) {
                    document.getElementById('dropzone-content').style.display = 'block';
                }
            }

            //handle file function
            function handleFiles(files) {
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const previewWrapper = document.createElement('div');
                        previewWrapper.className = 'relative w-24 h-24 group';
                        previewWrapper.dataset.newIndex = index;

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover rounded-lg shadow-md';
                        previewWrapper.appendChild(img);

                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className =
                            'absolute -top-2 -right-2 text-red-600 bg-white rounded-full p-0.5 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity';
                        removeButton.innerHTML = '<i class="fas fa-times-circle text-lg"></i>';
                        removeButton.onclick = () => removeNewImage(index);
                        previewWrapper.appendChild(removeButton);

                        previewContainer.appendChild(previewWrapper);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Main Image Handling
            const mainImageInput = document.getElementById('main_product_image');
            const mainImageDropzone = document.getElementById('main-image-dropzone');

            // Remove existing main image
            window.removeExistingMainImage = function(uuid) {
                // Add removal marker
                const removedInput = document.createElement('input');
                removedInput.type = 'hidden';
                removedInput.name = 'removed_main_image';
                removedInput.value = uuid;
                document.querySelector('form').appendChild(removedInput);

                // Clear existing image reference
                const existingInput = document.querySelector('input[name="existing_main_image"]');
                if (existingInput) existingInput.remove();

                // Remove preview
                const preview = document.querySelector(`[data-uuid="${uuid}"]`);
                if (preview) preview.remove();

                // Show dropzone
                document.getElementById('main-image-content').style.background = '';
            };

            // Handle new main image upload
            mainImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    // Clear existing previews
                    mainImageDropzone.querySelectorAll('[data-uuid]').forEach(el => el.remove());

                    // Create new preview
                    const preview = document.createElement('div');
                    preview.className = 'relative w-32 h-32 mx-auto';
                    preview.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg shadow-md">
                    <button type="button" class="absolute -top-2 -right-2 text-red-600 bg-white rounded-full p-0.5 shadow-sm"
                            onclick="removeNewMainImage()">
                        <i class="fas fa-times-circle text-lg"></i>
                    </button>
                `;

                    mainImageDropzone.prepend(preview);
                    document.getElementById('main-image-content').style.display = 'none';
                };
                reader.readAsDataURL(file);
            });

            // Remove new main image
            window.removeNewMainImage = function() {
                mainImageInput.value = '';
                mainImageDropzone.querySelector('[data-uuid]')?.remove();
                document.getElementById('main-image-content').style.display = 'flex';
            };

            // Click handling for main image dropzone
            mainImageDropzone.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    mainImageInput.click();
                }
            });

        });
    </script>
@endsection
