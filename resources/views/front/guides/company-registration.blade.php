@extends('layouts.front')

@section('title')
    How to Register a Limited Company with a Virtual Address | Step-by-Step UK
@endsection

@section('description')
    Form a UK limited company online using a virtual registered office address. We walk through Companies House registration, director details, PSC filings and getting started in under an hour.
@endsection

@section('keywords', "Register Limited Company Virtual Address, Company Formation Virtual Office, Companies House Registration Steps, Form Ltd Company Online UK, Incorporation Registered Office, Setup Company With Virtual Address")

@section('jsonld')
    <x-jsonld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => 'How to Register a Limited Company with a Virtual Address',
        'description' => 'Step-by-step guide to incorporating a UK limited company using a virtual registered office address.',
        'inLanguage' => 'en-GB',
        'publisher' => ['@type' => 'Organization', 'name' => 'Charlton Virtual Office', 'url' => config('app.url')],
        'mainEntityOfPage' => config('app.url') . '/guides/register-limited-company-with-virtual-address',
        'dateModified' => now()->toIso8601String(),
    ]" />
@endsection

@section('content')
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ url('/') }}" class="hover:text-orange-600">Home</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-blue-800 font-semibold">Register a limited company with a virtual address</span>
            </nav>

            <h1 class="text-4xl md:text-5xl font-bold text-blue-800 mb-4">How to Register a Limited Company with a Virtual Address</h1>
            <p class="text-gray-600 text-lg mb-2">Incorporating is faster than most people think — you can be registered in under an hour, paperwork-free.</p>
            <p class="text-gray-500 text-sm mb-10">Last updated: {{ now()->format('j F Y') }} · Reading time: 7 minutes</p>

            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-blue-800">Step 1 — Decide on your registered office address</h2>
                <p>
                    Every limited company needs a registered office. Order of operations matters here: get your <a href="{{ route('virtual-address.registered-office') }}" class="text-orange-600 font-semibold hover:underline">registered office address sorted first</a>. With a virtual address like our Woolwich SE18 one, you can file the moment you've chosen a plan — no waiting for a lease or a practical test (with <a href="{{ route('local.woolwich') }}" class="text-orange-600 font-semibold hover:underline">our Woolwich office</a>, real mail arrives at a real commercial estate).
                </p>

                <h2 class="text-2xl font-bold text-blue-800">Step 2 — Prepare your company details</h2>
                <p>Have these to hand before you log into Companies House:</p>
                <ul>
                    <li>1. <strong>Company name</strong> — check it's available (search the Companies House register).</li>
                    <li>2. <strong>SIC code</strong> — the 5-digit code describing your business activity.</li>
                    <li>3. <strong>Directors' details</strong> — name, date of birth, nationality, occupation, country of residence.</li>
                    <li>4. <strong>Directors' service addresses</strong> — use a professional one, not your home, for privacy.</li>
                    <li>5. <strong>Shareholders, share types and amounts</strong> — most start with 1 share at £1. Each shareholder also needs an address for the register.</li>
                    <li>6. <strong>PSC (People with Significant Control) details</strong> — typically the shareholders if they hold more than 25%.</li>
                </ul>

                <h2 class="text-2xl font-bold text-blue-800">Step 3 — Register online (usually £50)</h2>
                <p>
                    Log in to Companies House WebFiling or use a nominated agent. You'll select a director-based or shareholder-based registration, enter the details above, pay the £50 fee, and receive your certificate of incorporation — typically the same day. Your virtual registered office simply gets entered like any other address.
                </p>
                <div class="bg-blue-50 border-l-4 border-orange-500 p-5 rounded-r-lg">
                    <p class="font-semibold text-blue-800 mb-1">Good to know</p>
                    <p>Companies House files are public — anyone can look up directors' details. Using our SE18 address for the registered office <em>and</em> your director service addresses keeps home addresses private from day one.</p>
                </div>

                <h2 class="text-2xl font-bold text-blue-800">Step 4 — Set up for business (the important admin)</h2>
                <p>After incorporation you'll need to:</p>
                <ul>
                    <li>• Get your <strong>Unique Taxpayer Reference (UTR)</strong> — HMRC posts it to your registered office, so make sure mail handling is in place (ours accepts and forwards it).</li>
                    <li>• Register for <strong>corporation tax</strong> within 3 months of trading.</li>
                    <li>• Open a <strong>business bank account</strong> — a real SE18 commercial address helps verification.</li>
                    <li>• Keep statutory registers and file confirmation statements (annually, £34) and accounts on time.</li>
                </ul>

                <h2 class="text-2xl font-bold text-blue-800">Step 5 — Protect directors' privacy with a service address</h2>
                <p>
                    Your home address is only kept off the public record if you give Companies House a separate <a href="{{ route('virtual-address.directors-service') }}" class="text-orange-600 font-semibold hover:underline">directors' service address</a>. We provide one as part of our virtual office plans — and it also becomes the default for your filing obligations, so the register never shows your domestic address.
                </p>

                <div class="bg-blue-600 text-white rounded-lg p-6 text-center">
                    <p class="text-xl font-semibold mb-1">Incorporate in minutes, not days.</p>
                    <p class="text-blue-100 mb-4">Our SE18 registered office & mail handling means your UTR and statutory post arrive somewhere safe.</p>
                    <a href="{{ route('virtual-address.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg">Start With a Virtual Address</a>
                </div>

                <h2 class="text-2xl font-bold text-blue-800">Still deciding? Read on</h2>
                <p>
                    See <a href="{{ route('guides.registered-rules') }}" class="text-orange-600 font-semibold hover:underline">whether a virtual office can be your registered address</a> (spoiler: yes), or check <a href="{{ route('guides.best-areas') }}" class="text-orange-600 font-semibold hover:underline">the best SE London areas to register a business</a>.
                </p>
            </div>
        </div>
    </section>
@endsection