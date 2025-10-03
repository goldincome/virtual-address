@extends('layouts.front')


@section('title', "Contact Us")

@section('description')
   Contact Charlton Virtual Office in Woolwich, London for support with Virtual Office, Meeting Room, and Conference Room services.
@endsection

@section('keywords', "Contact Us, Virtual Office, Meeting Room, Conference Room, Business Address, Mail Handling, London, Woolwich, Charlton")

@section('css')
    <style>
        /* Hero background */
        .hero-bg-contact {
            background-image: url('{{ asset('images/conference.jpg') }}');
            background-size: cover;
            background-position: center;
        }
    </style>
@endsection

@section('content')
    <section class="hero-bg-contact text-white relative">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 md:py-32 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight">Get In Touch</h1>
            <p class="text-lg md:text-xl mb-6 text-blue-100 max-w-3xl mx-auto">
                We're here to help! Whether you have questions about our services, need support, or want to discuss your
                specific needs, reach out to us.
            </p>
        </div>
    </section>
    <main class="container mx-auto px-6 py-16 md:py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

            <div class="space-y-10">
                <section>
                    <h2 class="text-2xl font-semibold text-blue-800 mb-4">Contact Information</h2>
                    <div class="space-y-4 text-gray-700">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt fa-fw text-orange-500 mt-1 mr-3"></i>
                            <span>Unit 6, Block 3, Dockyard Industrial Estate,<br>Church Street, Woolwich, London UK</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-phone-alt fa-fw text-orange-500 mt-1 mr-3"></i>
                            <a href="tel:+4420000000000" class="hover:text-orange-600">+44 (0) 2032474747</a>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-envelope fa-fw text-orange-500 mt-1 mr-3"></i>
                            <a href="mailto:support@Charlton Virtual Office.com" class="hover:text-orange-600">support@charltonvirtualoffice.com</a>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock fa-fw text-orange-500 mt-1 mr-3"></i>
                            <span>Mon - Fri: 9:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                    <div class="mt-6 h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">

                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2483.808834525843!2d0.062277277967773!3d51.49837297180839!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47d8a7c8f7a9a0a5%3A0x3b9a3b0b0b0b0b0b!2sDockyard%20Industrial%20Estate%2C%20London%20SE18%205PQ!5e0!3m2!1sen!2suk!4v1712345678901"
                            width="100%" height="200" style="border:0" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" class="rounded-lg">
                        </iframe>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-blue-800 mb-4">How Can We Help?</h2>
                    <p class="text-gray-700 mb-4">Reach out to us for:</p>
                    <ul class="list-none space-y-2 text-gray-700">
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Inquiries
                            about Virtual Office Address plans.</li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Booking
                            Meeting Rooms or Conference Rooms.</li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Questions
                            about amenities and services.</li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Custom
                            requirements or long-term bookings.</li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Support with
                            existing services or accounts.</li>
                    </ul>
                </section>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-blue-800 mb-6">Send Us a Message</h2>
                @include('front.common.error-and-message')
                <form action="{{ route('contact-us.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                            class="w-full px-4 py-2 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        @error('subject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type of Request</label>
                        <select id="type" name="support_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Select an option</option>
                            <option value="Enquiry" {{ old('support_type') == 'Enquiry' ? 'selected' : '' }}>Enquiry</option>
                            <option value="Support Request" {{ old('support_type') == 'Support Request' ? 'selected' : '' }}>Support
                                Request</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-2 border @error('message') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </main>
@endsection
