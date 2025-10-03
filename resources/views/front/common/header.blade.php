<header class="bg-blue-700 text-white shadow-md sticky top-0 z-50">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="{{ route('home.index') }}" class="text-2xl font-bold hover:text-orange-300 transition duration-300">
            <i class="fas fa-building mr-2"></i> Charlton Virtual Office
        </a>
        <ul class="hidden md:flex space-x-6 items-center">
            <li><a href="{{ route('home.index') }}" class="{{ request()->is('/') ? 'text-orange-300 font-medium border-b-2 border-orange-300 pb-1' : '' }}">Home</a></li>
            <li><a href="{{ route('virtual-address.index') }}" class="{{ request()->routeIs('virtual-address.*')  ? 'text-orange-300 font-medium border-b-2 border-orange-300 pb-1' : '' }}">Virtual Office Address</a></li>
            <li><a href="{{ route('meeting-rooms.index') }}" class="{{ request()->routeIs('meeting-rooms.*')  ? 'text-orange-300 font-medium border-b-2 border-orange-300 pb-1' : '' }}">Meeting Rooms</a></li>
            <li><a href="{{ route('conference-rooms.index') }}" class="{{ request()->routeIs('conference-rooms.*')  ? 'text-orange-300 font-medium border-b-2 border-orange-300 pb-1' : '' }}">Conference Rooms</a></li>
            <li>
                <a href="{{ route('contact-us.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg transition duration-300 shadow">
                    Contact Us <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </li>
            <li>
                @if(!Auth::user())
                    <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Login
                    </a>
                @else
                    <div class="relative">
    
                        <a href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            User Panel
                        </a>       
                        
                        <div class="absolute  border border-gray-200 rounded-lg shadow-lg hidden group-hover:block">
                            <a href="{{route('dashboard')}}" class="block px-4 py-2 hover:bg-orange-700">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"  class="block px-4 py-2 hover:bg-orange-700">Logout</button>
                            </form>
                        </div>
                    </div>
                @endif
            </li>
            @if (Cart::count() > 0)
                <li> 
                    <a href="{{ route('cart.index') }}" class="text-xl hover:text-orange-300 transition duration-300 relative">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                {{ Cart::count() }}
                            </span> 
                    </a>
                </li>
            @endif
        </ul>
        <button class="md:hidden focus:outline-none">
             <i class="fas fa-bars text-2xl"></i>
        </button>
    </nav>
</header>

