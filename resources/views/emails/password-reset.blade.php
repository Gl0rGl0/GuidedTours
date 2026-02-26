<!DOCTYPE html>
<html>
<head>
    <title>{{ __('messages.emails.password_reset.subject') }}</title>
</head>
<body>
    <p>{{ __('messages.emails.password_reset.line1') }}</p>
    <p>{{ __('messages.emails.password_reset.line2') }}</p>
    
    <p><a href="{{ $resetUrl }}">{{ __('messages.emails.password_reset.action') }}</a></p>

    <p>{{ __('messages.emails.password_reset.line3') }}</p>
    <br>
    <p>{!! __('messages.emails.password_reset.regards', ['app_name' => config('app.name')]) !!}</p>
</body>
</html>
