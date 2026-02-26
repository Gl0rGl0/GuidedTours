<!DOCTYPE html>
<html>
<head>
    <title>{{ __('messages.emails.tour_cancelled.subject') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0d6efd;">{{ __('messages.emails.tour_cancelled.header') }}</h2>
        <p>{{ __('messages.emails.tour_cancelled.greeting', ['name' => $user->first_name]) }}</p>
        <p>{!! __('messages.emails.tour_cancelled.line1', ['tour_name' => e($visit->visitType->title), 'date' => \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y')]) !!}</p>
        <p>{{ __('messages.emails.tour_cancelled.line2') }}</p>
        <p>{{ __('messages.emails.tour_cancelled.line3') }}</p>
        <br>
        <p>{!! __('messages.emails.tour_cancelled.regards') !!}</p>
    </div>
</body>
</html>
