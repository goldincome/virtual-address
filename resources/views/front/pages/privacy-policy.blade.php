@extends('layouts.front')


@section('title')
    Privacy Policy 
@endsection

@section('description')
    This Website collects some Personal Data from its Users. Your Data, Your Trust.
@endsection

@section('css')
    <style>
        /* Hero background */
        .hero-bg-privacy {
            background-image: url('{{ asset('images/privacy-policy.jpg') }}');
            background-size: cover;
            background-position: center 30%;
        }
    </style>
@endsection

@section('content')
    <section class="hero-bg-privacy text-white relative">
        <div class="absolute inset-0 bg-blue-900 opacity-70"></div>
        <div class="container mx-auto px-6 py-24 md:py-32 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight">Privacy Policy</h1>
            <p class="text-lg md:text-xl mb-6 text-blue-100 max-w-3xl mx-auto">
                This Website collects some Personal Data from its Users. Your Data, Your Trust.
            </p>
        </div>
    </section>

    <main class="container mx-auto px-6 py-16 md:py-20">

        <section id="data-processing-summary" class="mb-16 md:mb-20">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-blue-800 mb-4">Personal Data We Collect</h2>
                <p class="text-gray-700 leading-relaxed mb-8">
                    Personal Data is processed for the following purposes and using the following services. Below is a detailed breakdown of the services we use and the data they collect.
                </p>

                <div class="space-y-12">

                    <div>
                        <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-bullhorn mr-3"></i> Advertising</h3>
                        <div class="space-y-4 pl-8 border-l-2 border-orange-200">
                            <div>
                                <h4 class="font-bold text-blue-700">Microsoft Advertising, Facebook Lookalike Audience and LinkedIn Ads</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-700">Meta ads conversion tracking (Meta pixel) and Hotjar Form Analysis & Conversion Funnels</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Trackers; Usage Data</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-700">Facebook Audience Network</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; unique device identifiers for advertising (Google Advertiser ID or IDFA, for example); Usage Data</p>
                            </div>
                             <div>
                                <h4 class="font-bold text-blue-700">LinkedIn conversion tracking (LinkedIn Insight Tag)</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> device information; Trackers; Usage Data</p>
                            </div>
                             <div>
                                <h4 class="font-bold text-blue-700">Microsoft Advertising Universal Event Tracking</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Trackers; unique device identifiers for advertising (Google Advertiser ID or IDFA, for example); Universally unique identifier (UUID); Usage Data</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-chart-bar mr-3"></i> Analytics</h3>
                        <div class="space-y-4 pl-8 border-l-2 border-orange-200">
                            <div>
                                <h4 class="font-bold text-blue-700">Google Analytics (Universal Analytics) and MixPanel</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-envelope mr-3"></i> Contacting the User</h3>
                        <div class="space-y-4 pl-8 border-l-2 border-orange-200">
                            <div>
                                <h4 class="font-bold text-blue-700">Contact form</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> address; city; company name; country; county; date of birth; email address; fax number; first name; last name; number of employees; phone number; profession; various types of Data; VAT Number; website; ZIP/Postal code</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-2xl font-semibold text-orange-600 mb-4 flex items-center"><i class="fas fa-external-link-alt mr-3"></i> Displaying content from external platforms</h3>
                        <div class="space-y-4 pl-8 border-l-2 border-orange-200">
                            <div>
                                <h4 class="font-bold text-blue-700">Google Maps widget</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                         <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Handling productivity</h3>
                            <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Google Workspace</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> address; city; company name; country; Data communicated while using the service; date of birth; device information; email address; first name; gender; geographic position; last name; password; phone number; profession; profile picture; screenshots; Tracker; Usage Data; username; VAT Number; workplace</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Heat mapping</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Hotjar Heat Maps & Recordings</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data; various types of Data as specified in the privacy policy of the service</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Infrastructure monitoring</h3>
                            <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Sentry</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> various types of Data as specified in the privacy policy of the service</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Social interaction</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">LinkedIn button and social widgets</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Live chat</h3>
                            <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Tawk.to Widget</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Data communicated while using the service; Tracker; Usage Data</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Contact management</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Mailgun</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> address; country; date of birth; email address; first name; gender; last name; phone number; profession; Tracker; Usage Data; various types of Data</p>
                            </div>
                        </div>
                         <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Data collection</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Facebook lead ads</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> address; city; company name; country; Data communicated while using the service; date of birth; email address; first name; gender; last name; marital status; phone number; profession; state; Tracker; Usage Data; ZIP/Postal code</p>
                            </div>
                        </div>
                         <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Registration</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Google OAuth</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> various types of Data as specified in the privacy policy of the service</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Remarketing</h3>
                            <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Facebook Custom Audience</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> email address; Tracker</p>
                                <h4 class="font-bold text-blue-700 mt-2">Facebook Remarketing and LinkedIn Website Retargeting</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Tracker; Usage Data</p>
                            </div>
                        </div>
                         <div>
                            <h3 class="text-xl font-semibold text-orange-600 mb-3">Tag Management</h3>
                             <div class="space-y-3">
                                <h4 class="font-bold text-blue-700">Google Tag Manager</h4>
                                <p class="text-gray-600 text-sm"><strong>Personal Data:</strong> Usage Data</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="opt-out-info" class="py-16 md:py-20 bg-blue-50 rounded-lg px-6">
             <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-center text-blue-800 mb-4">Opting Out of Interest-Based Advertising</h2>
                <p class="text-gray-700 leading-relaxed">
                    In addition to any opt-out feature provided by any of the services listed in this document, Users may learn more on how to generally opt out of interest-based advertising within the dedicated section of the Cookie Policy.
                </p>
            </div>
        </section>

        <section id="contact-info" class="py-16 md:py-20">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-blue-800 mb-4">Contact Information</h2>
                <div class="space-y-2 text-gray-700">
                    <div>
                        <h4 class="text-xl font-semibold text-orange-600">Owner and Data Controller</h4>
                        <p>Charlton Virtual Office - Unit 6, Block 3 Woolwich, Dockyard Industrial Estate, Woolwich Church St, Charlton, London SE18 5PQ</p>
                    </div>
                    <div>
                         <h4 class="text-xl font-semibold text-orange-600 mt-4">Owner contact email</h4>
                         <a href="mailto:enquiries@Charlton Virtual Office.com" class="text-blue-600 hover:underline">enquiries@Charlton Virtual Office.com</a>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection