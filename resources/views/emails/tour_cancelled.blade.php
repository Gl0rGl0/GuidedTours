<!DOCTYPE html>
<html>
<head>
    <title>Notice: Tour Cancelled</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0d6efd;">Tour Cancellation Notice</h2>
        <p>Dear {{ $user->first_name }},</p>
        <p>We are writing to inform you that the upcoming tour you booked, <strong>{{ $visit->visitType->title }}</strong> scheduled for <strong>{{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}</strong>, has been <strong>cancelled</strong>.</p>
        <p>Unfortunately, the minimum number of participants required to run the tour was not reached.</p>
        <p>We apologize for any inconvenience this may cause. We hope to see you on another one of our tours soon!</p>
        <br>
        <p>Best regards,</p>
        <p><strong>The Guided Tours Team</strong></p>
    </div>
</body>
</html>
