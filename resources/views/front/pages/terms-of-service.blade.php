@extends('layouts.front')

@section('title')
    Terms of Service - NURUD
@endsection

@section('description')
    Terms of Service for Virtually There Offices (NURUD)
@endsection

@section('css')
    <style>
        /* Hero background */
        .hero-bg-terms {
            background-image: url('{{ asset('images/terms-of-service.jpg') }}'); /* Assumed image */
            background-size: cover;
            background-position: center 30%;
        }
    </style>
@endsection

@section('content')
    <section class="hero-bg-terms text-white relative">
        <div class="absolute inset-0 bg-blue-900 opacity-70"></div>
        <div class="container mx-auto px-6 py-24 md:py-32 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight">Terms of Service</h1>
            <p class="text-lg md:text-xl mb-6 text-blue-100 max-w-3xl mx-auto">
                These terms and conditions govern the Agreement for your use of our Virtual Office and Support Services.
            </p>
        </div>
    </section>

    <main class="container mx-auto px-6 py-16 md:py-20">

        <section id="introduction" class="mb-16 md:mb-20">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-blue-800 mb-6 text-center">VIRTUALLY THERE OFFICES TERMS OF SERVICE</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center mb-8">
                    <div class="p-4 bg-blue-50 rounded-lg shadow-sm">
                        <p class="font-semibold text-blue-700">No long minimum term</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg shadow-sm">
                        <p class="font-semibold text-blue-700">No set up fees or deposit</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg shadow-sm">
                        <p class="font-semibold text-blue-700">No cancellation fees</p>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed">
                    The terms and conditions detailed govern the Agreement for your use of Virtual Office and Support Services between the Company (VT), Virtually There Offices Limited, Henleaze House, 13 Harbury Road, Henleaze, Bristol, BS9 4PN as the provider of agreed Services, and the User. The User agrees that the Services will be used only in accordance with these terms and conditions.
                </p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto space-y-12">
            <section id="general-services">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-cogs mr-3"></i> 1. General Services</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>1.1</strong> The User has entered into this Agreement for the provision of Services and Additional Services by VT as detailed by their online application and confirmation email.</p>
                    <p><strong>1.2</strong> No variation to these Conditions shall be binding unless agreed in writing.</p>
                    <p><strong>1.3</strong> Any typographical, clerical or other error or omission in any sales literature, quotation, price list, acceptance of the offer, invoice or other document or information issued by VT shall be subject to correction without any liability on the part of VT.</p>
                    <p><strong>1.4</strong> This Agreement is for the initial fixed term period and shall be extended automatically on a monthly or annual basis thereafter, depending on the frequency chosen when signing up.</p>
                    <p><strong>1.5</strong> No notice of renewal will be given so it is the User’s responsibility to cancel within the terms below if they do not wish to renew the services.</p>
                    <p><strong>1.6</strong> Any payments taken are non-refundable unless they comply with our termination terms.</p>
                    <p><strong>1.7</strong> We reserve the right to claim interest, compensation and reasonable costs under the Late Payment of Commercial Debts (Interest) Act 1998. If for any reason the Act does not apply, interest shall be payable on overdue amounts at 8% over the Bank of England Base Rate.</p>
                    <p><strong>1.8</strong> A card validation fee may be required when the User agrees to a service with an initial free period before service payment is requested.</p>
                </div>
            </section>

            <section id="call-answering">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-phone-alt mr-3"></i> 2. Call Answering Services</h3>
                <div class="pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p>Call Answering Service Terms and Conditions were last updated on 13 December 2023.</p>
                </div>
            </section>

            <section id="virtual-office">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-building mr-3"></i> 3. Virtual Office Services</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>3.1</strong> Virtual office services include letter post management only. Mail is stored securely for 3 months, after which it will be shredded. Post forwarding after cancellation is available for up to 3 months at an additional cost. Mail can be collected from Henleaze House, Bristol, BS9 4PN.</p>
                    <p><strong>3.2</strong> Parcel deliveries are only accepted at the Henleaze House address for relevant subscribers and must be collected within 48 hours. Parcels held longer will be charged at £5.00 per week plus VAT. A penalty fee of £100 applies to parcels delivered for services that exclude them. VT is not liable for package security or condition.</p>
                    <p><strong>3.3</strong> We cannot accept deliveries that require a signature as not all virtual office locations are manned.</p>
                    <p><strong>3.4</strong> The User may use the designated Centre address as its business address.</p>
                    <p><strong>3.5</strong> Virtual Offices are for a single business name only. Additional business names require separate contracts.</p>
                    <p><strong>3.6</strong> The virtual office address cannot be used as a registered office address unless agreed as an additional service. VT reserves the right to charge an annual fee of £60 plus VAT for this service and a £250 plus VAT fee if removal from Companies House is required.</p>
                    <p><strong>3.7</strong> The registered office address service [selected locations only] has an annual charge of £75 plus VAT. The User is responsible for complying with all HMRC statutory requirements and providing correct information.</p>
                    <p><strong>3.8</strong> Post-scanning/forwarding plans are subject to a fair use policy of 30 letters per month. Exceeding this limit may result in additional reasonable usage charges.</p>
                    <p><strong>3.9</strong> Discounts for additional virtual offices apply only to those additional offices, not the original subscription.</p>
                    <p><strong>3.10</strong> Post-scanning users can request mail forwarding at an additional cost, which includes a handling fee and the Royal Mail delivery fee.</p>
                    <p><strong>3.11</strong> Monthly and annual charges are subject to change.</p>
                </div>
            </section>

            <section id="financial">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-credit-card mr-3"></i> 4. Financial</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>4.1</strong> VT reserves the right to increase service prices with one month’s notice in writing or by email.</p>
                    <p><strong>4.2</strong> All prices are exclusive of VAT.</p>
                    <p><strong>4.3</strong> All fees are to be paid via our online payment system (Chargebee/Stripe). You will be required to enter payment details during setup.</p>
                    <p><strong>4.4</strong> The service will not be activated until payment criteria are met. Invoicing occurs on the monthly anniversary for the subsequent month. For free trials, billing commences at the end of the trial period.</p>
                    <p><strong>4.5</strong> VT will send all invoices electronically.</p>
                </div>
            </section>

            <section id="conditions-of-use">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-gavel mr-3"></i> 5. Conditions of Use</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>5.1</strong> The User shall not use VT services for any illegal, immoral, or reputation-damaging purpose.</p>
                    <p><strong>5.2</strong> The User shall provide personal identification (passport or driving licence) and proof of home address.</p>
                    <p><strong>5.3</strong> Breach of 5.1 shall entitle VT to terminate this Agreement immediately.</p>
                    <p><strong>5.4</strong> VT is required by law to register user names and addresses with HMRC.</p>
                    <p><strong>5.5</strong> A non-solicitation clause applies during the Agreement and for six months after, with a penalty equivalent to one year's salary for any breach.</p>
                    <p><strong>5.6</strong> The User must not carry on a business that competes with VT.</p>
                    <p><strong>5.7</strong> Per Google My Business guidelines, virtual offices are not service-area businesses and require staffing during business hours.</p>
                    <p><strong>5.8</strong> Multiple company/trading names cannot be applied to a single subscription.</p>
                </div>
            </section>
            
            <section id="aml">
                 <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-shield-alt mr-3"></i> 6. Anti-Money Laundering</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>6.1</strong> The User is required to complete Anti-Money Laundering checks before the full address can be used.</p>
                    <p><strong>6.2</strong> Subscription fees will be charged once the on-boarding process starts, regardless of the completion of AML checks.</p>
                    <p><strong>6.3</strong> If the User fails AML checks or fails to complete them, VT reserves the right to retain one month’s subscription cost.</p>
                </div>
            </section>

            <section id="force-majeure">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-exclamation-triangle mr-3"></i> 7. Force Majeure</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                     <p><strong>7.1</strong> VT is not liable for any delay or failure to perform obligations due to causes beyond its reasonable control.</p>
                     <p><strong>7.2</strong> The User agrees to waive claims for damages (direct, indirect, consequential, etc.) arising from any service failure or interruption.</p>
                </div>
            </section>
            
            <section id="termination">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-times-circle mr-3"></i> 8. Termination</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>8.1</strong> Termination requires formal written notice before the subscription anniversary date for termination at the end of the next full month.</p>
                    <p class="ml-4"><strong>8.1.a.</strong> Annual subscriptions must be cancelled at least one full month before the anniversary date to avoid renewal for the following year.</p>
                    <p><strong>8.2</strong> VT may terminate the Agreement with not less than one full month's written notice.</p>
                    <p><strong>8.3</strong> Failure to make punctual payments or observe obligations are fundamental breaches and will cancel the Agreement immediately.</p>
                    <p><strong>8.4</strong> Upon termination, the User must immediately pay any arrears due to VT.</p>
                    <p><strong>8.5</strong> Any sums owed by VT to the User upon termination will be returned, subject to administrative cost deductions.</p>
                    <p><strong>8.6</strong> Notices must be in writing and sent to the specified postal or email addresses.</p>
                    <p><strong>8.7</strong> Upon termination, the User must remove the VT address from all platforms and media.</p>
                    <p><strong>8.8</strong> A two-week grace period for post-processing is provided after cancellation. Afterward, mail will be returned to the sender.</p>
                    <p><strong>8.9</strong> Access to historical data in the portal ends after the grace period.</p>
                </div>
            </section>
            
            <section id="complaints">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-headset mr-3"></i> 9. Complaints</h3>
                <div class="pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p>We are committed to providing a high-quality service. For a copy of our complaints procedure, please contact us at +44 (0) 2032474747 or <a href="mailto:team@nurud.com" class="text-blue-600 hover:underline">team@nurud.com</a>.</p>
                </div>
            </section>

            <section id="law">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-balance-scale mr-3"></i> 10. Law</h3>
                <div class="pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p>This Agreement shall be subject to and construed in accordance with English law.</p>
                </div>
            </section>

            <section id="gdpr">
                <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-user-shield mr-3"></i> 11. General Data Protection Regulations</h3>
                <div class="space-y-4 pl-8 border-l-2 border-orange-200 text-gray-700 leading-relaxed">
                    <p><strong>11.1</strong> By entering this agreement, you provide us with personal information required to deliver our services.</p>
                    <p><strong>11.2</strong> Information required includes names, addresses, contact details, identification, and bank/company details.</p>
                    <p><strong>11.3</strong> We will hold your personal information securely, either in hard copy or digitally.</p>
                    <p><strong>11.4</strong> Information is only shared when required and in accordance with your wishes.</p>
                    <p><strong>11.5</strong> Your information will not be passed to unlisted third parties without your consent.</p>
                    <p><strong>11.6</strong> We hold identification and company information securely to protect our position and prevent money laundering. This is never passed to third parties.</p>
                    <p><strong>11.7</strong> Your details may be added to our mailing list, from which you can unsubscribe at any time.</p>
                    <p><strong>11.8</strong> We retain personal information for up to 6 years for legal reasons.</p>
                    <p><strong>11.9</strong> For data issues, contact us at 0203 476 7792 or <a href="mailto:team@nurud.com" class="text-blue-600 hover:underline">team@nurud.com</a>.</p>
                    <p><strong>11.10</strong> You have rights including SAR, rectification, erasure, and others. More information is available at www.ico.org.uk.</p>
                    <p><strong>11.11</strong> We retain data for up to 6 years, as this is the time limit for initiating civil action against us.</p>
                </div>
            </section>
        </div>
        
        <div class="mt-20 pt-8 border-t text-center text-sm text-gray-500">
            <p>NURUD</p>
            <p>Registered office: Unit 6, Block 3 Woolwich, Dockyard Industrial Estate, Woolwich Church St, Charlton, London SE18 5PQ</p>
            <p>More information on how we hold and process your data is available at our website: nurud.com</p>
        </div>

    </main>
@endsection