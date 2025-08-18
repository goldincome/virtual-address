@extends('layouts.front')


@section('title')
    About Us - NURUD
@endsection

@section('description')
    About Us - NURUD
@endsection

@section('css')
    <style>
        /* Hero background */
        .hero-bg-about {
            background-image: url('{{ asset('images/about-us.jpg') }}');
            background-size: cover;
            background-position: center 30%;
        }
    </style>
@endsection

@section('content')
    <section class="hero-bg-about text-white relative">
        <div class="absolute inset-0 bg-blue-900 opacity-70"></div> <div class="container mx-auto px-6 py-24 md:py-32 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight">About NURUD</h1>
            <p class="text-lg md:text-xl mb-6 text-blue-100 max-w-3xl mx-auto">
                Empowering Businesses with Flexible and Professional Workspace Solutions.
            </p>
        </div>
    </section>
    <main class="container mx-auto px-6 py-16 md:py-20">

        <section id="what-we-do" class="mb-16 md:mb-20">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-800 mb-4">Empowering Your Business Growth</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        NURUD was founded with a clear goal: to provide startups, freelancers, and established businesses with the professional image and essential services they need to thrive, without the burden of traditional office overheads. We understand the challenges of modern business and offer flexible solutions that adapt to your needs.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        We offer prestigious Virtual Office Addresses complete with mail handling, professional Meeting Rooms for your important discussions, and fully-equipped Conference Rooms for larger events and presentations. Our focus is on providing high-quality, reliable services that allow you to concentrate on what you do best running your business.
                    </p>
                </div>
                <div>
                    <img src="images/business-people.jpg"
                         alt="Business growth concept"
                         class="w-full h-auto rounded-lg shadow-lg"
                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/cccccc/ffffff?text=Growth';">
                </div>
            </div>
        </section>

        <section id="why-choose-NURUD" class="py-16 md:py-20 bg-blue-50 rounded-lg px-6">
            <h2 class="text-3xl font-bold text-center text-blue-800 mb-12">Why Partner with NURUD?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="text-center p-6 bg-white rounded shadow">
                    <i class="fas fa-medal text-orange-500 text-4xl"></i>
                    <h4 class="text-xl font-semibold text-blue-700 mb-2">Uncompromising Quality</h4>
                    <p class="text-gray-600 text-sm">We provide premium locations, modern facilities, and meticulously maintained spaces to ensure the best professional image for your business.</p>
                </div>
                <div class="text-center p-6 bg-white rounded shadow">
                     <i class="fas fa-shield-alt text-orange-500 text-4xl"></i>
                    <h4 class="text-xl font-semibold text-blue-700 mb-2">Trusted & Reliable</h4>
                    <p class="text-gray-600 text-sm">Count on us for secure mail handling, dependable room bookings, and consistent service delivery. Your business operations are safe with us.</p>
                </div>
                <div class="text-center p-6 bg-white rounded shadow">
                    <i class="fas fa-headset text-orange-500 text-4xl"></i>
                    <h4 class="text-xl font-semibold text-blue-700 mb-2">Supportive Partnership</h4>
                    <p class="text-gray-600 text-sm">Our friendly and professional team is always ready to assist, ensuring you have the support you need to succeed.</p>
                </div>
            </div>
        </section>

        <section id="mission-vision" class="py-16 md:py-20">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                 <div class="order-last md:order-first">
                    <img src="images/our-mission.jpg"
                         alt="Vision and mission concept"
                         class="w-full h-auto rounded-lg shadow-lg"
                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/cccccc/ffffff?text=Mission';">
                </div>
                <div class="bg-white p-8 rounded-lg shadow">
                    <h2 class="text-3xl font-bold text-blue-800 mb-4">Our Mission & Vision</h2>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xl font-semibold text-orange-600 mb-2 flex items-center"><i class="fas fa-bullseye mr-2"></i> Our Mission</h4>
                            <p class="text-gray-700 leading-relaxed">To empower small and growing businesses by providing accessible, flexible, and professional workspace solutions that enhance their credibility and support their operational needs.</p>
                        </div>
                         <div>
                            <h4 class="text-xl font-semibold text-orange-600 mb-2 flex items-center"><i class="fas fa-binoculars mr-2"></i> Our Vision</h4>
                            <p class="text-gray-700 leading-relaxed">To be the leading provider of virtual office and flexible workspace solutions in Lagos, recognized for our commitment to quality, reliability, and exceptional customer support, fostering the success of the businesses we serve.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        </main>
@endsection
