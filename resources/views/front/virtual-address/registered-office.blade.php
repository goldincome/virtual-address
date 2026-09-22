@extends('layouts.front')

@section('title')
    Registered Office Address UK | Companies House Ready | SE18 Woolwich
@endsection

@section('description')
    A Companies House-ready registered office address in Woolwich, London SE18 from £12.99/month. Mail accepting, forwarding & scanning included. Set up your UK registered office in minutes.
@endsection

@section('keywords', "Registered Office Address, Companies House Address, Registered Office London SE18, Registered Office UK, Business Address For Company, Companies House Registered Office Woolwich")

@section('jsonld')
    @php
        $faqs = [
            ['q' => 'Can I use a registered office address without a physical office?', 'a' => 'Yes. A virtual registered office is a legitimate way to satisfy the Companies House requirement for a UK registered office address, provided the address is a physical location where mail is accepted and can be recorded. There is no requirement to occupy the premises.'],
            ['q' => 'Is the SE18 address accepted by companies house?', 'a' => 'Yes. Our address at Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich, London SE18 5PQ is a physical commercial premise, fully eligible to be used as your registered office address.'],
            ['q' => 'Do you accept Companies House mail and legal documents?', 'a' => 'Yes. We accept and hold all official mail including Companies House correspondence, HMRC letters and other legal post, then forward or scan it to you according to your plan.'],
            ['q' => 'What is the difference between a registered office and a service address?', 'a' => 'A registered office is the statutory address of the company shown on the Companies House register, where statutory mail is delivered. A service address is used for the personal details of directors or shareholders to keep their home address private.'],
        ];
    @endphp
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Registered Office Address UK',
        'serviceType' => 'Registered Office Address',
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => 'Charlton Virtual Office',
            'url' => config('app.url'),
            'telephone' => '+442032474747',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Unit 6, Block 3, Dockyard Industrial Estate, Church Street, Woolwich',
                'addressLocality' => 'London',
                'postalCode' => 'SE18 5PQ',
                'addressCountry' => 'GB',
            ],
        ],
        'areaServed' => 'GB',
        'offers' => [
            '@type' => 'Offer',
            'price' => '12.99',
            'priceCurrency' => 'GBP',
        ],
    ]" />
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faqs),
    ]" />
@endsection

@section('content')
    <section class="hero-bg-address text-white relative" style="background-image: url('{{ asset('images/virtual-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <span class="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-semibold uppercase tracking-wider rounded-full mb-4">Companies House Ready · SE18 London</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Registered Office Address in the UK</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Every UK limited company needs a registered office address. Ours is a genuine Woolwich, London SE18 commercial address — ready for your incorporation in minutes.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">View Plans</a>
                <a href="{{ route('contact-us.index') }}" class="inline-block bg-white text-blue-800 hover:bg-blue-50 font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Speak to Us</a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">What Is a Registered Office Address?</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                Under the Companies Act 2006, every UK limited company must have a registered office: an official address where Companies House and other statutory bodies can deliver legal mail. It appears on the public register and must be a real physical location — not a PO Box.
            </p>
            <p class="text-gray-700 leading-relaxed mb-4">
                A virtual registered office gives you that official SE18 London address while you work from home, a co-working space, or anywhere at all. It's a legal requirement many people avoid until the last minute, yet it's one of the simplest things to arrange — and we make it automatic, with mail handling built in.
            </p>
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-6 mt-8">
                <h3 class="text-xl font-semibold text-blue-800 mb-3"><i class="fas fa-check-circle text-orange-500 mr-2"></i>What's included</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700 text-sm">
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Companies House-ready SE18 address</li>
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Official & legal mail accepted and held</li>
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Mail forwarding within the UK or abroad</li>
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Optional email scanning of each item</li>
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> Instant activation after checkout</li>
                    <li class="flex items-start"><i class="fas fa-check text-orange-500 mt-1 mr-2"></i> No lease, no deposit, cancel anytime</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">Why Businesses Choose Our Registered Office</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-home text-orange-500 mr-2"></i>Keep Your Home Address Private</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">If you run your company from home, your domestic address can end up on the public register. A virtual registered office keeps your personal life private and untouched by statutory mail.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-building text-orange-500 mr-2"></i>A Credible London Presence</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">An SE18 Woolwich address carries real weight with clients, lenders and suppliers — establishing that your company is an established UK business rather than a home-run side project.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-envelope text-orange-500 mr-2"></i>Never Miss Statutory Mail</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Companies House, HMRC and HMRC-adjacent correspondence is time-sensitive. We hold every item securely and scan or forward it so nothing gets lost or overlooked.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-pound-sign text-orange-500 mr-2"></i>Affordable & Flexible</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">A traditional office lease in London is far beyond most small firms. Our registered office plans start at £12.99/month with no long-term commitment.</p>
                </div>
            </div>
            <div class="bg-blue-600 text-white rounded-lg p-6 mt-10 text-center">
                <p class="text-xl font-semibold mb-1">Ready to use our SE18 address on your next Companies House filing?</p>
                <p class="text-blue-100 mb-4">It takes minutes to set up — no office, no lease, no hassle.</p>
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg">Get Your Registered Office</a>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Registered Office Address — FAQ</h2>
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
            <p class="text-center text-gray-600 mt-8 text-sm">
                Want to dig deeper? See our full explainer: <a href="{{ route('guides.registered-rules') }}" class="text-orange-600 hover:underline font-semibold">can I use a virtual office as a registered address?</a>
            </p>
        </div>
    </section>
@endsection