<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/fQc228F/favicon.png">
    <title>Password Changed</title>
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
                    <svg class="mx-auto h-16 w-16 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <h2 class="text-2xl font-bold text-gray-800 mt-4">Your Password Has Been Changed</h2>
                    <p class="text-gray-600 mt-2">Hello {{ $user->name }},</p>
                    <p class="text-gray-600 mt-2">The password for your Charlton Virtual Office account was just changed.</p>
                    <p class="text-gray-600 mt-2">If this was you, no further action is needed. If you did not make this change, please contact support immediately.</p>
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
