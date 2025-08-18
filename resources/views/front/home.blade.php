@extends('layouts.front')


@section('title')
    Virtual Office Services - NURUD
@endsection

@section('description')
    Virtual Office Services - NURUD
@endsection



@section('content')
    <section class="hero-bg text-white relative" style="background-image: url('{{ asset('images/home-banner.jpg') }}')">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-32 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">Your Virtual Business Address</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Establish your presence with our flexible virtual office solutions, meeting rooms, and conference facilities.
            </p>
            <a href="#our-services" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">
                Explore Services <i class="fas fa-chevron-down ml-2"></i>
            </a>
        </div>
    </section>

    <section id="introduction" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6 text-center">Flexible Office Solutions for Your Business</h2>
                <p class="text-lg text-gray-700 mb-4 leading-relaxed">
                    At NURUD, we provide modern, flexible workspace solutions tailored to your needs. Whether you need a prestigious virtual address to establish your business presence, professional meeting rooms for client discussions, or fully-equipped conference rooms for larger gatherings, we have you covered.
                </p>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    Our services are designed to be cost-effective, allowing you to access premium facilities and a prime location without the high overheads of traditional office leases. Focus on growing your business while we handle the workspace logistics.
                </p>
                <a href="#our-services" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                    See Our Services
                </a>
            </div>
            <div>
                <img src="{{ asset('images/home-banner.jpg') }}"
                    alt="Team collaborating in a modern office"
                    class="w-full h-auto rounded-lg shadow-lg"
                    onerror="this.onerror=null; this.src='https://placehold.co/600x450/cccccc/ffffff?text=Collaboration';">
            </div>
        </div>
    </section>

    <section id="our-services" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Our Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="service-card bg-white p-8 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition duration-300">
                    <div class="text-center mb-4">
                        <i class="fas fa-map-marker-alt text-4xl text-orange-500"></i>
                    </div>
                    <div class="card-content text-center">
                        <h3 class="text-xl font-semibold text-blue-700 mb-3">Virtual Business Address</h3>
                        <p class="text-gray-600 mb-6">
                            Get a prime business address, mail handling, and more without the cost of a physical office.
                        </p>
                    </div>
                    <a href="{{ route('virtual-address.index') }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                        Learn More
                    </a>
                </div>
                <div class="service-card bg-white p-8 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition duration-300">
                    <div class="text-center mb-4">
                        <i class="fas fa-users text-4xl text-orange-500"></i>
                    </div>
                    <div class="card-content text-center">
                        <h3 class="text-xl font-semibold text-blue-700 mb-3">Meeting Rooms</h3>
                        <p class="text-gray-600 mb-6">
                            Book professional meeting spaces by the hour or day for your client and team needs.
                        </p>
                    </div>
                    <a href="{{ route('meeting-rooms.index') }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                        Learn More
                    </a>
                </div>
                <div class="service-card bg-white p-8 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition duration-300">
                    <div class="text-center mb-4">
                        <i class="fas fa-chalkboard-teacher text-4xl text-orange-500"></i>
                    </div>
                    <div class="card-content text-center">
                        <h3 class="text-xl font-semibold text-blue-700 mb-3">Conference Rooms</h3>
                        <p class="text-gray-600 mb-6">
                            Access fully-equipped rooms for larger presentations, workshops, or board meetings.
                        </p>
                    </div>
                    <a href="{{ route('conference-rooms.index') }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Get Started in 3 Simple Steps</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto text-center">
                <div class="flex flex-col items-center p-6">
                    <div class="step-number bg-orange-500 text-white">1</div>
                    <i class="fas fa-mouse-pointer text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Choose Your Service</h3>
                    <p class="text-gray-600">Select the virtual address plan or book the meeting/conference room that fits your requirements.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                    <div class="step-number bg-orange-500 text-white">2</div>
                    <i class="fas fa-calendar-check text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Book or Sign Up</h3>
                    <p class="text-gray-600">Easily book your room online or sign up for your virtual office address through our simple process.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                    <div class="step-number bg-orange-500 text-white">3</div>
                    <i class="fas fa-briefcase text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Utilize Your Space</h3>
                    <p class="text-gray-600">Enjoy your professional meeting space or start using your prestigious virtual business address immediately.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="virtual-address-plans" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-4">Virtual Business Address Plans</h2>
            <p class="text-center text-gray-700 max-w-2xl mx-auto mb-12">
                A quick overview of our virtual address options. Find the perfect foundation for your business presence.
            </p>
            <div class="flex flex-wrap justify-center gap-8 max-w-6xl mx-auto">
                @foreach($plans as $index => $plan)
                    @if($index == 1)
                        <div class="plan-card bg-white p-8 rounded-lg shadow-lg border flex flex-col popular-package">
                            <div class="popular-badge">Popular</div>
                            <div class="card-content">
                                <h3 class="text-2xl font-semibold text-orange-600 mb-4 text-center">{{ $plan->name }}</h3>
                                <p class="text-4xl font-bold text-center text-blue-800 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                                <ul class="space-y-2 text-gray-700 mb-8 text-sm">
                                    @foreach($plan->features as $index => $feature)
                                        <li class="flex items-center"><i class="{{ $feature->is_activated ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                            {{ $feature->featureSetting->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <a href="{{ route('virtual-address.show', $plan->slug) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                Learn More
                            </a>
                        </div>
                    @else
                        <div class="plan-card bg-white p-8 rounded-lg shadow-md border border-gray-200 flex flex-col">
                            <div class="card-content">
                                <h3 class="text-2xl font-semibold text-blue-700 mb-4 text-center">{{ $plan->name }}</h3>
                                <p class="text-4xl font-bold text-center text-blue-800 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                                <ul class="space-y-2 text-gray-600 mb-8 text-sm">
                                    @foreach($plan->features as $index => $feature)
                                        <li class="flex items-center"><i class="{{ $feature->is_activated ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                            {{ $feature->featureSetting->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <a href="{{ route('virtual-address.show', $plan->slug) }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                Learn More
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="cta-block relative text-center mt-16 rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                style="background-image: url('{{asset('images/home-banner.jpg')}}'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-black/60 rounded-lg"></div> <div class="relative z-10 p-10">
                    <p class="text-xl font-medium text-white mb-6 max-w-xl mx-auto"> Ready to establish your professional presence? Explore our detailed virtual address plans and find the perfect fit for your business.
                    </p>
                    <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        View All Virtual Address Plans <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="meeting-room" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-4">
                    <i class="fas fa-users mr-3 text-orange-500"></i>Meeting Rooms
                </h2>
                <p class="text-gray-700 max-w-2xl mx-auto">
                    Professional spaces available for your client meetings, team catch-ups, or interviews. Book by the hour or day.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($meetingRooms as $meetingRoom)
                    <div class="room-card rounded-lg shadow-lg overflow-hidden border border-gray-200">
                        <img src="{{ $meetingRoom->meeting_primary_image }}" alt="Meeting Room 1" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Meeting+Room+1';">
                        <div class="p-6 flex flex-col flex-grow card-content">
                            <h4 class="text-lg font-semibold text-blue-700 mb-3">{{ $meetingRoom->name }}</h4>
                            <p class="text-gray-600 text-sm mb-4">{{ $meetingRoom->intro }}</p>
                            <a href="{{ route('meeting-rooms.show',$meetingRoom->slug) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="cta-block relative text-center mt-16 rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                style="background-image: url('{{asset('images/home-banner.jpg')}}'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-black/60 rounded-lg"></div> <div class="relative z-10 p-10">
                    <p class="text-xl font-medium text-white mb-6 max-w-xl mx-auto"> Need a professional space for your next meeting? Browse our selection of fully-equipped meeting rooms available for booking.
                    </p>
                    <a href="meeting-rooms.html" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        Explore All Meeting Rooms <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="conference-room" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-4">
                    <i class="fas fa-chalkboard-teacher mr-3 text-orange-500"></i>Conference Rooms
                </h2>
                <p class="text-gray-700 max-w-2xl mx-auto">
                    Fully-equipped conference rooms for larger presentations, workshops, or board meetings. State-of-the-art technology included.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($conferenceRooms as $conferenceRoom)
                    <div class="room-card rounded-lg shadow-lg overflow-hidden border border-gray-200">
                        <img src="{{ $conferenceRoom->conference_primary_image }}" alt="Conference Room 1" class="w-full h-48 object-cover" onerror="this.onerror=null; this.src='https://placehold.co/400x250/cccccc/ffffff?text=Conference+Room+1';">
                        <div class="p-6 flex flex-col flex-grow card-content">
                            <h4 class="text-lg font-semibold text-blue-700 mb-3">{{ $conferenceRoom->name }}</h4>
                            <p class="text-gray-600 text-sm mb-4">{{ $conferenceRoom->intro }}</p>
                            <a href="{{ route('conference-rooms.show',$conferenceRoom->slug ) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="cta-block relative text-center mt-16 rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                style="background-image: url('{{asset('images/home-banner.jpg')}}'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-black/60 rounded-lg"></div> <div class="relative z-10 p-10">
                    <p class="text-xl font-medium text-white mb-6 max-w-xl mx-auto"> Planning a larger event or presentation? Discover our state-of-the-art conference rooms designed to impress.
                    </p>
                    <a href="{{ route('conference-rooms.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        Discover All Conference Rooms <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What is a virtual office address?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">A virtual office address provides your business with a physical mailing address and optional services like mail handling, without the need for dedicated office space. It enhances your professional image and provides a prime business location.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>How do I book a meeting or conference room?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">You can easily book meeting rooms or conference rooms through our online booking system. Select your desired date, time, and room, and complete the reservation. Contact us for recurring bookings or special requests.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What amenities are included with room bookings?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">All our meeting and conference rooms include high-speed Wi-Fi, comfortable seating, and access to basic amenities. Specific rooms may include whiteboards, projectors, display screens, or video conferencing equipment as detailed in the room description.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Can I receive mail and packages with a virtual address?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Yes, our virtual office address plans include mail receiving. Depending on your chosen plan, we offer mail holding, forwarding, and scanning services for your convenience.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What is the cancellation policy for room bookings?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Our cancellation policy varies based on the booking duration and notice period. Generally, cancellations made more than 48 hours in advance receive a full refund. Please refer to our detailed booking terms and conditions or contact support for specific cases.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="need-help" class="py-16 bg-blue-700 text-white">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-3xl font-semibold mb-4">Need Help or Have Questions?</h3>
            <p class="text-blue-100 mb-8 max-w-lg mx-auto">
                Our dedicated support team is here to assist you with any inquiries about our services or booking process. Get in touch today!
            </p>
            <a href="contact-us.html" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                Contact Support <i class="fas fa-headset ml-2"></i>
            </a>
        </div>
    </section>
@endsection

@section('js')
    <script>
        console.log("Homepage loaded with enhanced CTAs with backgrounds, Need Help section, and updated Footer.");

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

        // Add other JS here if needed (e.g., mobile menu toggle)

    </script>

@endsection
