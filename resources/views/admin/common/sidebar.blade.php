<aside id="sidebar"
    class="sidebar bg-blue-800 text-blue-100 p-4 space-y-2 shadow-lg
                       fixed inset-y-0 left-0 top-16 md:top-0 md:sticky md:h-screen md:w-64 md:translate-x-0 
                       transform -translate-x-full md:translate-x-0 
                       transition-transform duration-300 ease-in-out z-40 overflow-y-auto">
    <h3 class="text-xl uppercase text-blue-300 font-semibold mb-3 mt-2 px-2 pt-14 md:pt-2">Main Menu</h3> 
    <a href="{{ route('admin.dashboard.index') }}" class="block py-2.5 px-4 rounded-md 
        {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt mr-3 w-5 text-center text-orange-400"></i> Dashboard
    </a>
    <h3 class="text-xl uppercase text-blue-300 font-semibold pt-6 mb-3 px-2">Services</h3>
    <a href="{{ route('admin.plans.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/plans*') ? 'active' : '' }}" >
        <i class="fas fa-map-marker-alt mr-3 w-5 text-center text-orange-400"></i> Virtual Addresses
    </a>
    <a href="{{ route('admin.meeting-rooms.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/meeting-rooms*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt mr-3 w-5 text-center text-orange-400"></i> Meeting Rooms 
    </a>

    <a href="{{ route('admin.conference-rooms.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/conference-rooms*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt mr-3 w-5 text-center text-orange-400"></i> Conference Rooms 
    </a>

    <a href="{{ route('admin.orders.index') }}" class="block py-2.5 px-4 rounded-md 
        {{ request()->is('admin/orders*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart mr-3 w-5 text-center text-orange-400"></i> My Orders
    </a>
    <a href="{{ route('admin.mails.index') }}" class="block py-2.5 px-4 rounded-md 
        {{ request()->is('admin/mails*') ? 'active' : '' }}">
        <i class="fas fa-envelope-open-text mr-3 w-5 text-center text-orange-400"></i> Mail Services
    </a>
    <a href="#bookings" class="block py-2.5 px-4 rounded-md">
        <i class="fas fa-calendar-alt mr-3 w-5 text-center text-orange-400"></i> Room Bookings
    </a>

    <h3 class="text-xl uppercase text-blue-300 font-semibold pt-6 mb-3 px-2">Settings</h3>
    <a href="{{ route('admin.feature-settings.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/feature-settings*') ? 'active' : '' }}">
        <i class="fas fa-life-ring mr-3 w-5 text-center text-orange-400"></i> Feature Setting
    </a>
    <a href="{{ route('admin.mail-settings.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/mail-settings*') ? 'active' : '' }}">
        <i class="fas fa-envelope-open-text mr-3 w-5 text-center text-orange-400"></i> Mail Prices
    </a>
    <a href="{{ route('admin.plan-room-discounts.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/plan-room-discounts*') ? 'active' : '' }}">
        <i class="fas fa-tags mr-3 w-5 text-center text-orange-400"></i> Plan Room Discounts
    </a>
    <a href="{{ route('admin.opening-days.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/opening-days*') ? 'active' : '' }}">
        <i class="fas fa-clock mr-3 w-5 text-center text-orange-400"></i> Work Hours
    </a>
    <a href="{{ route('admin.off-days.index') }}" class="block py-2.5 px-4 rounded-md
        {{ request()->is('admin/off-days*') ? 'active' : '' }}">
        <i class="fas fa-calendar-times mr-3 w-5 text-center text-orange-400"></i> Off Days
    </a>
    <a href="#profile" class="block py-2.5 px-4 rounded-md">
        <i class="fas fa-user-edit mr-3 w-5 text-center text-orange-400"></i> User Profile
    </a>
    <a href="#change-password" class="block py-2.5 px-4 rounded-md">
        <i class="fas fa-key mr-3 w-5 text-center text-orange-400"></i> Change Password
    </a>

    <div class="pt-8">
       
        <form method="POST" action="{{ route('admin.logout') }}">
                @csrf

            <button  class="block text-center py-2.5 px-4 rounded-md bg-orange-500 hover:bg-orange-600 text-white transition duration-300">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </button>
        </form>
    </div>
</aside>
