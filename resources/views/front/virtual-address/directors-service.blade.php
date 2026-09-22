@extends('layouts.front')

@section('title')
    Directors' Service Address UK | Keep Your Home Address Private | SE18
@endsection

@section('description')
    Separate your personal home address from the public Companies House register. Use our Woolwich SE18 London service address for your directors' details — from £12.99/month.
@endsection

@section('keywords', "Directors Service Address, Service Address UK, Companies House Director Address, Protect Home Address, Director Service Address London SE18, Company Officer Address")

@section('jsonld')
    @php
        $faqs = [
            ['q' => 'What is a directors service address?', 'a' => 'A service address is the address shown on the Companies House register for a director or other company officer, in place of their usual residential address. It keeps your home address off the public register while still meeting your legal filing obligations.'],
            ['q' => 'Does Companies House accept a virtual service address?', 'a' => 'Yes. Companies House accepts any address where documents can be delivered to the director. A professional virtual service address at a real commercial location like ours is perfectly acceptable.'],
            ['q' => 'Is a service address the same as a registered office?', 'a' => 'No. The registered office is the statutory address of the company itself, where official company mail is delivered. A service address relates to individual officers (directors, secretaries, PSCs) to protect their home addresses. You can use ours for either or both.'],
            ['q' => 'Can I also use a service address for my register of members?', 'a' => 'Yes. Sections of the statutory registers require addresses for shareholders and people with significant control (PSCs). Our SE18 address can serve these entries too, keeping personal residential details private.'],
        ];
    @endphp
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Directors\' Service Address UK',
        'serviceType' => 'Directors\' Service Address',
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => 'Charlton Virtual Office',
            'url' => config('app.url'),
            'telephone' => '+442032474747',
        ],
        'areaServed' => 'GB',
    ]" />
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faqs),
    ]" />
@endsection

@section('content')
    <section class="hero-bg-director text-white relative" style="background-image: url('{{ asset('images/business-people.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <span class="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-semibold uppercase tracking-wider rounded-full mb-4">Privacy Protection · SE18 London</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Directors' Service Address</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Your home shouldn't be on the public register. Use a professional Woolwich, London SE18 service address to keep your personal details private.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">View Plans</a>
                <a href="{{ route('guides.company-registration') }}" class="inline-block bg-white text-blue-800 hover:bg-blue-50 font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">Company Formation Guide</a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl font-bold text-blue-800 mb-6">Why Directors Need a Service Address</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                When you incorporate a limited company in the UK, the directors' details — including their usual residential address — become part of the public Companies House register if you don't provide a separate service address.
            </p>
            <p class="text-gray-700 leading-relaxed mb-6">
                Using a service address is completely legal and widely recommended. It means official correspondence addressed to you as a director is delivered to a professional commercial address, while your home stays off the database that anyone can search online. It also protects you from junk mail, identity-targeting scams and unwanted visitors.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-shield-alt text-orange-500 mr-2"></i>Key benefits</h3>
                    <ul class="text-gray-600 text-sm space-y-2 leading-relaxed">
                        <li>• Home address removed from the public register</li>
                        <li>• A credible SE18 London business address</li>
                        <li>• Safe handling of statutory director mail</li>
                        <li>• Simple to change anytime via Companies House</li>
                    </ul>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><i class="fas fa-user-shield text-orange-500 mr-2"></i>Who it's for</h3>
                    <ul class="text-gray-600 text-sm space-y-2 leading-relaxed">
                        <li>• Directors of new and existing limited companies</li>
                        <li>• People with significant control (PSCs)</li>
                        <li>• Company secretaries & LLP members</li>
                        <li>• Anyone with an overseas or hard-to-reach address</li>
                    </ul>
                </div>
            </div>
            <div class="bg-blue-600 text-white rounded-lg p-6 mt-10 text-center">
                <p class="text-xl font-semibold mb-1">Keep your personal life personal.</p>
                <p class="text-blue-100 mb-4">Set up your SE18 directors' service address today.</p>
                <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg">Get Started</a>
            </div>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Directors' Service Address — FAQ</h2>
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
                Learn the full steps in our guide: <a href="{{ route('guides.company-registration') }}" class="text-orange-600 hover:underline font-semibold">how to register a limited company with a virtual address</a>.
            </p>
        </div>
    </section>
@endsection