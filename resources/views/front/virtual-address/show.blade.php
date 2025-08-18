@extends('layouts.front')


@section('title')
    Virtual Office Address Services - NURUD
@endsection

@section('description')
    Virtual Office Address Services - NURUD
@endsection

@section('css')
<style>
    /* Custom styles for the fancy radio button cards */
    .subscription-option {
            border: 1px solid #e5e7eb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .subscription-option:hover,
        .subscription-option.selected {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }

</style>
@endsection


@section('content')
    <section class="hero-bg-virtual text-white relative"
        style="background-image: url('{{ asset('images/virtual-office-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">{{ $plan->name }} Virtual Office Address</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Establish credibility and a professional image with a prime business address without the high costs.
            </p>
        </div>
    </section>

    <section id="introduction" class="mb-16 md:mb-20">
        <div class="container mx-auto px-6 max-w-7xl">
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-12 items-stretch bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Left: Description -->
                <div class="p-8 flex flex-col justify-center">
                    <h2 class="text-3xl font-bold text-blue-800 mb-4">
                        Elevate Your Business with the {{ $plan->name }} Plan
                    </h2>
                    <p class="text-gray-700 leading-relaxed">
                        {!! nl2br($plan->description) !!}
                    </p>
                </div>

                <!-- MODIFIED: Right: Call to Action with Fancy Radio Buttons -->
                <div class="p-8 bg-blue-50 flex flex-col justify-center">
                    <h3 class="text-2xl font-semibold text-blue-800 mb-4">Start with the {{ $plan->name }} Plan</h3>
                    <ul class="text-gray-700 space-y-3 mb-6">
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Instant business credibility</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Prime mailing address included</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Cancel anytime, no hidden fees</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Access to meeting rooms & mail handling
                        </li>
                    </ul>
                    
                    <form action="{{ route('virtual-address.store', ['plan_id' => $plan->id]) }}" method="POST">
                        @csrf
                        @include('front.common.error-and-message')

                        <!-- Subscription Type Selection -->
                        <h3 class="text-2xl font-semibold text-blue-800 mb-4">Choose Your Subscription</h3>
                        <fieldset class="space-y-4 mb-6">
                            <legend class="sr-only">Subscription Options</legend>
                            @foreach($subscriptionTypes::cases() as $type)
                                <label for="{{ $type->value }}_sub" class="subscription-option flex justify-between items-center">
                                    <input type="radio" id="{{ $type->value }}_sub" name="subscription_type"
                                        value="{{ $type->value }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                    <div>
                                        <span class="font-semibold text-lg text-blue-800">{{ ucfirst($type->value) }}
                                            Subscription</span>
                                        <p class="text-sm text-gray-600">
                                            @if($type->value === $subscriptionTypes::MONTHLY->value)
                                                Billed monthly 
                                            @else
                                                Save with annual plan <br/>
                                                <span class="font-bold text-blue-600">({{ $plan->discount_percent.'% discount' }})</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-orange-600">
                                            £{{ $type->value === $subscriptionTypes::MONTHLY->value ? $plan->price.'/month' : $plan->yearly_monthly_price.'/year'}}
                                        </span>
                                         @if($type->value !== $subscriptionTypes::MONTHLY->value)
                                            <p class="text-sm font-bold text-blue-600">
                                                You save £{{ ($plan->price * 12) - $plan->yearly_monthly_price }}
                                            </p>
                                        @endif
                                    </div>
                          
                                </label>
                            @endforeach
                        </fieldset>

                        <!-- Divider -->
                        <hr class="my-6 border-t border-gray-300">

                        @if($plan->mailSettings->isNotEmpty())
                            <!-- Mail Delivery Selection -->
                            <h4 class="text-lg font-semibold text-blue-800 mb-3">Select Mail Delivery Option</h4>
                            <fieldset class="space-y-4 mb-6">
                                <legend class="sr-only">Mail Delivery Options</legend>
                                @foreach($plan->mailSettings as $mailSetting)
                                    <label for="{{ $mailSetting->mail_type->value }}" class="subscription-option flex justify-between items-center">
                                        <input type="radio" id="{{ $mailSetting->mail_type->value }}" required name="mail_type"
                                            value="{{ $mailSetting->mail_type->value }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                        <div>
                                            <span class="font-semibold text-lg text-blue-800">
                                                @if($mailSetting->mail_type->value === $mailTypes::Scanned->value)
                                                    {{ $mailTypes::Scanned->label() }}
                                                @else
                                                    {{ $mailTypes::Forwarded->label() }} 
                                                @endif
                                            </span>
                                            <p class="text-sm text-gray-600">
                                                @if($mailSetting->mail_type->value === $mailTypes::Scanned->value)
                                                    All your mails will be scanned to you
                                                @else
                                                    All your mail will be forwarded to you
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-lg font-semibold text-orange-600">
                                            @if($mailSetting->mail_type->value === $mailTypes::Scanned->value)
                                                {{ currencyFormatter($mailSetting->price) }}/mail
                                            @else
                                                {{ currencyFormatter($mailSetting->price) }}/mail
                                            @endif
                                        </span>
                                      
                                    </label>
                                @endforeach
                            </fieldset>
                        @endif
                        @if($subscription)
                            @if($subscription->plan->id === $plan->id)
                                <span 
                                    class="w-full bg-blue-500 hover:bg-orange-600 text-white text-lg font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                                    You are currently subscribed to this Plan
                                </span>
                            @endif
                            @if( $plan->level > $subscription->plan->level)
                                 <button type="submit"
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white text-lg font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                                    Upgrade To this Plan Now
                                </button>
                            @else
                                @foreach ($allPlans as $dplan)
                                    @if($dplan->level > $subscription->plan->level)
                                        <hr class="my-6 border-t border-gray-300">
                                        <a href="{{ route('virtual-address.show',$dplan->slug) }}" 
                                            class="w-full bg-orange-500 hover:bg-orange-600 text-white text-lg font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                                            Upgrade to {{ $dplan->name }} Plan Now
                                        </a><br/><br/>
                                        Enjoy upto 16% Special Discount
                                        
                                    @endif
                                @endforeach
                            @endif
                        @else
                            <button type="submit"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white text-lg font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                                Add to Cart
                            </button>
                        @endif
                    </form>

                    <p class="text-sm text-gray-600 mt-3 text-center">Secure checkout. </p>
                </div>
            </div>
        </div>
    </section>


    <section id="features" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">{{ $plan->name }} Plan Features
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10 max-w-6xl mx-auto text-center">
                @foreach ($plan->features as $feature)
                    <div class="flex flex-col items-center p-6">
                        <i class="{{ $feature->featureSetting->icon }} text-4xl text-orange-500 mb-4"></i>
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">{{ $feature->featureSetting->name }}</h3>
                        <p class="text-gray-600">{{ $feature->featureSetting->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- The rest of your file remains unchanged... --}}

    <section id="cta-image-bg" class="my-16 md:my-20">
        <div class="relative text-center rounded-lg shadow-xl overflow-hidden"
            style="background-image: url('https://source.unsplash.com/random/1200x400/?business-growth,success,professional'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-blue-800 opacity-75"></div>
            <div class="relative z-10 p-12 md:p-20">
                <h3 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Take Your Business to the Next Level?
                </h3>
                <p class="text-lg text-blue-100 mb-8 max-w-xl mx-auto">
                    The {{ $plan->name }} Plan offers the tools and prestige you need. Get started today and make a
                    lasting
                    impression.
                </p>
                <form action="{{ route('virtual-address.store', ['plan_id' => $plan->id]) }}" method="POST">
                    @csrf
                    <button
                        class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-10 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                        Sign Up for {{ $plan->name }} <i class="fas fa-check-circle ml-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section id="how-to-order" class="py-16 md:py-20 bg-white rounded-lg shadow-lg px-6">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Getting Your {{ $plan->name }} Plan is
            Simple
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto text-center">
            <div class="flex flex-col items-center p-6">
                <div class="step-number bg-orange-500 text-white">1</div>
                <i class="fas fa-mouse-pointer text-5xl text-blue-700 mb-4"></i>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">Select "{{ $plan->name }}"</h3>
                <p class="text-gray-600 text-sm">Choose the {{ $plan->name }} Plan from our virtual office options.</p>
            </div>
            <div class="flex flex-col items-center p-6">
                <div class="step-number bg-orange-500 text-white">2</div>
                <i class="fas fa-file-signature text-5xl text-blue-700 mb-4"></i>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">Complete Sign Up</h3>
                <p class="text-gray-600 text-sm">Fill out our quick online form with your business details.</p>
            </div>
            <div class="flex flex-col items-center p-6">
                <div class="step-number bg-orange-500 text-white">3</div>
                <i class="fas fa-rocket text-5xl text-blue-700 mb-4"></i>
                <h3 class="text-xl font-semibold text-blue-800 mb-2">Activate & Grow</h3>
                <p class="text-gray-600 text-sm">Start using your new professional address and services immediately!</p>
            </div>
        </div>
    </section>




    <section id="why-choose-us" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Why the {{ $plan->name }} Plan is
                Right
                for You</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                    <i class="fas fa-balance-scale-right text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Best Value</h3>
                    <p class="text-gray-600">Get a comprehensive set of features at a competitive price point, offering
                        excellent return on investment.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                    <i class="fas fa-cogs text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Enhanced Mail Services</h3>
                    <p class="text-gray-600">Regular mail forwarding and included scanning save you time and keep you
                        organized, wherever you are.</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                    <i class="fas fa-chart-line text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Professional Boost</h3>
                    <p class="text-gray-600">A prime address and optional local number significantly enhance your business
                        credibility and image.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="cta-simple" class="py-16 text-center">
        <h3 class="text-2xl font-semibold text-blue-800 mb-4">Don't Miss Out on the Pro Advantage!</h3>
        <p class="text-gray-700 mb-6 max-w-lg mx-auto">The {{ $plan->name }} Plan is our most popular for a reason. Give
            your
            business the tools it needs to succeed.</p>
        <form action="{{ route('virtual-address.store', ['plan_id' => $plan->id]) }}" method="POST">
            @csrf
            <button
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md text-lg transform hover:scale-105">
                Add {{ $plan->name }} to Cart <i class="fas fa-shopping-cart ml-2"></i>
            </button>
        </form>
    </section>

    <section id="testimonials" class="py-16 md:py-20 bg-blue-50 rounded-lg px-6">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Hear From Our {{ $plan->name }}
            Clients</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-orange-500">
                <div class="star-rating mb-3">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                        class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic mb-4">"The {{ $plan->name }} plan was a game-changer. The weekly mail
                    forwarding
                    is so convenient, and the address gives us instant credibility."</p>
                <p class="font-semibold text-blue-800">Lola Adebayo</p>
                <p class="text-sm text-gray-500">Owner, Creative Solutions Ltd.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-orange-500">
                <div class="star-rating mb-3">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                        class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text-gray-700 italic mb-4">"Excellent value! The mail scanning saves me so much time, and having
                    access to discounted meeting rooms is a huge plus for client meetings."</p>
                <p class="font-semibold text-blue-800">Mike Eze</p>
                <p class="text-sm text-gray-500">Independent Financial Advisor</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-orange-500">
                <div class="star-rating mb-3">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                        class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic mb-4">"Switching to the {{ $plan->name }} Plan streamlined our operations
                    significantly. The professional address and reliable mail services are top-notch."</p>
                <p class="font-semibold text-blue-800">Sarah Chen</p>
                <p class="text-sm text-gray-500">Director, Innovatech Global</p>
            </div>
        </div>
    </section>

    <section id="testimonials-cta" class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-3xl font-semibold mb-4">Join Our Satisfied Clients!</h3>
            <p class="text-blue-100 mb-8 max-w-lg mx-auto">
                Experience the benefits of a professional business address and reliable mail services. Choose your plan
                today!
            </p>
            <form action="{{ route('virtual-address.store', ['plan_id' => $plan->id]) }}" method="POST">
                @csrf
                <button
                    class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                    Buy Your Virtual Office {{ $plan->name }} Plan Now <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </section>

    <section id="faq" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-4xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>What exactly is a virtual office address?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">A virtual office address provides businesses with a physical
                        mailing address and office-related services without the overhead of a long lease and administrative
                        staff. It allows you to establish a professional presence in a desired location.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>How does mail handling work?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">We receive mail and packages on your behalf at your virtual
                        address. Depending on your plan, we can hold it for pickup, forward it to another address (daily,
                        weekly, or on-demand), or scan the contents and email them to you.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Can I use this address for business registration?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Yes, in most cases, our virtual office address can be used as your
                        official registered business address. However, we recommend checking specific requirements with your
                        local authorities or registration body.</p>
                </div>
                <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Are meeting rooms included?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Access to meeting rooms may be included or offered at a discounted
                        rate depending on your chosen subscription package. Please check the details of each plan.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="purchase-cta" class="py-16 text-center mt-12">
        <div class="bg-orange-500 text-white p-10 md:p-16 rounded-lg shadow-xl">
            <i class="fas fa-rocket text-5xl mb-6"></i>
            <h3 class="text-3xl md:text-4xl font-bold mb-4">Ready to Supercharge Your Business?</h3>
            <p class="text-orange-100 mb-8 max-w-xl mx-auto">
                The {{ $plan->name }} Plan is your key to a more professional, efficient, and flexible business
                operation.
            </p>
            <form action="{{ route('virtual-address.store', ['plan_id' => $plan->id]) }}" method="POST">
                @csrf
                <button
                    class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 px-10 rounded-lg text-xl transition duration-300 shadow-lg transform hover:scale-105">
                    Secure Your {{ $plan->name }} Plan Now <i class="fas fa-lock ml-2"></i>
                </button>
            </form>
        </div>
    </section>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Fancy Radio Button Logic ---
        // This script handles the interactive selection of option cards.
        const subscriptionOptions = document.querySelectorAll('.subscription-option');

        // Set pointer cursor for all option cards
        subscriptionOptions.forEach(optionLabel => {
            optionLabel.style.cursor = 'pointer';
        });

        subscriptionOptions.forEach(optionLabel => {
            optionLabel.addEventListener('click', function() {
                const currentRadio = this.querySelector('input[type="radio"]');
                if (!currentRadio) return; 
                
                const radioName = currentRadio.name;

                // Deselect all other options in the same group.
                document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
                    const parentLabel = radio.closest('.subscription-option');
                    if (parentLabel) {
                        parentLabel.classList.remove('selected');
                        parentLabel.style.borderColor = ''; // Reset border color
                        parentLabel.style.backgroundColor = ''; // Reset background color
                    }
                });

                // Select the clicked option.
                this.classList.add('selected');
                //this.style.borderColor = '#000000'; // Black border
                //this.style.backgroundColor = '#7deda4'; // White background
                currentRadio.checked = true;
            });
        });

        // --- Set Default Selections on Page Load ---
        // This ensures the `checked` options are visually selected initially.
        const radioGroups = new Set();
        document.querySelectorAll('.subscription-option input[type="radio"]').forEach(radio => {
            radioGroups.add(radio.name);
        });

        radioGroups.forEach(groupName => {
            const checkedRadio = document.querySelector(`.subscription-option input[name="${groupName}"]:checked`);
            if (checkedRadio) {
                const parentLabel = checkedRadio.closest('.subscription-option');
                parentLabel.classList.add('selected');
                //parentLabel.style.borderColor = '#000000'; // Black border
                //parentLabel.style.backgroundColor = '#7deda4'; // White background
            }
        });

        // --- FAQ Toggle ---
        // This script handles the expand/collapse functionality of the FAQ items.
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                if (answer && answer.classList.contains('faq-answer')) {
                    // Close other open answers
                    document.querySelectorAll('.faq-answer.open').forEach(openAnswer => {
                        if (openAnswer !== answer) {
                            openAnswer.classList.remove('open');
                            openAnswer.previousElementSibling.querySelector('i').classList.remove('rotate-180');
                        }
                    });

                    answer.classList.toggle('open');
                    if (icon) {
                        icon.classList.toggle('rotate-180');
                    }
                } else {
                    console.error("Could not find the answer element for:", question);
                }
            });
        });
    });
</script>
@endsection