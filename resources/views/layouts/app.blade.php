
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <title>@yield('title') - Charlton Virtual Office</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="msapplication-TileImage" content="{{ asset('favicon.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <meta name="author" content="Charlton Virtual Office">
    <meta property="og:title" content="@yield('title')"/>
    <meta property="og:description" content="@yield('description')"/>
    <meta name="robots" content= "index, follow">
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{{ request()->url() }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('og')

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
     @vite(['resources/css/admin.css', 'resources/js/app.js'])
    @yield('css')
    <style>
         body { font-family: 'Inter', sans-serif; }
        .hero-bg-orders {
            background-image: url('https://source.unsplash.com/random/1920x400/?office,documents,desk');
            background-size: cover;
            background-position: center;
        }
        footer a:not(.social-icon) { @apply hover:text-orange-300 transition duration-300 hover:underline; }
        .status-active { @apply bg-green-100 text-green-800; }
        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }
        .status-completed { @apply bg-blue-100 text-blue-800; }

        /* Sidebar for User Account Area */
        .user-sidebar {
            width: 260px; /* Fixed width */
        }
        .user-sidebar a {
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-left-color 0.2s ease-in-out;
            border-left: 4px solid transparent;
        }
        .user-sidebar a.active, .user-sidebar a:hover {
            background-color: #eff6ff; /* blue-50 */
            color: #1d4ed8; /* blue-700 */
            border-left-color: #f97316; /* orange-500 */
        }
        .user-sidebar a.active i, .user-sidebar a:hover i {
            color: #f97316; /* orange-500 */
        }
    </style>
</head>
<body class="bg-gray-100">

    @include('front.common.header')
    <section class="hero-bg-orders text-white relative">
        <div class="absolute inset-0 bg-blue-800 opacity-70"></div>
        <div class="container mx-auto px-6 py-16 md:py-10 relative z-10 text-center">
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">@yield('page_title')</h1>
            <p class="text-md md:text-lg text-blue-100 max-w-2xl mx-auto mt-2">@yield('page_intro')</p>
        </div>
    </section>
    <div class="container mx-auto px-6 py-12 md:py-4 flex flex-col md:flex-row gap-4">
    
   
        @include('front.common.sidebar')
        <main class="flex-grow">
            @include('front.common.error-and-message')
            @yield('content')
        </main>
    </div>

    @include('front.common.footer')
    @yield('js')

    <script>
       
        const dropdown = document.querySelector('.absolute'); // Select the dropdown menu
        const button = document.querySelector('.bg-blue-500'); // Select the button

        button.addEventListener('click', () => {
        dropdown.classList.toggle('hidden'); // Toggle the 'hidden' class to show/hide the dropdown
        });

        document.addEventListener('click', (event) => {
        if (!button.contains(event.target) && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden'); // Close the dropdown if a click occurs outside
        }
        });
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('userSidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });

    </script>
</body>
</html>

