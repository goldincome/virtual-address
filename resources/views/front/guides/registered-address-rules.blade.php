@extends('layouts.front')

@section('title')
    Can I Use a Virtual Office as a Registered Address? | UK Guide 2026
@endsection

@section('description')
    YES — here's how. A guide to using a virtual office as your UK registered office address under Companies House rules, what's allowed, what isn't, and how to do it right.
@endsection

@section('keywords', "Virtual Office Registered Address, Companies House Registered Office, Can I Use Virtual Office As Registered Address, UK Company Registered Office Rules, Registered Office Address Requirements")

@section('jsonld')
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => 'Can I Use a Virtual Office as a Registered Address?',
        'description' => 'A clear guide to the Companies House rules on registered office addresses and how a virtual office satisfies them.',
        'inLanguage' => 'en-GB',
        'publisher' => ['@type' => 'Organization', 'name' => 'Charlton Virtual Office', 'url' => config('app.url')],
        'mainEntityOfPage' => config('app.url') . '/guides/can-i-use-a-virtual-office-as-registered-address',
        'dateModified' => now()->toIso8601String(),
    ]" />
@endsection

@section('content')
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ url('/') }}" class="hover:text-orange-600">Home</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-blue-800 font-semibold">Can I use a virtual office as a registered address?</span>
            </nav>

            <h1 class="text-4xl md:text-5xl font-bold text-blue-800 mb-4">Can I Use a Virtual Office as a Registered Address?</h1>
            <p class="text-gray-600 text-lg mb-2">The short answer: <strong class="text-blue-800">yes — with an important caveat.</strong></p>
            <p class="text-gray-500 text-sm mb-10">Last updated: {{ now()->format('j F Y') }} · Reading time: 5 minutes</p>

            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-blue-800">1. What Companies House actually requires</h2>
                <p>
                    Under the Companies Act 2006, every UK company must have a <em>registered office</em>: an address where statutory communications and legal documents can be delivered and are recorded. The rules (sections 86 and 87 of the Act) require that it is a physical address. Companies House states it must be "a location where, if a document is delivered to that address, it will reach the company" — and, importantly, "must not be a PO Box."
                </p>
                <div class="bg-blue-50 border-l-4 border-orange-500 p-5 rounded-r-lg">
                    <p class="font-semibold text-blue-800 mb-1">The key rule</p>
                    <p>A virtual office address is acceptable for a <strong>registered office</strong> as long as it is an actual physical premises where mail is received — which is exactly what our Woolwich SE18 office is.</p>
                </div>

                <h2 class="text-2xl font-bold text-blue-800">2. The "occupier" question for overseas companies</h2>
                <p>
                    For UK-incorporated companies, using a virtual registered office is straightforward: you appoint the address, and our team handles your statutory mail. For <strong>overseas companies</strong> (opening a UK establishment), Companies House additionally requires a <em>person at the address</em> who is authorised to accept service. As a UK mail-handling provider, we can act as that point of contact — but check with a formation agent if your situation is complex.
                </p>

                <h2 class="text-2xl font-bold text-blue-800">3. What a virtual registered office can and can't be used for</h2>
                <p>Understanding the boundaries keeps you compliant:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                        <h3 class="font-semibold text-green-700 mb-3">Perfectly fine</h3>
                        <ul class="text-sm space-y-2">
                            <li>• Registered office address (Companies House)</li>
                            <li>• Directors' service address</li>
                            <li>• PSC & shareholder addresses</li>
                            <li>• General business correspondence & invoices</li>
                            <li>• Receiving statutory mail & legal documents</li>
                        </ul>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-5">
                        <h3 class="font-semibold text-red-700 mb-3">Not appropriate</h3>
                        <ul class="text-sm space-y-2">
                            <li>• Trading "operating address" if you trade from a shop/office elsewhere (different rules apply)</li>
                            <li>• Business rates / VAT registration where occupancy matters (check HMRC)</li>
                            <li>• Claiming a physical presence you don't have for banking or licences</li>
                        </ul>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-blue-800">4. Why it's a smart choice for small businesses</h2>
                <p>
                    Thousands of UK businesses — from consultants and tradespeople to e-commerce sellers — use a virtual registered office. It keeps your home address off the public register, gives you a credible London presence, and typically costs far less than a lease. You can also add mail scanning and a meeting room for the days you need face-to-face time.
                </p>

                <div class="bg-blue-600 text-white rounded-lg p-6 text-center">
                    <p class="text-xl font-semibold mb-1">Use our Companies House-ready SE18 address.</p>
                    <p class="text-blue-100 mb-4">Registered office, service address & mail handling from £12.99/month.</p>
                    <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg">View Registered Office Plans</a>
                </div>

                <h2 class="text-2xl font-bold text-blue-800">Need the full step-by-step?</h2>
                <p>
                    Walk through the incorporation process with our guide to <a href="{{ route('guides.company-registration') }}" class="text-orange-600 font-semibold hover:underline">registering a limited company with a virtual address</a>, or compare your options in <a href="{{ route('guides.comparison') }}" class="text-orange-600 font-semibold hover:underline">virtual office vs serviced office vs coworking</a>.
                </p>
            </div>
        </div>
    </section>
@endsection