<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Notification</title>
</head>
<body>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Please click the link below to reset your password. This link will expire in 60 minutes.</p>
    
    <p><a href="{{ $resetUrl }}">Reset Password</a></p>

    <p>If you did not request a password reset, no further action is required.</p>
    <br>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
