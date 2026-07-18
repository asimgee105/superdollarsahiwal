<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AURA Verification OTP' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; color: #333333; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f9f9f9; padding: 40px 0; }
        .container { max-width: 580px; margin: 0 auto; background-color: #ffffff; border: 1px solid #eaeaea; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-b: 1px solid #f5f5f5; }
        .logo { font-size: 24px; font-weight: 900; color: #ff3f6c; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; text-align: center; }
        .title { font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px; }
        .description { font-size: 14px; line-height: 1.6; color: #666666; margin-bottom: 30px; }
        .otp-box { font-size: 32px; font-weight: 900; color: #ff3f6c; background-color: #fff0f3; border: 1px dashed #ffb8c6; border-radius: 8px; padding: 15px 30px; display: inline-block; letter-spacing: 6px; margin-bottom: 30px; }
        .expiry-text { font-size: 12px; color: #999999; margin-top: 10px; }
        .footer { background-color: #fafafa; padding: 20px; text-align: center; border-t: 1px solid #eaeaea; }
        .footer-text { font-size: 11px; color: #999999; line-height: 1.5; }
        .footer-link { color: #ff3f6c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo">AURA Enterprise</div>
            </div>
            <div class="content">
                <h1 class="title">{{ $title }}</h1>
                <p class="description">
                    {{ $description ?? 'Use the secure OTP code below to complete your authentication request.' }}
                </p>
                <div class="otp-box">{{ $code }}</div>
                <p class="expiry-text">
                    This OTP is single-use only and will expire in <strong>{{ $expiry ?? 10 }} minutes</strong>.
                </p>
            </div>
            <div class="footer">
                <p class="footer-text">
                    This is an automated security notification from <a href="https://www.superdollarsahiwal.com" class="footer-link">superdollarsahiwal.com</a>.<br>
                    If you did not initiate this request, please ignore this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
