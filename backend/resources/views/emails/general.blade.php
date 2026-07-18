<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AURA Notification' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; color: #333333; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f9f9f9; padding: 40px 0; }
        .container { max-width: 580px; margin: 0 auto; background-color: #ffffff; border: 1px solid #eaeaea; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #f5f5f5; }
        .logo { font-size: 24px; font-weight: 900; color: #ff3f6c; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; text-align: left; }
        .title { font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px; text-align: center; }
        .paragraph { font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { background-color: #ff3f6c; color: #ffffff !important; font-size: 11px; font-weight: 900; text-transform: uppercase; text-decoration: none; padding: 14px 28px; border-radius: 4px; display: inline-block; letter-spacing: 1.5px; box-shadow: 0 4px 6px rgba(255, 63, 108, 0.2); }
        .details-box { background-color: #fcfcfc; border: 1px solid #eaeaea; border-radius: 6px; padding: 20px; margin: 20px 0; }
        .detail-item { font-size: 13px; line-height: 1.8; color: #555555; }
        .detail-label { font-weight: bold; color: #1a1a1a; }
        .footer { background-color: #fafafa; padding: 20px; text-align: center; border-top: 1px solid #eaeaea; }
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
                
                @if(!empty($greeting))
                    <p class="paragraph" style="font-weight: bold; color: #1a1a1a;">{{ $greeting }}</p>
                @endif

                <p class="paragraph">{{ $message_text }}</p>

                @if(!empty($details))
                    <div class="details-box">
                        @foreach($details as $label => $value)
                            <div class="detail-item">
                                <span class="detail-label">{{ $label }}:</span> {!! $value !!}
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($button_text) && !empty($button_url))
                    <div class="button-container">
                        <a href="{{ $button_url }}" class="button">{{ $button_text }}</a>
                    </div>
                @endif
            </div>
            <div class="footer">
                <p class="footer-text">
                    This is an automated notification from <a href="https://www.superdollarsahiwal.com" class="footer-link">superdollarsahiwal.com</a>.<br>
                    © 2026 AURA Commerce. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
