<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/fQc228F/favicon.png">
    <title>Welcome to Charlton Virtual Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #1d4ed8;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .content {
            padding: 32px;
        }
        .footer {
            background-color: #1e3a8a;
            color: #dbeafe;
            padding: 24px;
            text-align: center;
            font-size: 12px;
        }
        .footer a {
            color: #93c5fd;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Charlton Virtual Office</h1>
            </div>
            <div class="content">
                <div class="text-center mb-8">
                    <svg class="mx-auto h-16 w-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"></path></svg>
                    <h2 class="text-2xl font-bold text-blue-800 mt-4">Welcome, {{ $user->name }}!</h2>
                    <p class="text-gray-600 mt-2">Thank you for creating an account with Charlton Virtual Office.</p>
                    <p class="text-gray-600 mt-2">Your account is now active. Please review your cart and continue to checkout to complete your subscription.</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-md text-left my-8 border border-blue-200">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">Your Account Details</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone }}</p>
                    </div>
                </div>

                <div class="text-center mt-10">
                    <a href="{{ route('dashboard') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 shadow-md inline-block">
                        Go to Dashboard
                    </a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Charlton Virtual Office. All Rights Reserved.</p>
                <p class="mt-2">
                    <a href="{{ url('/contact-us') }}">Contact Support</a> |
                    <a href="{{ url('/') }}">Visit our Website</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
