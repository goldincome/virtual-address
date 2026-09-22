@extends('layouts.front')

@section('title')
    Virtual Office in Woolwich, London SE18 | Charlton Virtual Office
@endsection

@section('description')
    Charlton Virtual Office is a virtual office address, registered office and meeting room provider in Woolwich SE18. Serving Charlton, Greenwich, Plumstead, Abbey Wood & Thamesmead. From £12.99/month.
@endsection

@section('keywords', "Virtual Office Woolwich, Virtual Address SE18, Registered Office Woolwich, Meeting Rooms Woolwich, Virtual Office Greenwich, Virtual Office Charlton, Mail Forwarding Woolwich, Virtual Office Plumstead, Virtual Office Abbey Wood, Virtual Office Thamesmead")

@section('jsonld')
    @if(class_exists('App\\Models\\Product'))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "{{ config('app.url') }}/virtual-office-woolwich-london#business",
        "name": "Charlton Virtual Office",
        "description": "Virtual office addresses, registered office, mail forwarding and meeting room hire in Woolwich, London SE18.",
        "url": "{{ config('app.url') }}/virtual-office-woolwich-london",
        "telephone": "+442032474747",
        "email": "support@charltonvirtualoffice.com",
        "priceRange": "££",
        "image": "{{ asset('images/virtual-office-address.jpg') }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich",
            "addressLocality": "London",
            "postalCode": "SE18 5PQ",
            "addressCountry": "GB"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": 51.4932,
            "longitude": 0.0536
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens": "09:00",
                "closes": "18:00"
            }
        ],
        "areaServed": [
            { "@type": "City", "name": "Woolwich" },
            { "@type": "City", "name": "Charlton" },
            { "@type": "City", "name": "Greenwich" },
            { "@type": "City", "name": "Plumstead" },
            { "@type": "City", "name": "Abbey Wood" },
            { "@type": "City", "name": "Thamesmead" }
        ]
    }
    </script>
    @endif
@endsection

