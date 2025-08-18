<!-- Mobile Menu Toggle Button -->
<div class="md:hidden flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold text-blue-800">My Account</h3>
    <button id="toggleSidebarBtn" class="text-blue-800 focus:outline-none text-xl">
        <i class="fas fa-bars"></i>
    </button>
</div>

<!-- Sidebar -->
<aside id="userSidebar" class="user-sidebar md:sticky md:top-20 bg-white p-6 rounded-lg shadow-md h-fit flex-shrink-0 hidden md:block">
    <h3 class="text-lg font-semibold text-blue-800 mb-4 border-b pb-2 hidden md:block">My Account</h3>
    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
        {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt fa-fw mr-2 text-gray-500"></i>Dashboard
        </a>
        <a href="{{ route('virtual-address-orders.index') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
        {{ request()->is('virtual-address-orders*') ? 'active' : '' }}">
            <i class="fas fa-map-marker-alt fa-fw mr-2 text-gray-500"></i>Virtual Address Plan
        </a>
        <a href="{{ route('meeting-room-orders.index') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
         {{ request()->is('meeting-room-orders*') ? 'active' : '' }}">
            <i class="fas fa-users fa-fw mr-2 text-gray-500"></i>Meeting Room Bookings
        </a>
        <a href="{{ route('conference-room-orders.index') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
         {{ request()->is('conference-room-orders*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher fa-fw mr-2 text-gray-500"></i>Conference Room Bookings
        </a>
        <a href="{{ route('mails.index') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
         {{ request()->is('mails*') ? 'active' : '' }}">
            <i class="fas fa-envelope-open-text fa-fw mr-2 text-gray-500"></i>Mail Requests
        </a>
         <a href="{{ route('invoices.index') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
         {{ request()->is('invoices*') ? 'active' : '' }}">
            <i class="fas fa-envelope-open-text fa-fw mr-2 text-gray-500"></i>Invoices
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
            {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="fas fa-user-edit fa-fw mr-2 text-gray-500"></i>My Profile
        </a>
        <a href="{{ route('profile.edit') }}#update-password" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md 
            {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="fas fa-user-edit fa-fw mr-2 text-gray-500"></i>Update Password
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center py-2 px-3 text-gray-700 hover:text-blue-700 rounded-md mt-4 border-t pt-3">
                <i class="fas fa-sign-out-alt fa-fw mr-2 text-gray-500"></i>Logout
            </button>
        </form>
    </nav>
</aside>