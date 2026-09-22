@extends('layouts.front')

@section('title')
    Mail Forwarding & Scanning Service | Virtual Mail | London SE18
@endsection

@section('description')
    Zero in on what matters — let us handle your post. Mail receiving, forwarding anywhere in the UK & internationally, plus on-demand scanning from our Woolwich SE18 mail centre.
@endsection

@section('keywords', "Mail Forwarding, Virtual Mailbox, Mail Scanning London, Virtual Mail Service UK, Mail Receiving Company, Post Forwarding SE18, Business Mail Handling")

@section('jsonld')
    @php
        $faqs = [
            ['q' => 'How fast is mail forwarded?', 'a' => 'Mail is typically processed and dispatched within 1–2 working days of arrival. UK First Class usually arrives within 1–2 days after dispatch; international forwarding is sent tracked depending on destination.'],
            ['q' => 'Can I have my mail scanned instead of forwarded?', 'a' => 'Yes. With an eligible plan, we can open, scan and email you a copy of each item so you can view your post from anywhere in the world — requesting forwarding only for the items you actually want delivered.'],
            ['q' => 'Can you forward mail internationally?', 'a' => 'Yes. We forward to any address in the UK and to international destinations, using tracked postal services so important documents and parcels can be traced.'],
            ['q' => 'What happens to parcels and larger packages?', 'a' => 'Parcels are held securely on site. We notify you of arrival and arrange a convenient forwarding, collection or redelivery based on your plan and the package size.'],
        ];
    @endphp
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Mail Forwarding & Scanning Service',
        'serviceType' => 'Mail Forwarding & Scanning',
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => 'Charlton Virtual Office',
            'url' => config('app.url'),
            'telephone' => '+442032474747',
        ],
        'areaServed' => ['GB', 'EU', 'USA', 'Rest of World'],
    ]" />
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faqs),
    ]" />
@endsection

@section('content')
    <section class="hero-bg-mail text-white relative" style="background-image: url('{{ asset('images/virtual-office.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <span class="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-semibold uppercase tracking-wider rounded-full mb-4">Virtual Mailbox & Scanning</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Mail Forwarding & Scanning Service</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Have your post received, held, scanned and forwarded from our secure Woolwich mail centre — so you can run a paper business without an office.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Choose Your Plan</a>
                <a href="{{ route('local.woolwich') }}" class="inline-block bg-white text-blue-800 hover:bg-blue-50 font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Visit Our Woolwich Base</a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">A Virtual Mailbox That Actually Works</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Every virtual office plan at Charlton Virtual Office includes our mail handling service. Your business mail arrives at our commercial address in Woolwich, where we hold it securely and handle it exactly how you choose.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                    <i class="fas fa-inbox text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">1. Receive</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Letters, invoices, contracts and packages are signed for and held safely at our SE18 commercial premises — no more missed deliveries at home.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                    <i class="fas fa-file-alt text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">2. Scan (optional)</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Ask us to open and scan items, and you'll get a clear copy by email — read your post from anywhere in the world, minutes after it lands.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                    <i class="fas fa-shipping-fast text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2">3. Forward</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Send the originals on to you — anywhere in the UK or internationally — using tracked services so nothing is ever lost.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">Perfect for Remote & Travel-Focused Businesses</h2>
            <p class="text-gray-700 leading-relaxed mb-8">
                Whether you're a solo consultant, an e-commerce seller handling marketplace returns, or a director who splits time between cities, a virtual mailbox removes the friction of physical post. Key benefits:
            </p>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700 text-sm mb-8">
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> A stable address that never moves when you do</li>
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> View post remotely via email scanning</li>
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Forward single items on demand</li>
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Parcels signed for on your behalf</li>
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Secure handling of statutory and legal mail</li>
                <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> No trips to the sorting office</li>
            </ul>
            <div class="bg-blue-600 text-white rounded-lg p-6 text-center">
                <p class="text-xl font-semibold mb-1">Never miss another delivery.</p>
                <p class="text-blue-100 mb-4">Your mail, handled. Sign up today.</p>
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg">View Mail Plans</a>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Mail Forwarding — FAQ</h2>
            <div class="space-y-4">
                @foreach ($faqs as $faq)
                    <details class="faq-row p-5 rounded-lg bg-gray-50 shadow group">
                        <summary class="list-none cursor-pointer text-lg font-semibold text-blue-700 flex justify-between items-center">
                            {{ $faq['q'] }}<i class="fas fa-chevron-down text-orange-500 transition-transform duration-300 group-open:rotate-180"></i>
                        </summary>
                        <p class="text-gray-600 mt-3 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endsection