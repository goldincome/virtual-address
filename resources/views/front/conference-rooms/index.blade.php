@extends('layouts.front')


@section('title')
    Professional Conference Room Booking in London
@endsection

@section('description')
    Book a professional Conference Room near Woolwich, Greenwich, and Charlton. Ideal for corporate meetings & training. Easy online booking.
@endsection
@section('keywords', "Conference Room,  Boardroom, Seminar Hall, Training Center, Business Meeting, Presentation Room, Workshop Space, Corporate Events, London Conference Rooms, Virtual Office Conference Facilities")

@section('css')
    <style>
        .hero-bg-conference {
            background-image: url('{{ asset('images/conference3.jpg') }}'); /* Conference specific hero */
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
    
    <section class="hero-bg-conference text-white relative">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Spacious Conference Rooms</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Host successful presentations, workshops, and board meetings in our large, fully-equipped conference facilities.
            </p>
            <a href="#room-types" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">
                View Room Options <i class="fas fa-chevron-down ml-2"></i>
            </a>
        </div>
    </section>

    <section id="introduction" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6">Present, Train, and Collaborate</h2>
                <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                   Our conference rooms are designed for larger groups and impactful events. Equipped with state-of-the-art audiovisual technology and flexible seating arrangements, they provide the ideal setting for presentations, training sessions, workshops, and board meetings.
                </p>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    Make a lasting impression with a professional environment that supports seamless communication and collaboration. Book the space you need for the duration required, with support available to ensure your event runs smoothly.
                </p>
                <a href="#room-types" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                    Explore Conference Rooms
                </a>
            </div>
            <div>
                <img src="{{ asset('images/conference.jpg') }}"
                     alt="People attending a presentation in a conference room"
                     class="w-full h-auto rounded-lg shadow-lg"
                     onerror="this.onerror=null; this.src='https://placehold.co/600x450/cccccc/ffffff?text=Conference';">
            </div>
        </div>
    </section>

    <section id="room-types" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Our Conference Room Options</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($conferenceRooms as $index => $conferenceRoom)
                    @if($index == 1)
                        <div class="room-package-card rounded-lg shadow-lg overflow-hidden border-2 border-orange-500"> 
                            <img src="{{ $conferenceRoom->conference_primary_image }}" alt="{{ $conferenceRoom->name }}" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Seminar+Hall';">
                            <div class="p-6 flex flex-col flex-grow card-content">
                                <h3 class="text-xl font-semibold text-orange-600 mb-3">{{ $conferenceRoom->name }}</h3>
                                <p class="text-gray-700 text-sm mb-4">{{ $conferenceRoom->intro }}</p>
                                <div class="flex items-center justify-between text-2xl font-bold text-blue-900 mb-4">
                                    <span>{{ currencyFormatter($conferenceRoom->price) }}<span class="text-base font-normal text-gray-500">/hour</span></span>
                                    <a href="{{ route('conference-rooms.show', $conferenceRoom->slug) }}" class="text-sm text-blue-500 font-medium hover:underline">Learn More >></a>
                                </div>
                                <a href="{{route('conference-rooms.show', $conferenceRoom->slug)}}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="room-package-card rounded-lg shadow-lg overflow-hidden border border-gray-200">
                            <img src="{{ $conferenceRoom->conference_primary_image }}" alt="{{ $conferenceRoom->name }}" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Boardroom';">
                            <div class="p-6 flex flex-col flex-grow card-content">
                                <h3 class="text-xl font-semibold text-blue-700 mb-3">{{ $conferenceRoom->name }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ $conferenceRoom->intro }}</p>
                                <div class="flex items-center justify-between text-2xl font-bold text-blue-900 mb-4">
                                    <span>{{ currencyFormatter($conferenceRoom->price) }}<span class="text-base font-normal text-gray-500">/hour</span></span>
                                    <a href="{{ route('conference-rooms.show', $conferenceRoom->slug) }}" class="text-sm text-orange-500 font-medium hover:underline">Learn More >></a>
                                </div>
                                <a href="{{route('conference-rooms.show', $conferenceRoom->slug)}}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                    View Details
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
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Booking Your Conference Room</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto text-center">
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">1</div>
                     <i class="fas fa-search-plus text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Explore Options</h3>
                    <p class="text-gray-600">Review our conference room sizes, amenities, and layouts to find the best match for your event.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">2</div>
                     <i class="fas fa-calendar-alt text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Check Availability</h3>
                    <p class="text-gray-600">Use our online booking tool to select your date and time, check availability, and reserve your room instantly.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">3</div>
                     <i class="fas fa-chalkboard-teacher text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Host Your Event</h3>
                    <p class="text-gray-600">Arrive at your scheduled time and utilize our professional space and technology for a successful event.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="book" class="py-16">
        <div class="container mx-auto px-6">
             <div class="cta-block relative text-center rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                  style="background-image: url('{{ asset('images/conference.jpg') }}'); background-size: cover; background-position: center;">
                 <div class="absolute inset-0 bg-black/60 rounded-lg"></div>
                 <div class="relative z-10 p-10 md:p-16">
                     <h3 class="text-3xl md:text-4xl font-bold text-white mb-6 max-w-2xl mx-auto">Book Your Conference Room Today</h3>
                     <p class="text-lg text-blue-100 mb-8 max-w-xl mx-auto">
                        Plan your next presentation, workshop, or board meeting in a space designed for success. Check availability now.
                     </p>
                    <a href="#room-types" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        Reserve Your Conference Room <i class="fas fa-calendar-check ml-2"></i>
                    </a>
                 </div>
            </div>
        </div>
     </section>


    <section id="why-choose-us" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Why Host Your Event With Us?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-tv text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Advanced Technology</h3>
                    <p class="text-gray-600">High-quality AV equipment, projectors, and reliable Wi-Fi.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-users-cog text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Flexible Spaces</h3>
                    <p class="text-gray-600">Rooms adaptable for various layouts and event types.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-map-marked-alt text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Convenient Location</h3>
                    <p class="text-gray-600">Easily accessible prime business locations.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-concierge-bell text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Professional Support</h3>
                    <p class="text-gray-600">On-site team available to assist with setup and technical needs.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-handshake text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Impress Your Guests</h3>
                    <p class="text-gray-600">Modern, clean, and professional environment for your attendees.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-utensils text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Catering Options</h3>
                    <p class="text-gray-600">Arrange refreshments or full catering to suit your event.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Conference Room FAQs</h2>
            <div class="space-y-4">
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What is the capacity of your conference rooms?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Our conference rooms vary in size, typically accommodating groups from 10 up to 20 people. Please check the details for each specific room (e.g., Boardroom, Seminar Hall).</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What technology is available in the conference rooms?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Standard amenities include high-speed Wi-Fi. Most conference rooms are equipped with projectors or large display screens, whiteboards, and video conferencing capabilities. Specific equipment details are listed on each room's booking page.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Can I arrange a specific room layout?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Some of our conference rooms, like the Seminar Hall and Training Center, offer flexible layouts (e.g., classroom, theatre, U-shape). Please specify your preferred layout during booking or contact us to discuss possibilities.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Is technical support available during my event?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Yes, our on-site staff can provide basic technical assistance to help you get started with the AV equipment. For more complex requirements, please discuss them with us prior to your booking.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>How far in advance should I book a conference room?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">We recommend booking as early as possible, especially for larger rooms or peak times, to ensure availability. However, you can check real-time availability and book online anytime.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact-cta" class="py-16 bg-blue-700 text-white">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-3xl font-semibold mb-4">Ready to Host Your Next Event?</h3>
            <p class="text-blue-100 mb-8 max-w-lg mx-auto">
               Contact our team to discuss your conference room needs, check availability, or get assistance with booking.
            </p>
            <a href="contact-us.html" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                Contact Our Team <i class="fas fa-headset ml-2"></i>
            </a>
        </div>
    </section>
 
@endsection


@section('js')
    <script>
       console.log("Conference Room page loaded.");

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
