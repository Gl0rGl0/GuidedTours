<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $registration->booking_code }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .ticket-box { border: 2px solid #0d6efd; padding: 20px; border-radius: 10px; margin: 20px; }
        .header { border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; clear: both; }
        .title { font-size: 24px; font-weight: bold; color: #0d6efd; float: left; }
        .status-badge { background: #198754; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px; text-transform: uppercase; float: right; margin-top: 5px; }
        .info-row { margin-bottom: 15px; font-size: 14px; }
        .label { font-weight: bold; width: 130px; display: inline-block; color: #555; }
        .qr-code { text-align: center; margin-top: 40px; }
        .footer { font-size: 11px; text-align: center; margin-top: 50px; color: #777; border-top: 1px solid #eee; padding-top: 15px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="ticket-box">
        <div class="header">
            <span class="title">Guided Tours Ticket</span>
            <span class="status-badge">Valid</span>
            <div class="clear"></div>
        </div>

        <div class="info-row">
            <span class="label">Booking Code:</span>
            <strong style="font-size: 16px;">{{ $registration->booking_code }}</strong>
        </div>

        <div class="info-row">
            <span class="label">Tour Title:</span>
            {{ $registration->visit->visitType->title }}
        </div>

        <div class="info-row">
            <span class="label">Location:</span>
            {{ $registration->visit->visitType->place->name }}
        </div>

        <div class="info-row">
            <span class="label">Date:</span>
            {{ \Carbon\Carbon::parse($registration->visit->visit_date)->format('l, F j, Y') }}
        </div>

        <div class="info-row">
            <span class="label">Start Time:</span>
            {{ \Carbon\Carbon::parse($registration->visit->effective_start_time ?? $registration->visit->visitType->start_time)->format('g:i A') }}
        </div>

        <div class="info-row">
            <span class="label">Meeting Point:</span>
            {{ $registration->visit->visitType->meeting_point }}
        </div>

        <div class="info-row">
            <span class="label">Num Participants:</span>
            {{ $registration->num_participants }}
        </div>

        <div class="info-row">
            <span class="label">Booked By:</span>
            {{ $registration->user->first_name }} {{ $registration->user->last_name }}
        </div>

        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" width="150" height="150">
            <br>
            <small style="color: #999;">Scan to verify</small>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} City Heritage Tours / UniBS<br>
            Please present this ticket to the guide.
        </div>
    </div>
</body>
</html>
