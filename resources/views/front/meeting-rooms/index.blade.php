@extends('layouts.front')


@section('title')
    Book a Meeting Room in South East London
@endsection

@section('description')
    Instant online booking for professional Meeting Rooms near Woolwich, Greenwich, Charlton, Plumstead, and Abbeywood. Hourly and daily rates available.
@endsection

@section('keywords', "Meeting Room,  Business Meeting, Client Meeting, Team Meeting, Presentation Room, Workshop Space, Flexible Workspace, Hourly Booking, Daily Booking, London Meeting Rooms")

@section('css')
    <style>
        .hero-bg-meeting {
            background-image: url('{{ asset('images/meeting3.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .room-package-card {
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        .room-package-card .card-content {
            flex-grow: 1;
        }
    </style>
@endsection

@section('content')
    
    <section class="hero-bg-meeting text-white relative">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Professional Meeting Rooms</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Impress your clients and collaborate effectively in our fully-equipped, modern meeting spaces. Book by the hour or day.
            </p>
            <a href="#room-types" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">
                Explore Rooms <i class="fas fa-chevron-down ml-2"></i>
            </a>
        </div>
    </section>

    <section id="introduction" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6">Meet, Collaborate, Succeed</h2>
                <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                    Finding the right space for your important meetings shouldn't be complicated. Our meeting rooms provide a professional, private, and productive environment equipped with the technology you need.
                </p>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    Whether it's a client presentation, a team brainstorming session, or a crucial interview, our flexible booking options and prime locations make it easy to find the perfect space when you need it.
                </p>
                <a href="#room-types" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                    See Room Options
                </a>
            </div>
            <div>
                <img src="{{ asset('images/meeting.jpg') }}"
                     alt="Business people in a meeting room"
                     class="w-full h-auto rounded-lg shadow-lg"
                     onerror="this.onerror=null; this.src='https://placehold.co/600x450/cccccc/ffffff?text=Meeting';">
            </div>
        </div>
    </section>

    <section id="room-types" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Our Meeting Room Options</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($meetingRooms as $index => $meetingRoom)
                    @if($index == 1)
                        <div class="room-package-card rounded-lg shadow-lg overflow-hidden border-2 border-orange-500"> 
                            <img src="{{ $meetingRoom->meeting_primary_image }}" alt="{{ $meetingRoom->name }}" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Medium+Room';">
                            <div class="p-6 flex flex-col flex-grow card-content">
                                <h3 class="text-xl font-semibold text-orange-600 mb-3">{{ $meetingRoom->name }} </h3>
                                <p class="text-gray-700 text-sm mb-4">{{ $meetingRoom->intro }}</p>
                                <p class="text-2xl font-bold text-blue-900 mb-4">{{ currencyFormatter($meetingRoom->price) }}<span class="text-base font-normal text-gray-500">/hour</span></p>
                                <a href="{{ route('meeting-rooms.show', $meetingRoom->slug) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="room-package-card rounded-lg shadow-lg overflow-hidden border border-gray-200">
                            <img src="{{ $meetingRoom->meeting_primary_image }}" alt="{{ $meetingRoom->name }}" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Small+Room';">
                            <div class="p-6 flex flex-col flex-grow card-content">
                                <h3 class="text-xl font-semibold text-blue-700 mb-3">{{ $meetingRoom->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ $meetingRoom->intro }}</p>
                                <p class="text-2xl font-bold text-blue-900 mb-4">{{ currencyFormatter($meetingRoom->price) }}<span class="text-base font-normal text-gray-500">/hour</span></p>
                                <a href="{{ route('meeting-rooms.show', $meetingRoom->slug) }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Booking Your Meeting Room is Easy</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto text-center">
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">1</div>
                     <i class="fas fa-search text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Browse Rooms</h3>
                    <p class="text-gray-600">Explore our available meeting rooms and choose the one that fits your size and needs.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">2</div>
                     <i class="fas fa-calendar-alt text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Select Date & Time</h3>
                    <p class="text-gray-600">Use our simple online calendar to check availability and select your preferred date and time slot.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">3</div>
                     <i class="fas fa-check-circle text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Confirm & Meet</h3>
                    <p class="text-gray-600">Complete your booking details and payment. Arrive on the day and enjoy your productive meeting!</p>
                </div>
            </div>
        </div>
    </section>

    <section id="book" class="py-16">
        <div class="container mx-auto px-6">
             <div class="cta-block relative text-center rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                  style="background-image: url('{{ asset('images/meeting2.jpg') }}'); background-size: cover; background-position: center;">
                 <div class="absolute inset-0 bg-black/60 rounded-lg"></div>
                 <div class="relative z-10 p-10 md:p-16">
                     <h3 class="text-3xl md:text-4xl font-bold text-white mb-6 max-w-2xl mx-auto">Ready to Book Your Perfect Meeting Space?</h3>
                     <p class="text-lg text-blue-100 mb-8 max-w-xl mx-auto">
                        Secure your preferred meeting room quickly and easily through our online booking system.
                     </p>
                    <a href="#room-types" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        Check Availability & Book Now <i class="fas fa-calendar-check ml-2"></i>
                    </a>
                 </div>
            </div>
        </div>
     </section>


    <section id="why-choose-us" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Why Choose Our Meeting Rooms?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-map-marked-alt text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Prime Locations</h3>
                    <p class="text-gray-600">Conveniently located in key business areas for easy access.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-wifi text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Modern Amenities</h3>
                    <p class="text-gray-600">High-speed Wi-Fi, presentation tools, and comfortable furniture.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-dollar-sign text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Flexible Booking</h3>
                    <p class="text-gray-600">Book by the hour or day with simple online reservations.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-user-tie text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Professional Environment</h3>
                    <p class="text-gray-600">Impress clients and foster productivity in a polished setting.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-concierge-bell text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Support Staff</h3>
                    <p class="text-gray-600">On-site assistance available to ensure your meeting runs smoothly.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-coffee text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Refreshments</h3>
                    <p class="text-gray-600">Access to coffee, tea, and water. Catering options available.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Meeting Room FAQs</h2>
            <div class="space-y-4">
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>How do I book a meeting room?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Booking is easy! Use our online calendar to select your desired room, date, and time. Follow the prompts to confirm your details and payment.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What equipment is included?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">All rooms come with high-speed Wi-Fi and comfortable seating. Specific amenities like whiteboards, display screens, or projectors are listed in each room's description.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Can I book a room outside of standard business hours?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Availability outside standard hours may vary. Please check the online booking system or contact our support team to inquire about specific needs.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Is catering available?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Basic refreshments like coffee, tea, and water are available. We can also arrange catering services upon request � please contact us in advance to discuss options.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What is the cancellation policy?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Generally, cancellations made at least 48 hours before the booking time are fully refundable. Please review our full terms and conditions or contact support for details.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact-cta" class="py-16 bg-blue-700 text-white">
        <div class="container mx-auto px-6 text-center" >
            <h3 class="text-3xl font-semibold mb-4">Have Questions About Our Meeting Rooms?</h3>
            <p class="text-blue-100 mb-8 max-w-lg mx-auto">
                Our team is ready to help you find the perfect space and answer any questions you might have.
            </p>
            <a href="contact-us.html" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                Contact Us Today <i class="fas fa-headset ml-2"></i>
            </a>
        </div>
    </section>

@endsection


@section('js')
    <script>
       console.log("Meeting Room page loaded.");

        // --- FAQ Toggle ---
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                if (answer && answer.classList.contains('faq-answer')) {
                    answer.classList.toggle('open');
                    if (icon) {
                        icon.classList.toggle('rotate-180');
                    }
                } else {
                    console.error("Could not find the answer element for:", question);
                }
            });
        });
    </script>

@endsection
