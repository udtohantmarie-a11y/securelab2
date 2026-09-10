<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; border-top: 5px solid #dc3545; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .text-danger { color: #dc3545; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 12px; color: #6c757d; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 class="text-danger">🚨 SecureLab Security Alert</h2>
        <p>Hello Admin,</p>
        <p>An abnormal activity has been detected in the laboratory. Please review the details below:</p>
        <hr>
        <p><strong>Incident Details:</strong> {{ $alertDetails }}</p>
        <p><strong>Time Detected:</strong> {{ now()->timezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
        <hr>
        <p class="footer">This is an automated priority message from the SecureLab Smart Access System. Please check the dashboard immediately to secure the premises.</p>
    </div>
</body>
</html>