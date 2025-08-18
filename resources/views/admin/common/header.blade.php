<header class="bg-blue-700 text-white shadow-md sticky top-0 z-50 h-16">
    <nav class="container mx-auto px-4 sm:px-6 h-full flex justify-between items-center">
        <div class="flex items-center">
            <button id="sidebarToggle" class="md:hidden mr-3 text-white hover:text-orange-300 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <a href="{{ route('admin.dashboard.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
                <i class="fas fa-building mr-2"></i> NURUD Admin Panel
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <span class="hidden sm:inline text-sm">Welcome, {{ auth()->user()->name }}</span> <a href="#" class="text-sm hover:text-orange-300 transition duration-300" title="Notifications">
                <i class="fas fa-bell"></i>
            </a>
            <a href="#profile" onclick="loadContent('profile'); setActiveLink(this); return false;" class="text-sm hover:text-orange-300 transition duration-300" title="My Profile">
                <i class="fas fa-user-circle"></i>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf

                <button  class="text-sm bg-orange-500 hover:bg-orange-600 px-3 py-1 rounded-md transition duration-300">
                         <i class="fas fa-sign-out-alt md:mr-1"></i> <span class="hidden md:inline">Logout</span>
                </button>
            </form>
           
        </div>
    </nav>
</header>