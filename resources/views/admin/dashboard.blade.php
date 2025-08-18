@extends('layouts.admin')

@section('content')
    <main id="dashboardContent" class="content-area flex-grow p-6 md:p-10 bg-gray-50 overflow-y-auto md:ml-5">
        <div id="dashboard-view">
            <h1 class="text-3xl font-bold text-blue-800 mb-6">Welcome to your Dashboard, John!</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-blue-700">Active Services</h3>
                        <i class="fas fa-briefcase text-3xl text-orange-400"></i>
                    </div>
                    <p class="text-4xl font-bold text-blue-800">2</p>
                    <p class="text-sm text-gray-500 mt-1">Virtual Address, Meeting Room Credits</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-blue-700">Recent Orders</h3>
                        <i class="fas fa-receipt text-3xl text-green-500"></i>
                    </div>
                    <p class="text-4xl font-bold text-blue-800">5</p>
                    <a href="#orders"
                        onclick="loadContent('orders'); setActiveLink(document.querySelector('a[href=\\'#orders\\']')); return false;"
                        class="text-sm text-orange-600 hover:underline mt-1">View all orders</a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-blue-700">Unread Mail</h3>
                        <i class="fas fa-envelope text-3xl text-red-500"></i>
                    </div>
                    <p class="text-4xl font-bold text-blue-800">3</p>
                    <a href="#mail-services"
                        onclick="loadContent('mail-services'); setActiveLink(document.querySelector('a[href=\\'#mail-services\\']')); return false;"
                        class="text-sm text-orange-600 hover:underline mt-1">Manage mail</a>
                </div>
            </div>

            <div class="mt-10 bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-xl font-semibold text-blue-700 mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="new-booking.html"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                        <i class="fas fa-plus-circle mr-2"></i> Book a Room
                    </a>
                    <a href="#profile"
                        onclick="loadContent('profile'); setActiveLink(document.querySelector('a[href=\\'#profile\\']')); return false;"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 shadow">
                        <i class="fas fa-user-cog mr-2"></i> Update Profile
                    </a>
                </div>
            </div>
        </div>

        <div id="orders-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-8">My Orders</h1>
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Service</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#VM12345</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-05-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Business Pro Plan</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$99.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-orange-600 hover:text-orange-900">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#VM12300</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-04-15</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Meeting Room (2 hours)</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$50.00</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-orange-600 hover:text-orange-900">View</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="profile-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-8">My Profile</h1>
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md border border-gray-200">
                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="profile-first-name" class="block text-sm font-medium text-gray-700">First
                                Name</label>
                            <input type="text" name="profile_first_name" id="profile-first-name" value="John"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="profile-last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="profile_last_name" id="profile-last-name" value="Doe"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="profile-email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="profile_email" id="profile-email" value="john.doe@example.com"
                            readonly
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Email cannot be changed here. Contact support for assistance.
                        </p>
                    </div>
                    <div>
                        <label for="profile-phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="profile_phone" id="profile-phone" value="+15551234567"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="profile-company" class="block text-sm font-medium text-gray-700">Company Name
                            (Optional)</label>
                        <input type="text" name="profile_company" id="profile-company"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div class="pt-4">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="change-password-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-8">Change Password</h1>
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md border border-gray-200 max-w-lg mx-auto">
                <form action="#" method="POST" class="space-y-6">
                    <div>
                        <label for="current-password" class="block text-sm font-medium text-gray-700">Current
                            Password</label>
                        <input type="password" name="current_password" id="current-password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="new_password" id="new-password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="confirm-new-password" class="block text-sm font-medium text-gray-700">Confirm New
                            Password</label>
                        <input type="password" name="confirm_new_password" id="confirm-new-password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                    </div>
                    <div class="pt-4">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <i class="fas fa-key mr-2"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="mail-services-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-6">Mail Services</h1>
            <p>Mail management interface will be here.</p>
        </div>
        <div id="bookings-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-6">Room Bookings</h1>
            <p>Room booking history and management will be here.</p>
        </div>
        <div id="support-tickets-view" class="hidden">
            <h1 class="text-3xl font-bold text-blue-800 mb-6">Support Tickets</h1>
            <p>Support ticket system will be here.</p>
        </div>

    </main>
@endsection

@section('js')
     <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('dashboardContent');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full'); // For sliding
            sidebar.classList.toggle('translate-x-0');
            sidebarOverlay.classList.toggle('hidden');
            // Optional: prevent body scroll when sidebar is open on mobile
            // document.body.classList.toggle('overflow-hidden'); // md:overflow-auto on body if you use this
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }


        function setActiveLink(element) {
            // Remove active class from all sidebar links
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.classList.remove('active');
                // link.classList.remove('bg-orange-400'); // Tailwind handles this with .active class
                // link.classList.remove('text-white');
            });
            // Add active class to the clicked link
            if (element) {
                element.classList.add('active');
                // element.classList.add('bg-orange-400');
                // element.classList.add('text-white');
            }
        }

        function loadContent(viewName) {
            // Hide all views
            document.getElementById('dashboard-view').classList.add('hidden');
            document.getElementById('orders-view').classList.add('hidden');
            document.getElementById('profile-view').classList.add('hidden');
            document.getElementById('change-password-view').classList.add('hidden');
            document.getElementById('mail-services-view').classList.add('hidden');
            document.getElementById('bookings-view').classList.add('hidden');
            document.getElementById('support-tickets-view').classList.add('hidden');


            // Show the selected view
            const targetView = document.getElementById(viewName + '-view');
            if (targetView) {
                targetView.classList.remove('hidden');
            } else {
                console.error("View not found:", viewName + "-view");
                 document.getElementById('dashboard-view').classList.remove('hidden'); // Fallback to dashboard
            }

            // Update active link in sidebar
            const linkElement = document.querySelector(`.sidebar a[href="#${viewName}"]`);
            setActiveLink(linkElement);

            // If on mobile, close sidebar after navigation
            if (window.innerWidth < 768 && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }
        }

        // Initial load or on hash change
        document.addEventListener('DOMContentLoaded', () => {
            const initialHash = window.location.hash.substring(1);
            if (initialHash) {
                loadContent(initialHash);
            } else {
                loadContent('dashboard'); // Default view
            }
        });


        // Handle clicks on sidebar links (already good, just ensure setActiveLink is called)
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function(event) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    // event.preventDefault(); // Already handled by onclick attribute
                    // const viewName = href.substring(1);
                    // loadContent(viewName); // Already handled by onclick attribute
                    setActiveLink(this); // Ensure active state is set
                }
            });
        });

    </script>
@endsection
