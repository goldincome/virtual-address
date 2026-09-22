<footer class="bg-blue-900 text-blue-200 py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-left mb-8">
            <div>
                <h5 class="text-lg font-semibold mb-4 text-white">Our Company</h5>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about-us.index') }}">About Us</a></li>
                    <li><a href="{{ route('contact-us.index') }}">Contact Us</a></li>
                    <li><a href="{{ route('terms-of-service.index') }}">Terms of Service</a></li>
                    <li><a href="{{ route('privacy-policy.index') }}">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4 text-white">Our Services</h5>
                 <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('virtual-address.index') }}">Virtual Office Address</a></li>
                    <li><a href="{{ route('virtual-address.registered-office') }}">Registered Office Address</a></li>
                    <li><a href="{{ route('virtual-address.mail-forwarding') }}">Mail Forwarding &amp; Scanning</a></li>
                    <li><a href="{{ route('virtual-address.directors-service') }}">Directors' Service Address</a></li>
                    <li><a href="{{ route('meeting-rooms.index') }}">Meeting Rooms</a></li>
                    <li><a href="{{ route('conference-rooms.index') }}">Conference Rooms</a></li>
                    <li><a href="{{ route('about-us.index') }}#faq">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4 text-white">Resources &amp; Guides</h5>
                 <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('local.woolwich') }}">Virtual Office in Woolwich, SE18</a></li>
                    <li><a href="{{ route('guides.registered-rules') }}">Can I Use a Virtual Office as a Registered Address?</a></li>
                    <li><a href="{{ route('guides.company-registration') }}">Register a Limited Company with a Virtual Address</a></li>
                    <li><a href="{{ route('guides.comparison') }}">Virtual Office vs Serviced Office vs Coworking</a></li>
                    <li><a href="{{ route('guides.best-areas') }}">Best Areas in SE London to Register a Business</a></li>
                </ul>
            </div>
            <div>
                 <h5 class="text-lg font-semibold mb-4 text-white">Contact Us</h5>
                 <address class="not-italic text-sm space-y-2 mb-4">
                    <p><i class="fas fa-map-marker-alt mr-2 text-orange-400"></i>Unit 6,Block 3, Dockyard Industrial Estate,<br>Church Street, Woolwich, London UK</p>
                    <p><i class="fas fa-phone-alt mr-2 text-orange-400"></i><a href="tel:+442032474747">+44 (0) 2032474747</a></p>
                    <p><i class="fas fa-envelope mr-2 text-orange-400"></i><a href="mailto:support@charltonvirtualoffice.com">support@charltonvirtualoffice.com</a></p>
                 </address>
                 <div class="flex space-x-4">
                    <a href="#" aria-label="LinkedIn" class="social-icon text-xl hover:text-orange-300 transition duration-300"><i class="fab fa-linkedin"></i></a>
                    <a href="#" aria-label="Twitter" class="social-icon text-xl hover:text-orange-300 transition duration-300"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Facebook" class="social-icon text-xl hover:text-orange-300 transition duration-300"><i class="fab fa-facebook-f"></i></a>
                 </div>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-blue-700 text-center text-sm">
             &copy; 2025 Charlton Virtual Office. All Rights Reserved. |
             <a href="{{ route('privacy-policy.index') }}">Privacy Policy</a> |
             <a href="{{ route('terms-of-service.index') }}">Terms of Service</a>
        </div>
    </div>
</footer>
