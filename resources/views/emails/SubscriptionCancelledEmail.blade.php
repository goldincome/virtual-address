<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $stage === 'ended' ? 'Subscription Ended' : 'Subscription Cancelled' }} - Charlton Virtual Office</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .email-wrapper { background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .header { background-color: #1d4ed8; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { font-size: 24px; font-weight: 700; margin: 0; }
        .content { padding: 32px; }
        .footer { background-color: #1e3a8a; color: #dbeafe; padding: 24px; text-align: center; font-size: 12px; }
        .footer a { color: #93c5fd; text-decoration: none; }
        .btn { background-color: #f97316; color: #ffffff !important; text-decoration: none; font-weight: 600; padding: 12px 24px; border-radius: 8px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Charlton Virtual Office</h1>
            </div>
            <div class="content">
                @if($stage === 'ended')
                    <h2 class="text-2xl font-bold text-blue-800 mt-4">Your Subscription Has Ended</h2>
                    <p class="text-gray-600 mt-2">Dear {{ $user->name }},</p>
                    <p class="text-gray-600 mt-2">
                        Your virtual office subscription ended on
                        <strong>{{ $endsAt ? $endsAt->format('F j, Y') : 'the end of your billing period' }}</strong>.
                        You no longer have access to your virtual address plan features.
                    </p>
                    <p class="text-gray-600 mt-2">
                        If this was a mistake or you would like to rejoin, you can subscribe again at any time.
                    </p>
                @else
                    <h2 class="text-2xl font-bold text-blue-800 mt-4">Your Subscription Has Been Cancelled</h2>
                    <p class="text-gray-600 mt-2">Dear {{ $user->name }},</p>
                    <p class="text-gray-600 mt-2">
                        We have received your cancellation request. Your subscription will stay active until
                        <strong>{{ $endsAt ? $endsAt->format('F j, Y, g:i a') : 'the end of your current billing period' }}</strong>,
                        after which you will no longer be billed.
                    </p>
                    <p class="text-gray-600 mt-2">
                        You can resume your subscription at any time before it ends by logging in to your dashboard.
                    </p>
                @endif

                <div class="text-center mt-10">
                    <a href="{{ route('dashboard') }}" class="btn">Go to Dashboard</a>
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