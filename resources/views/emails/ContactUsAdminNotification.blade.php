<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/fQc228F/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster:wght@400;700&display=swap" rel="stylesheet">
    <title>New Message - Charlton Virtual Office</title>

</head>

<body
    style='Margin:0;padding:0;color:#000000;background-color:#eeeeee;font-size:15px;font-family:"Montserrat", sans-serif;line-height:25px;'>
    <div class="content-outer" style="padding:20px;">
        <div class="content" style="background:#ffffff;margin:auto;max-width:600px;">
            <div class="header"
                style="height:100px;text-align:center;background-size:cover;background-repeat:no-repeat;background-position:center;background-color:#0fc55e;display:flex;">
                <div class="header-title"
                    style="margin:auto;color:#fff;font-size:50px;font-weight:bold;font-family:'Lobster', cursive;">
                    CharltonVirtualOffice.com
                </div>
            </div>
            <div class="body" style="padding:30px 20px;">
                <div>
                    Dear Admin,
                </div>
                <div style="margin-top: 20px;">
                    A new message was received.
                    <br>
                    Please review the message details below:
                </div>
                <div class="divider" style="margin:30px 0;height:2px;background-color:#eee;"></div>
                <div style="margin-top: 10px;">
                    <div style="margin-bottom: 15px;">
                        <span style="font-weight: bold;">
                            Full Name :
                        </span>
                        <span>
                            {{$contactUs['name']}}
                        </span>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <span style="font-weight: bold;">
                            Email Address :
                        </span>
                        <span>
                            {{$contactUs['email']}}
                        </span>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="font-weight: bold;">
                            Subject :
                        </span>
                        <span>
                            {{$contactUs['subject']}}
                        </span>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <span style="font-weight: bold;">
                            Message :
                        </span>
                        <span>
                            {!! nl2br( $contactUs['message'])!!}
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer" style="padding:30px 20px;font-size:12px;background-color:#f8f8f8;">
                
                <br>
                If this email you received, is not as a result of a recent action on <a href="https://charltonvirtualoffice.com"
                    style="color:#0fc55e;">https://charltonvirtualoffice.com</a>,
                please contact NIN UK team at <a href="mailto:info@ninuk.co.uk"
                    style="color:#0fc55e;">info@charltonvirtualoffice.com</a>.
            </div>
            <div class="footer-bottom" style="height:20px;text-align:center;background-color:#0fc55e;"></div>
        </div>
    </div>
</body>

</html>