@section('content')
    <section class="hero-bg-virtual text-white relative" style="background-image: url('{{ asset('images/virtual-office-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <span class="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-semibold uppercase tracking-wider rounded-full mb-4">Woolwich · SE18 · South East London</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Virtual Office in Woolwich, London</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                A prestigious business address, registered office, mail forwarding and meeting rooms — based in the heart of Royal Greenwich, London SE18.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">View Virtual Office Plans</a>
                <a href="{{ route('meeting-rooms.index') }}" class="inline-block bg-white text-blue-800 hover:bg-blue-50 font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Book a Meeting Room</a>
            </div>
        </div>
    </section>

    <section id="nap" class="py-12 bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100 flex flex-col">
                    <h2 class="text-lg font-semibold text-blue-800 mb-3"><i class="fas fa-map-marker-alt mr-2 text-orange-500"></i> Address</h2>
                    <address class="text-gray-700 not-italic text-sm leading-relaxed">
                        Charlton Virtual Office<br>
                        Unit 6, Block 3, Dockyard Industrial Estate<br>
                        Church Street, Woolwich<br>
                        London, SE18 5PQ, United Kingdom
                    </address>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100 flex flex-col">
                    <h2 class="text-lg font-semibold text-blue-800 mb-3"><i class="fas fa-phone-alt mr-2 text-orange-500"></i> Phone</h2>
                    <p class="text-gray-700 text-sm"><a href="tel:+442032474747" class="hover:text-orange-600">+44 (0) 203 247 4747</a></p>
                    <h2 class="text-lg font-semibold text-blue-800 mt-5 mb-2"><i class="fas fa-envelope mr-2 text-orange-500"></i> Email</h2>
                    <p class="text-gray-700 text-sm"><a href="mailto:support@charltonvirtualoffice.com" class="hover:text-orange-600">support@charltonvirtualoffice.com</a></p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100 flex flex-col">
                    <h2 class="text-lg font-semibold text-blue-800 mb-3"><i class="fas fa-train mr-2 text-orange-500"></i> Getting Here</h2>
                    <ul class="text-gray-700 text-sm space-y-1 leading-relaxed">
                        <li>Woolwich Arsenal (DLR): ~8 min walk</li>
                        <li>Woolwich (Elizabeth line): ~12 min walk</li>
                        <li>Woolwich Church Street buses: 53, 54, 96, 99, 177, 380, 422, 472</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="map" class="py-12 bg-gray-50">
        <div class="container mx-auto px-6 max-w-7xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-2">Find Us in Woolwich</h2>
            <p class="text-center text-gray-600 mb-8 max-w-2xl mx-auto">Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich, London SE18 5PQ</p>
            <div class="rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <iframe
                    src="https://www.google.com/maps?q=51.4932,0.0536&z=15&output=embed"
                    width="100%"
                    height="420"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Charlton Virtual Office, Unit 6 Block 3, Dockyard Industrial Estate, Church Street, Woolwich, London SE18 5PQ"></iframe>
            </div>
        </div>
    </section>

    <section id="intro" class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">A Professional Business Address in Woolwich</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Based at the Dockyard Industrial Estate off Church Street, Charlton Virtual Office gives your business a real, verifiable SE18 London address. Use it on your website, business cards and marketing — and register it with Companies House as your UK registered office address.
            </p>
            <p class="text-gray-700 leading-relaxed mb-4">
                Woolwich sits on the Elizabeth line and DLR, making it one of the fastest-connected areas of South East London. That means an address here carries genuine weight with clients, suppliers and banks — at a fraction of the cost of a Canary Wharf or City postcode.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Beyond the address, we handle your incoming post with reliable mail forwarding and scanning, and offer a fully equipped meeting room and conference room that you can book by the hour when you need face-to-face time.
            </p>
        </div>
    </section>

    <section id="services" class="py-16 bg-blue-50">
        <div class="container mx-auto px-6 max-w-7xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">What We Offer in SE18</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <a href="{{ route('virtual-address.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-envelope-open-text text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Virtual Business Address</h3>
                    <p class="text-gray-600 text-sm">Use our Woolwich SE18 address for your business correspondence, website and marketing from £12.99/month.</p>
                </a>
                <a href="{{ route('virtual-address.registered-office') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-landmark text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Registered Office Address</h3>
                    <p class="text-gray-600 text-sm">A Companies House-ready registered office address for your UK limited company, with mail handling included.</p>
                </a>
                <a href="{{ route('virtual-address.mail-forwarding') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-shipping-fast text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Mail Forwarding & Scanning</h3>
                    <p class="text-gray-600 text-sm">Forward post to any UK or international address, or have it scanned and emailed to you.</p>
                </a>
                <a href="{{ route('meeting-rooms.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-users text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Meeting Room Hire</h3>
                    <p class="text-gray-600 text-sm">A professional, private meeting room in Woolwich with 4K screen and video conferencing — hourly or daily.</p>
                </a>
                <a href="{{ route('conference-rooms.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-chalkboard-teacher text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Conference Room Hire</h3>
                    <p class="text-gray-600 text-sm">A larger boardroom-style space for presentations, workshops and bigger client meetings.</p>
                </a>
                <a href="{{ route('virtual-address.directors-service') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300 border border-gray-200 block">
                    <i class="fas fa-id-card text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Directors' Service Address</h3>
                    <p class="text-gray-600 text-sm">Protect your home address from the public Companies House register with a separate service address.</p>
                </a>
            </div>
        </div>
    </section>

    <section id="areas-we-serve" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-4">Areas We Serve Around Woolwich</h2>
            <p class="text-center text-gray-600 mb-12 max-w-3xl mx-auto">
                Our Woolwich base sits within easy reach of the towns and neighbourhoods of Royal Greenwich and Bexley. Business owners across South East London use our SE18 address and book our rooms every week.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Woolwich (SE18)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Our home neighbourhood. Local trades, consultants and startups use the SE18 address to stay local while looking established. Clients can reach us on foot from Woolwich Arsenal DLR in about 8 minutes, or five stops from Canary Wharf on the Elizabeth line, making Woolwich popular with professionals who commute into central London but want a local SE London presence.
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Charlton (SE7)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Just two miles west of the office and served by direct buses (53, 54, 380) plus Charlton railway station. Charlton-based builders, property agents and home businesses appreciate a professional address that is distinct from their home, with meeting space a short ride away for client sign-offs.
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Greenwich (SE10)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Around 3 miles from our base, Greenwich businesses get the prestige of a Royal London address while our SE18 pricing stays far below Greenwich's serviced-office rents. Reaching us is simple on the DLR from Greenwich Cutty Sark or the 177 bus along the river — and the meeting room is ideal for Greenwich-based creative and consultancy teams.
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Plumstead (SE18)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        A five-minute drive or a short bus trip from the Dockyard Estate. Plumstead has a growing community of sole traders and small firms; a registered office address here's common, but keeping it at a commercial estate in Woolwich keeps home addresses private while mail and parcels are handled safely on site.
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Abbey Wood (SE2)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Eight minutes on the 469 or 472 bus from Abbey Wood station, and well connected via the Elizabeth line into Woolwich. Young online businesses in Abbey Wood use our mailing address for supplier invoices and e-commerce returns, with mail scanned and forwarded so they never need to visit the post office.
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-xl font-semibold text-blue-700 mb-3"><i class="fas fa-map-pin text-orange-500 mr-2"></i>Thamesmead (SE28)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Reachable by bus (472, 99) in around 15 minutes. Thamesmead's regeneration has attracted new start-ups, digital agencies and trades businesses. They use our SE18 address as a stable, professional point of presence while our mail scanning keeps them fully remote-friendly — no room required.
                    </p>
                </div>
            </div>

            <div class="mt-10 bg-blue-50 border border-blue-100 rounded-lg p-6 text-center">
                <p class="text-gray-700">
                    Also happy to help clients from <strong>Blackheath (SE3)</strong>, <strong>Eltham (SE9)</strong>, <strong>Lewisham (SE13)</strong> and the wider <strong>South East London</strong> area. If you're not sure the location suits you, call <a href="tel:+442032474747" class="text-orange-600 font-semibold hover:underline">+44 (0) 203 247 4747</a> — we'll talk it through.
                </p>
            </div>
        </div>
    </section>

    <section id="cta" class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-semibold mb-4">Set Up Your Woolwich Business Address Today</h2>
            <p class="text-blue-100 mb-8 max-w-xl mx-auto">Get a professional SE18 address in minutes, with mail handling from £12.99/month. No long leases, no hidden fees.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Choose a Plan</a>
                <a href="{{ route('contact-us.index') }}" class="inline-block bg-white text-blue-800 hover:bg-blue-50 font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Ask a Question</a>
            </div>
        </div>
    </section>

    <section id="faq" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Virtual Office in Woolwich — FAQ</h2>
            <div class="space-y-4">
                <details class="faq-row p-5 rounded-lg bg-gray-50 shadow group">
                    <summary class="list-none cursor-pointer text-lg font-semibold text-blue-700 flex justify-between items-center">
                        Does a virtual office give me a real Woolwich street address?<i class="fas fa-chevron-down text-orange-500 transition-transform duration-300 group-open:rotate-180"></i>
                    </summary>
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">Yes. Your virtual office uses our physical business premises at Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich SE18 5PQ — a genuine commercial address you can use for mail, invoices and Companies House registration.</p>
                </details>
                <details class="faq-row p-5 rounded-lg bg-gray-50 shadow group">
                    <summary class="list-none cursor-pointer text-lg font-semibold text-blue-700 flex justify-between items-center">
                        Is SE18 a good postcode for a registered office?<i class="fas fa-chevron-down text-orange-500 transition-transform duration-300 group-open:rotate-180"></i>
                    </summary>
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">Yes. Woolwich is part of Royal Greenwich and sits on the Elizabeth line and DLR, with strong transport links to central London. It's a credible, affordable alternative to more expensive central and city postcodes — popular with sole traders, limited companies and e-commerce businesses across South East London.</p>
                </details>
                <details class="faq-row p-5 rounded-lg bg-gray-50 shadow group">
                    <summary class="list-none cursor-pointer text-lg font-semibold text-blue-700 flex justify-between items-center">
                        Can clients actually visit my virtual office?<i class="fas fa-chevron-down text-orange-500 transition-transform duration-300 group-open:rotate-180"></i>
                    </summary>
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">Yes. You're free to use the address for correspondence, and you can book our meeting room in Woolwich by the hour whenever you need to host clients face to face — a professional venue that matches the address on your business card.</p>
                </details>
                <details class="faq-row p-5 rounded-lg bg-gray-50 shadow group">
                    <summary class="list-none cursor-pointer text-lg font-semibold text-blue-700 flex justify-between items-center">
                        How quickly can I start using the address?<i class="fas fa-chevron-down text-orange-500 transition-transform duration-300 group-open:rotate-180"></i>
                    </summary>
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">Almost immediately. Once you pick a plan and complete checkout, your Woolwich SE18 address is active and ready to use on your website, business cards and company registration paperwork.</p>
                </details>
            </div>
        </div>
    </section>
@endsection