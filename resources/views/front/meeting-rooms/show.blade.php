@extends('layouts.front')

{{-- SEO Sections --}}
@section('title')
    {{ $meetingRoom->name }} Hire | Woolwich, London
@endsection

@section('description')
    Book {{ $meetingRoom->name }} in Woolwich, London for your most important meetings. High-spec, premium Meeting Room designed to impress clients. 
@endsection

@section('keywords', "{{ $meetingRoom->name }}, High-Spec, Premium Venue, Boardroom, Woolwich, London, Impress Clients")

@section('content')
    <div class="container mx-auto px-6 py-16 md:py-10 max-w-7xl">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <div class="lg:col-span-2 space-y-10">
                {{-- Gallery Section --}}
                <section id="room-images">
                    <h2 class="text-3xl font-bold text-blue-800 mb-6">{{ $meetingRoom->name }} Gallery</h2>
                    <div class="mb-6">
                        <img id="main-room-image" src="{{ $meetingRoom->meeting_primary_image ?: 'https://placehold.co/1080x720/1d4ed8/ffffff?text=Room+Image' }}"
                            alt="{{ $meetingRoom->name }} - Main View"
                            class="w-full h-auto max-h-[600px] rounded-lg shadow-lg object-cover cursor-pointer"
                            onerror="this.onerror=null; this.src='https://placehold.co/1080x720/1d4ed8/ffffff?text=Image+Not+Found';">
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 thumbnail-container">
                        <img src="{{ $meetingRoom->meeting_primary_image ?: 'https://placehold.co/150x100/cccccc/ffffff?text=Thumb+1' }}"
                            alt="{{ $meetingRoom->name }} Thumbnail"
                            data-large="{{ $meetingRoom->meeting_primary_image ?: 'https://placehold.co/1080x720/cccccc/ffffff?text=Image+1+Not+Found' }}"
                            class="w-full h-24 object-cover rounded shadow gallery-thumb active"
                            onerror="this.onerror=null; this.src='https://placehold.co/150x100/cccccc/ffffff?text=Thumb+1'; this.dataset.large='https://placehold.co/1080x720/cccccc/ffffff?text=Image+1+Not+Found'">
                        @foreach ($meetingRoom->getMedia($meetingRoom::MEETING_ADDITIONAL_IMAGES) as $media)
                            <img src="{{ $media->getUrl() }}" alt="{{ $meetingRoom->name }} Thumbnail {{ $loop->iteration + 1 }}"
                                data-large="{{ $media->getUrl() }}"
                                class="w-full h-24 object-cover rounded shadow gallery-thumb"
                                onerror="this.onerror=null; this.src='https://placehold.co/150x100/cccccc/ffffff?text=Thumb+{{ $loop->iteration + 1 }}'; this.dataset.large='https://placehold.co/1080x720/cccccc/ffffff?text=Image+{{ $loop->iteration + 1 }}+Not+Found'">
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2 italic">Click the main image to enlarge. Click thumbnails to change main image.</p>
                </section>

                {{-- Description Section --}}
                <section id="room-description">
                    <h2 class="text-3xl font-bold text-blue-800 mb-4">Room Description</h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                        {!! $meetingRoom->description ?: '<p>Detailed description not available. Please contact us for more information.</p>' !!}
                    </div>
                </section>

                {{-- Features Section --}}
                <section id="room-features">
                    <h2 class="text-3xl font-bold text-blue-800 mb-6">Room Features</h2>
                    @if($meetingRoom->features && (is_array($meetingRoom->features) ? count($meetingRoom->features) : (is_object($meetingRoom->features) && method_exists($meetingRoom->features, 'count') ? $meetingRoom->features->count() : 0)) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            {{-- Adjust based on how $meetingRoom->features is structured --}}
                            {{-- If it's a collection of objects with a 'value' property, as in the old file: --}}
                            @foreach($meetingRoom->features as $feature)
                                <div class="flex items-center text-gray-700">
                                    <i class="{{ $feature->featureSetting->icon }} text-orange-500 mr-3 w-6 text-center"></i>
                                    <span>{{ $feature->featureSetting->name }}</span> 
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 mt-4">Specific features list not available. Standard amenities are provided.</p>
                    @endif
                </section>
            </div>

            {{-- Booking Section --}}
            <div class="lg:col-span-1 space-y-10">
                <section id="booking" class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 sticky top-24">
                    <h3 class="text-2xl font-semibold text-blue-800 mb-4 text-center">Book {{ $meetingRoom->name }}</h3>
                    @include('front.common.error-and-message')
                    <div class="text-center mb-5">
                        <span class="text-3xl font-bold text-blue-700">{{ currencyFormatter($meetingRoom->price) }}</span>
                        <span class="text-gray-600">/ hour</span>
                    </div>

                    <form action="{{ route('meeting-rooms.store', ['product_id' => $meetingRoom->id] ) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="booking-date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="date-picker" name="booking_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                required>
                        </div>
                        <div>
                            <label for="booking-time-display" class="block text-sm font-medium text-gray-700 mb-1">Time Slot(s)</label>
                            <div class="custom-multiselect-container relative">
                                <div id="booking-time-display"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 cursor-pointer flex justify-between items-center min-h-[42px]">
                                    <span>Select Time Slot(s)</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                                <div id="booking-time-options"
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    {{-- Checkbox options will be populated by JavaScript --}}
                                    <p class="text-gray-500 text-sm px-4 py-2">Please select a date to see available slots.</p>
                                </div>
                            </div>
                            <div id="booking-time-message" class="mt-2 text-sm text-red-600 font-medium"></div>
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md text-lg">
                            Add to Cart
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-2">Full payment required at checkout. Review cancellation policy.</p>
                    </form>
                </section>

                {{-- Other sections (Why Choose, FAQ) remain the same --}}
                <section id="why-choose">
                    <h3 class="text-2xl font-semibold text-blue-800 mb-4">Why Choose {{ $meetingRoom->name }}?</h3>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i> <span><strong>Professional Impression:</strong> Impress clients and stakeholders.</span></li>
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i> <span><strong>Fully Equipped:</strong> All necessary tech included.</span></li>
                        <li class="flex items-start"><i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i> <span><strong>Comfort & Focus:</strong> Designed for productivity.</span></li>
                    </ul>
                </section>

                <section id="faq-room">
                    <h2 class="text-2xl font-semibold text-blue-800 mb-6">Room FAQ</h2>
                    <div class="space-y-4">
                        <div class="faq-item p-4 rounded-lg bg-gray-100 shadow-sm">
                            <h4 class="faq-question text-md font-semibold text-blue-700 flex justify-between items-center"><span>What is the maximum capacity?</span><i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i></h4>
                            <p class="text-gray-600 faq-answer text-sm">Capacity details are available in the room features or description.</p>
                        </div>
                         <div class="faq-item p-4 rounded-lg bg-gray-100 shadow-sm">
                            <h4 class="faq-question text-md font-semibold text-blue-700 flex justify-between items-center"><span>What is the cancellation policy?</span><i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i></h4>
                            <p class="text-gray-600 faq-answer text-sm">Cancellations made at least 48 hours before booking receive a full refund. Please see full terms.</p>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        {{-- CTA and Other Rooms Sections remain the same --}}
         <section id="cta-booking" class="mt-16 md:mt-24 py-12 bg-blue-600 text-white rounded-lg shadow-xl text-center">
            <div class="container mx-auto px-6">
                <h3 class="text-3xl font-semibold mb-4">Ready to Book {{ $meetingRoom->name }}?</h3>
                <p class="text-blue-100 mb-8 max-w-lg mx-auto">Secure this premium space for your next important meeting.</p>
                <a href="#booking" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                    Book Now <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </section>

        <section id="other-rooms" class="mt-16 md:mt-24 text-center">
            <h2 class="text-2xl font-semibold text-blue-800 mb-6">Explore Other Spaces</h2>
            <div class="flex justify-center space-x-6">
                <a href="{{ url('/meeting-rooms') }}" class="text-blue-600 hover:text-orange-500 hover:underline transition duration-300"><i class="fas fa-users mr-1"></i> View Meeting Rooms</a>
                <a href="{{ url('/meeting-rooms') }}" class="text-blue-600 hover:text-orange-500 hover:underline transition duration-300"><i class="fas fa-chalkboard-teacher mr-1"></i> View All Meeting Rooms</a>
            </div>
        </section>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="modal">
        <span class="modal-close" id="modalClose">&times;</span>
        <div class="modal-content-wrapper bg-white p-4 rounded-lg">
            <img class="modal-content" id="modalImage" src="" alt="Meeting Room View">
            <div class="modal-thumbnails gallery-scroll" id="modalThumbnails"></div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Styles from original + new styles for multi-select */
        .faq-item { border-bottom: 1px solid #e5e7eb; }
        .faq-question { cursor: pointer; }
        .faq-answer { overflow: hidden; transition: max-height 0.3s ease-out; max-height: 0; }
        .faq-answer.open { max-height: 300px; margin-top: 0.5rem; }
        .faq-question i.rotate-180 { transform: rotate(180deg); } /* Ensure rotate works */

        .thumbnail-container img, .modal-thumbnails img { cursor: pointer; transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out, border-color 0.2s ease-in-out; border: 2px solid transparent; }
        .thumbnail-container img:hover, .modal-thumbnails img:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .thumbnail-container img.active, .modal-thumbnails img.active { border-color: #f97316; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.85); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease-in-out; }
        .modal.active { display: flex; opacity: 1; }
        .modal-content-wrapper { max-width: 90%; max-height: 90%; transform: scale(0.95); transition: transform 0.3s ease-in-out; }
        .modal.active .modal-content-wrapper { transform: scale(1); }
        .modal-content { display: block; width: auto; height: auto; max-width: 100%; max-height: 75vh; margin: auto; }
        .modal-close { position: absolute; top: 15px; right: 25px; color: #f1f1f1; font-size: 40px; font-weight: bold; transition: 0.3s; cursor: pointer; line-height: 1; }
        .modal-close:hover, .modal-close:focus { color: #bbb; }
        .modal-thumbnails { margin-top: 15px; text-align: center; max-width: 100%; overflow-x: auto; padding-bottom: 10px; white-space: nowrap; }
        .modal-thumbnails img { width: 80px; height: 60px; object-fit: cover; margin: 0 5px; display: inline-block; }

        /* Custom Multi-select Dropdown Styles */
        .multiselect-option input[type="checkbox"] {
            accent-color: #f97316; /* Orange accent for checkbox */
        }
        #booking-time-display i.rotate-180 { transform: rotate(180deg); }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- FAQ Toggle ---
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const icon = question.querySelector('i');
                    if (answer && answer.classList.contains('faq-answer')) {
                        const isOpen = answer.classList.contains('open');
                        answer.classList.toggle('open');
                        if (icon) icon.classList.toggle('rotate-180', !isOpen);
                    }
                });
            });

            // --- Image Gallery Logic ---
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const mainImg = document.getElementById('main-room-image');
            const pageThumbnails = document.querySelectorAll('.thumbnail-container .gallery-thumb');
            const modalClose = document.getElementById('modalClose');
            const modalThumbnailsContainer = document.getElementById('modalThumbnails');

            const largeImageSources = Array.from(pageThumbnails).map(thumb => thumb.dataset.large);

            function openModalWithImage(src) {
                if (!modal || !modalImg || !modalThumbnailsContainer) return;
                modalImg.src = src;
                modalThumbnailsContainer.innerHTML = '';

                largeImageSources.forEach(imgSrc => {
                    const thumb = document.createElement('img');
                    thumb.src = imgSrc.includes('placehold.co') ? imgSrc.replace(/(\d+)x(\d+)/, '150x100') : imgSrc;
                    thumb.alt = "Room View Thumbnail";
                    thumb.dataset.large = imgSrc;
                    thumb.className = 'modal-thumb w-20 h-16 object-cover inline-block mx-1 cursor-pointer border-2 border-transparent rounded hover:border-orange-400 transition';
                    if (imgSrc === src) {
                        thumb.classList.add('active', 'border-orange-500');
                    }
                    thumb.addEventListener('click', function() {
                        modalImg.src = this.dataset.large;
                        modalThumbnailsContainer.querySelectorAll('img').forEach(t => t.classList.remove('active', 'border-orange-500'));
                        this.classList.add('active', 'border-orange-500');
                    });
                    modalThumbnailsContainer.appendChild(thumb);
                });
                modal.classList.add('active');
            }

            if (mainImg) {
                mainImg.addEventListener('click', function() { openModalWithImage(this.src); });
            }

            pageThumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    const largeImagePath = this.dataset.large;
                    if (mainImg && largeImagePath) {
                        mainImg.src = largeImagePath;
                        mainImg.alt = this.alt.replace('Thumbnail', 'Main View');
                        pageThumbnails.forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });

            if (modalClose) modalClose.addEventListener('click', () => modal.classList.remove('active'));
            if (modal) {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) modal.classList.remove('active');
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal.classList.contains('active')) {
                        modal.classList.remove('active');
                    }
                });
            }

            // --- Booking Form Logic ---
            const datePicker = document.getElementById('date-picker');
            // const durationDropdown = document.getElementById('duration-dropdown'); // Duration is now standard select for cart
            const bookingTimeDisplay = document.getElementById('booking-time-display');
            const bookingTimeOptionsContainer = document.getElementById('booking-time-options');
            let bookingTimeCheckboxes = []; // Will be populated dynamically
            const bookingTimeMessage = document.getElementById('booking-time-message');


            if (datePicker) {
                const today = new Date().toISOString().split('T')[0];
                datePicker.setAttribute('min', today);

                datePicker.addEventListener('change', function () {
                    const selectedDate = this.value;
                    if (selectedDate) {
                        updateBookingTimeSlots(selectedDate);
                    } else {
                        // Clear and hide time slots if date is cleared
                        bookingTimeOptionsContainer.innerHTML = '<p class="text-gray-500 text-sm px-4 py-2">Please select a date to see available slots.</p>';
                        updateBookingTimeDisplay(); // Reset display text
                        bookingTimeOptionsContainer.classList.add('hidden');
                         if(bookingTimeDisplay) bookingTimeDisplay.querySelector('i').classList.remove('rotate-180');

                    }
                });
            }

            function setupBookingTimeCheckboxes() {
                bookingTimeCheckboxes = bookingTimeOptionsContainer.querySelectorAll('input[type="checkbox"]');
                bookingTimeCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        updateBookingTimeDisplay();
                        validateBookingTime();
                    });
                });
            }

            if (bookingTimeDisplay && bookingTimeOptionsContainer) {
                bookingTimeDisplay.addEventListener('click', function () {
                    // Only toggle if there are actual options or a message indicating to select a date
                    if (bookingTimeOptionsContainer.children.length > 0) {
                        bookingTimeOptionsContainer.classList.toggle('hidden');
                        this.querySelector('i').classList.toggle('rotate-180');
                    } else {
                        bookingTimeMessage.textContent = "Please select a date first.";
                    }
                });

                document.addEventListener('click', function (event) {
                    if (bookingTimeDisplay && !bookingTimeDisplay.contains(event.target) && bookingTimeOptionsContainer && !bookingTimeOptionsContainer.contains(event.target)) {
                        bookingTimeOptionsContainer.classList.add('hidden');
                         if(bookingTimeDisplay.querySelector('i')) bookingTimeDisplay.querySelector('i').classList.remove('rotate-180');
                    }
                });
            }

            function updateBookingTimeDisplay() {
                if (!bookingTimeDisplay) return;
                const selectedLabels = [];
                bookingTimeCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        // The text content is the checkbox label itself
                        selectedLabels.push(checkbox.parentElement.textContent.trim());
                    }
                });

                const displaySpan = bookingTimeDisplay.querySelector('span');
                if (selectedLabels.length > 0) {
                    displaySpan.textContent = selectedLabels.length === 1 ? selectedLabels[0] : selectedLabels.length + ' slots selected';
                } else {
                    displaySpan.textContent = 'Select Time Slot(s)';
                }
            }

            function validateBookingTime() {
                if (!bookingTimeMessage) return true;
                let oneChecked = false;
                bookingTimeCheckboxes.forEach(cb => {
                    if (cb.checked) oneChecked = true;
                });
                // This validation is for UI feedback; form submission relies on backend
                // if (!oneChecked) {
                // bookingTimeMessage.textContent = "Please select at least one time slot if making a booking.";
                // } else {
                // bookingTimeMessage.textContent = "";
                // }
                return true;
            }

            function updateBookingTimeSlots(selectedDate) {
                if (!bookingTimeOptionsContainer || !selectedDate) return;

                bookingTimeOptionsContainer.innerHTML = '<p class="text-blue-500 text-sm px-4 py-2">Loading slots...</p>'; // Show loading state
                updateBookingTimeDisplay(); // Reset display text to "Select Time Slot(s)"

                // Ensure the dropdown is potentially visible if user clicks display
                bookingTimeOptionsContainer.classList.remove('hidden');
                 if(bookingTimeDisplay) bookingTimeDisplay.querySelector('i').classList.remove('rotate-180');


                fetch(`/schedule/slots?product_id={{ $meetingRoom->id }}&booking_date=${selectedDate}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        bookingTimeOptionsContainer.innerHTML = ''; // Clear loading/old slots
                        if (data.slots && data.slots.length > 0) {
                            data.slots.forEach(slotText => { // Assuming slotText is "9:00 AM - 10:00 AM"
                                const label = document.createElement('label');
                                label.className = 'multiselect-option block px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                // To get a value like "0900-1000", we need to parse/convert slotText
                                // For simplicity, if your getTimeSlots can return {value: "0900-1000", label: "9:00 AM - 10:00 AM"}
                                // it would be easier. Otherwise, we use the display text as value too.
                                // Or, derive value:
                                const timeParts = slotText.match(/(\d{1,2}:\d{2}\s*[AP]M)/g);
                                let valueAttribute = slotText; // Default to full text if parsing fails
                                if (timeParts && timeParts.length === 2) {
                                    const startTime = new Date(`1/1/2000 ${timeParts[0]}`);
                                    const endTime = new Date(`1/1/2000 ${timeParts[1]}`);
                                    valueAttribute = `${String(startTime.getHours()).padStart(2,'0')}${String(startTime.getMinutes()).padStart(2,'0')}-${String(endTime.getHours()).padStart(2,'0')}${String(endTime.getMinutes()).padStart(2,'0')}`;
                                }

                                label.innerHTML = `<input type="checkbox" name="booking_time[]" value="${valueAttribute}" class="mr-2"> ${slotText}`;
                                bookingTimeOptionsContainer.appendChild(label);
                            });
                            setupBookingTimeCheckboxes(); // Re-setup listeners for new checkboxes
                        } else {
                            bookingTimeOptionsContainer.innerHTML = '<p class="text-red-500 text-sm px-4 py-2">No available slots for this date.</br>Select another date</p>';
                        }
                        updateBookingTimeDisplay(); // Update display based on new (empty) selection
                    })
                    .catch(error => {
                        console.error('Error fetching time slots:', error);
                        bookingTimeOptionsContainer.innerHTML = '<p class="text-red-500 text-sm px-4 py-2">Error loading slots. Please try again.</p>';
                        updateBookingTimeDisplay();
                    });
            }

            // Initial call to set up display text
            if (bookingTimeDisplay) {
                 updateBookingTimeDisplay();
            }
        });
    </script>
@endsection
