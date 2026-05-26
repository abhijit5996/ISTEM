@php
  $instrumentName = optional($booking->instrument)->name ?? $booking->instrument_id;
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking Rejected</title>
</head>
<body style="margin:0;background:#fff7f7;padding:24px 12px;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:720px;margin:0 auto;background:#ffffff;border:1px solid #ffd3d8;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(127,29,29,0.1);">
    <tr>
      <td style="padding:28px 28px 12px 28px;background:linear-gradient(135deg,#f43f5e 0%,#fb7185 50%,#f97316 100%);color:#fff;">
        <div style="font-size:12px;letter-spacing:.16em;text-transform:uppercase;font-weight:700;opacity:.88;">Booking Rejected</div>
        <h1 style="margin:10px 0 0;font-size:30px;line-height:1.1;">Your instrument booking was rejected</h1>
      </td>
    </tr>
    <tr>
      <td style="padding:28px;">
        <p style="margin:0 0 14px;font-size:16px;line-height:1.7;">Hi {{ $booking->name }},</p>
        <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#334155;">We could not approve this booking request at this time.</p>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate;border-spacing:0 10px;">
          <tr>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;width:44%;font-weight:700;color:#7f1d1d;">User</td>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;">{{ $booking->name }}</td>
          </tr>
          <tr>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;font-weight:700;color:#7f1d1d;">Email</td>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;">{{ $booking->email ?: $booking->user_email }}</td>
          </tr>
          <tr>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;font-weight:700;color:#7f1d1d;">Instrument</td>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;">{{ $instrumentName }}</td>
          </tr>
          <tr>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;font-weight:700;color:#7f1d1d;">Booking Window</td>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;">{{ $booking->start_date }} → {{ $booking->end_date }}</td>
          </tr>
          <tr>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;font-weight:700;color:#7f1d1d;">Status</td>
            <td style="padding:14px 16px;background:#fffafa;border:1px solid #ffd3d8;border-radius:14px;">Rejected</td>
          </tr>
        </table>

        @if(!empty($booking->rejection_reason))
          <div style="margin-top:20px;padding:16px 18px;border-left:4px solid #f43f5e;background:#fff1f2;border-radius:12px;">
            <div style="font-weight:700;margin-bottom:6px;color:#0f172a;">Rejection reason</div>
            <div style="color:#334155;line-height:1.7;">{{ $booking->rejection_reason }}</div>
          </div>
        @endif

        @if(!empty($booking->admin_comment))
          <div style="margin-top:16px;padding:16px 18px;border-left:4px solid #fb7185;background:#fff7f8;border-radius:12px;">
            <div style="font-weight:700;margin-bottom:6px;color:#0f172a;">Admin remarks</div>
            <div style="color:#334155;line-height:1.7;">{{ $booking->admin_comment }}</div>
          </div>
        @endif

        <p style="margin:24px 0 0;font-size:15px;line-height:1.7;color:#334155;">Please contact the admin team if you need any clarification.</p>
      </td>
    </tr>
  </table>
</body>
</html>
