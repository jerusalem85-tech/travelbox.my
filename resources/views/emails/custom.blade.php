<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>body{font-family:sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px}
.header{border-bottom:2px solid #e5e7eb;padding-bottom:12px;margin-bottom:20px}
.header h1{font-size:20px;color:#1a56db;margin:0}
.body-content{font-size:14px;white-space:pre-wrap;line-height:1.7}
.footer{border-top:2px solid #e5e7eb;padding-top:12px;margin-top:20px;font-size:12px;color:#9ca3af}</style></head>
<body>
<div class="header">
    <h1>TravelBox</h1>
    @if ($tripNumber)<p style="margin:4px 0 0;font-size:13px;color:#6b7280">{{ $tripNumber }}</p>@endif
</div>
<div class="body-content">{{ $body }}</div>
<div class="footer">
    <p>Sent via TravelBox ERP | travelbox.my</p>
</div>
</body>
</html>
