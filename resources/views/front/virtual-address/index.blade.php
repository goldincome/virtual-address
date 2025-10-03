@extends('layouts.front')


@section('title')
    Professional Virtual Address & Mail Forwarding 
@endsection

@section('description')
    Instant Virtual Address and reliable Mail Forwarding across SE London, including Woolwich Arsenal, Abbey Wood, and Thamesmead. Affordable and secure.
@endsection

@section('keywords', "Virtual Address, Mail Forwarding, Registered Office, Woolwich Arsenal, Charlton, Greenwich, South East London, Business Address, Mail Handling, UK Business Address")



@section('content')
    
    <section class="hero-bg-virtual text-white relative" style="background-image: url('{{ asset('images/virtual-office-address.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto px-6 py-24 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Prestigious Virtual Office Address</h1>
            <p class="text-lg md:text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Establish credibility and a professional image with a prime business address without the high costs.
            </p>
        </div>
    </section>

    <section id="virtual-address-description" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6 max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                 <h2 class="text-3xl md:text-4xl font-bold text-blue-800 mb-6">What is a Virtual Office Address?</h2>
                 <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    A Virtual Office Address provides your business with a prestigious physical mailing address and essential office-related services, such as mail handling, without the need to rent expensive traditional office space. It's the perfect solution for establishing a professional presence in a prime location while maintaining flexibility and controlling costs.
                </p>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    Whether you're a startup looking to build credibility, a freelancer needing a professional front, a remote team requiring a central hub for mail, or an international company entering a new market, a virtual address offers significant advantages. You can use the address on your website, business cards, marketing materials, and even for business registration (subject to local regulations).
                </p>
                 <p class="text-lg text-gray-700 leading-relaxed">
                    Beyond just an address, our virtual office packages often include mail forwarding, scanning services, and access to meeting rooms, providing a comprehensive solution to support your business operations efficiently and affordably.
                </p>
            </div>
            <div>
                 <img src="images/virtual-address.jpg"
                     alt="Professional office setting"
                     class="w-full h-auto rounded-lg shadow-lg"
                     onerror="this.onerror=null; this.src='https://placehold.co/600x450/cccccc/ffffff?text=Image+Not+Found';">
            </div>
        </div>
    </section>

    <section id="packages" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Choose Your Plan</h2>
            <div class="flex flex-wrap justify-center gap-8 max-w-6xl mx-auto">
                @foreach($plans as $index => $plan)
                    @if($index == 1)
                        <div class="bg-white p-8 rounded-lg shadow-lg border flex flex-col popular-package">
                            <div class="popular-badge">Popular</div>
                            <h3 class="text-2xl font-semibold text-orange-600 mb-4 text-center">{{ $plan->name }}</h3>
                            <p class="text-4xl font-bold text-center text-blue-900 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                            <ul class="space-y-3 text-gray-700 mb-8 flex-grow">
                                @foreach($plan->features as $index => $feature)
                                    <li class="flex items-center"><i class="{{ $feature->featureSetting->status ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                        {{ $feature->featureSetting->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('virtual-address.show',$plan->slug) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow-md">
                                Select {{ $plan->name }}
                            </a>
                        </div>
                    @else
                        <div class="bg-gray-50 p-8 rounded-lg shadow-md border border-gray-200 flex flex-col">
                            <h3 class="text-2xl font-semibold text-blue-700 mb-4 text-center">{{ $plan->name }}</h3>
                            <p class="text-4xl font-bold text-center text-blue-900 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                            <ul class="space-y-3 text-gray-600 mb-8 flex-grow">
                                @foreach($plan->features as $index => $feature)
                                    <li class="flex items-center"><i class="{{ $feature->is_activated ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                        {{ $feature->featureSetting->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('virtual-address.show',$plan->slug) }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                               Select {{ $plan->name }}
                            </a>
                        </div>
                      @endif
                @endforeach  
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">How It Works in 3 Simple Steps</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-5xl mx-auto text-center">
                 <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">1</div>
                     <i class="fas fa-mouse-pointer text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Choose Your Plan</h3>
                    <p class="text-gray-600">Select the virtual office package that best suits your business needs and budget.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">2</div>
                     <i class="fas fa-cogs text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Set Up Your Address</h3>
                    <p class="text-gray-600">Complete the simple sign-up process and receive your new prestigious business address instantly.</p>
                </div>
                <div class="flex flex-col items-center p-6">
                     <div class="step-number bg-orange-500 text-white">3</div>
                     <i class="fas fa-envelope-open-text text-5xl text-blue-700 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-800 mb-2">Receive & Manage Mail</h3>
                    <p class="text-gray-600">We receive your mail and handle it according to your instructions (forwarding, scanning, pickup).</p>
                </div>
            </div>
        </div>
    </section>

    <section id="our-plans" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Our Plans</h2>
            <div class="flex flex-wrap justify-center gap-8 max-w-6xl mx-auto">
                @foreach($plans as $index => $plan)
                    @if($index == 1)
                        <div class="bg-white rounded-lg shadow-lg border flex flex-col popular-package overflow-hidden">
                            <div class="popular-badge">Popular</div>
                            <img src="{{ $plan->primary_image }}"
                                alt="Business Pro Plan Preview"
                                class="w-full h-48 object-cover cursor-pointer plan-image"
                                data-gallery="{{ $plan->id }}">
                            <div class="p-8 flex flex-col flex-grow">
                                <h3 class="text-2xl font-semibold text-orange-600 mb-4 text-center">{{ $plan->name }}</h3>
                                <p class="text-4xl font-bold text-center text-blue-900 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                                <ul class="space-y-3 text-gray-700 mb-8 flex-grow">
                                    @foreach($plan->features as $index => $feature)
                                        <li class="flex items-center"><i class="{{ $feature->is_activated ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                            {{ $feature->featureSetting->name }}
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('virtual-address.show',$plan->slug) }}" class="mt-auto block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow-md">
                                    Select {{ $plan->name }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg shadow-md border border-gray-200 flex flex-col overflow-hidden">
                            <img src="{{ $plan->primary_image }}"
                                alt="Basic Plan Preview"
                                class="w-full h-48 object-cover cursor-pointer plan-image"
                                data-gallery="{{ $plan->id }}">
                            <div class="p-8 flex flex-col flex-grow">
                                <h3 class="text-2xl font-semibold text-blue-700 mb-4 text-center">{{ $plan->name }}</h3>
                                <p class="text-4xl font-bold text-center text-blue-900 mb-6">{{ currencyFormatter($plan->price) }}<span class="text-lg font-normal text-gray-500">/month</span></p>
                                <ul class="space-y-3 text-gray-600 mb-8 flex-grow">
                                    @foreach($plan->features as $index => $feature)
                                        <li class="flex items-center"><i class="{{ $feature->is_activated ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-400' }} mr-2"></i>
                                            {{ $feature->featureSetting->name }}
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('virtual-address.show',$plan->slug) }}" class="mt-auto block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow">
                                    Select {{ $plan->name }}
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section id="why-choose-us" class="py-16 md:py-24 bg-blue-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">Why Choose Our Virtual Address?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-landmark text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Prime Location</h3>
                    <p class="text-gray-600">Impress clients with a prestigious business address in a sought-after area.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-dollar-sign text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Cost Effective</h3>
                    <p class="text-gray-600">Get the benefits of a physical office address at a fraction of the cost.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-user-tie text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Professional Image</h3>
                    <p class="text-gray-600">Enhance your brand's credibility and professionalism instantly.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-envelope-open-text text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Reliable Mail Handling</h3>
                    <p class="text-gray-600">Secure and efficient mail receiving, forwarding, and scanning services.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-sync-alt text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Flexibility & Scalability</h3>
                    <p class="text-gray-600">Choose the plan that fits your needs and scale as your business grows.</p>
                </div>
                 <div class="text-center p-6 bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                     <i class="fas fa-headset text-4xl text-orange-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-blue-700 mb-2">Dedicated Support</h3>
                    <p class="text-gray-600">Our friendly team is here to assist you with any queries.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-800 mb-12">What Our Customers Are Saying</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                    <div class="star-rating mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-700 italic mb-4">"The virtual address service gave my startup the professional image it needed without the high cost. Mail forwarding is always prompt!"</p>
                    <p class="font-semibold text-blue-800">Aisha Bello</p>
                    <p class="text-sm text-gray-500">Founder, TechStart NG</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                     <div class="star-rating mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i> </div>
                    <p class="text-gray-700 italic mb-4">"Using YourBrand's virtual office has been seamless. The mail scanning service is incredibly convenient for managing business correspondence remotely."</p>
                    <p class="font-semibold text-blue-800">Chinedu Okonkwo</p>
                    <p class="text-sm text-gray-500">Freelance Consultant</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg shadow border border-blue-100">
                     <div class="star-rating mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-700 italic mb-4">"The Business Pro plan is perfect for my growing e-commerce store. Having a prestigious address and reliable mail handling makes a huge difference."</p>
                    <p class="font-semibold text-blue-800">Fatima Ibrahim</p>
                    <p class="text-sm text-gray-500">CEO, KwikShop</p>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials-cta" class="py-16 bg-blue-600 text-white">
         <div class="container mx-auto px-6 text-center">
            <h3 class="text-3xl font-semibold mb-4">Join Our Satisfied Clients!</h3>
            <p class="text-blue-100 mb-8 max-w-lg mx-auto">
                Experience the benefits of a professional business address and reliable mail services. Choose your plan today!
            </p>
            <a href="#packages" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg transform hover:scale-105">
                Select Your Virtual Office Plan <i class="fas fa-arrow-right ml-2"></i>
            </a>
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
                    <p class="text-gray-600 faq-answer">A virtual office address provides businesses with a physical mailing address and office-related services without the overhead of a long lease and administrative staff. It allows you to establish a professional presence in a desired location.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>How does mail handling work?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">We receive mail and packages on your behalf at your virtual address. Depending on your plan, we can hold it for pickup, forward it to another address (daily, weekly, or on-demand), or scan the contents and email them to you.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Can I use this address for business registration?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Yes, in most cases, our virtual office address can be used as your official registered business address. However, we recommend checking specific requirements with your local authorities or registration body.</p>
                </div>
                 <div class="faq-item p-5 rounded-lg bg-gray-50 shadow">
                    <h4 class="faq-question text-lg font-semibold text-blue-700 mb-2 flex justify-between items-center">
                        <span>Are meeting rooms included?</span>
                        <i class="fas fa-chevron-down text-orange-500 transition-transform duration-300"></i>
                    </h4>
                    <p class="text-gray-600 faq-answer">Access to meeting rooms may be included or offered at a discounted rate depending on your chosen subscription package. Please check the details of each plan.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-16 bg-blue-700 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Establish Your Professional Presence?</h2>
            <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">Choose the virtual office plan that suits you best and get started today!</p>
            <a href="#packages" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 shadow-lg">
                View Plans & Sign Up <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>


    <div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div id="modalOverlay" class="absolute inset-0 bg-black bg-opacity-75 modal-overlay"></div>
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-xl relative max-w-3xl w-full modal-content transform scale-95">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl leading-none z-10">
                &times;
            </button>
            <h3 id="galleryTitle" class="text-xl font-semibold text-blue-800 mb-4 text-center">Plan Gallery</h3>
            <div id="galleryContainer" class="gallery-scroll overflow-x-auto flex space-x-4 p-4 bg-gray-100 rounded h-72 items-center">
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
       console.log("Virtual Office Address page loaded (interactive FAQ & Gallery enabled).");

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

        // --- Image Gallery Modal ---
        const modal = document.getElementById('imageModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModalBtn = document.getElementById('closeModal');
        const galleryContainer = document.getElementById('galleryContainer');
        const galleryTitle = document.getElementById('galleryTitle');
        const planImages = document.querySelectorAll('.plan-image');

        const galleries = {
            @foreach($plans as $plan)
                {{ $plan->id }}: {
                    title: "{{ $plan->name }} Gallery",
                    images: [
                        '{{ $plan->primary_image }}',
                        @foreach($plan->getMedia($plan::ADDITIONAL_IMAGES) as $media)
                            '{{ $media->getUrl() }}',
                        @endforeach
                    ]
                },
            @endforeach
        };

        function openModal(galleryId) {
            const galleryData = galleries[galleryId];
            if (!galleryData) {
                console.error("Gallery data not found for:", galleryId);
                return;
            }
            galleryContainer.innerHTML = '';
            galleryTitle.textContent = galleryData.title || "Plan Gallery";
            galleryData.images.forEach(src => {
                const img = document.createElement('img');
                img.src = src;
                img.alt = `${galleryData.title} Image`;
                img.className = 'h-64 object-contain rounded-md flex-shrink-0';
                galleryContainer.appendChild(img);
            });
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.querySelector('.modal-overlay').classList.add('opacity-100');
            modal.querySelector('.modal-content').classList.remove('scale-95');
            modal.querySelector('.modal-content').classList.add('scale-100');
        }

        function closeModal() {
             modal.querySelector('.modal-overlay').classList.remove('opacity-100');
             modal.querySelector('.modal-content').classList.remove('scale-100');
             modal.querySelector('.modal-content').classList.add('scale-95');
             setTimeout(() => {
                 modal.classList.add('hidden');
             }, 300);
        }

        planImages.forEach(image => {
            image.addEventListener('click', () => {
                const galleryId = image.getAttribute('data-gallery');
                openModal(galleryId);
            });
        });

        closeModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', closeModal);

        modal.querySelector('.modal-content').addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

    </script>

@endsection
