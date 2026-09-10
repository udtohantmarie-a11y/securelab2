<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureLab Verification</title>
    <style>
        /* Base Reset para sa Email Clients */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        
        /* Layout & Structure */
        .email-wrapper { width: 100%; background-color: #f0f2f5; padding: 40px 20px; box-sizing: border-box; }
        .email-content { max-width: 550px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
        
        /* Header - Tech Gradient */
        .header { background: linear-gradient(135deg, #0a2540, #0d6efd); padding: 35px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 26px; letter-spacing: 3px; text-transform: uppercase; font-weight: 800; }
        .header p { color: #a9c5f8; margin: 8px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Main Body */
        .body { padding: 40px 35px; color: #333333; line-height: 1.6; font-size: 16px; }
        .body h2 { margin-top: 0; color: #0a2540; font-size: 20px; font-weight: 700; margin-bottom: 20px; }
        
        /* Magic Link Button */
        .btn-container { text-align: center; margin: 30px 0 20px; }
        .btn-verify {
            background-color: #0d6efd;
            color: #ffffff !important;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 50px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        /* Divider */
        .divider { 
            text-align: center; 
            color: #aaaaaa; 
            font-size: 12px; 
            letter-spacing: 2px;
            margin: 25px 0; 
            position: relative; 
        }
        
        /* OTP Box Enhancements */
        .otp-container { text-align: center; margin: 20px 0 35px; }
        .otp-box { 
            display: inline-block; 
            background-color: #f8f9fa; 
            border: 2px dashed #0d6efd; 
            padding: 15px 35px; 
            font-size: 38px; 
            font-weight: 800; 
            letter-spacing: 8px; 
            color: #0a2540; 
            border-radius: 10px; 
        }
        
        /* Security Warning Box */
        .warning-box { background-color: #fff8e1; border-left: 4px solid #ffc107; padding: 15px 20px; font-size: 14px; color: #665c00; margin-top: 30px; border-radius: 0 6px 6px 0; line-height: 1.5; }
        
        /* Footer */
        .footer { background-color: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #888888; border-top: 1px solid #eeeeee; }
        .footer p { margin: 5px 0; }
        .footer strong { color: #0a2540; }

        /* Mobile Responsiveness */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 20px 10px; }
            .body { padding: 30px 20px; }
            .header h1 { font-size: 22px; }
            .otp-box { font-size: 30px; letter-spacing: 5px; padding: 15px 20px; width: 100%; box-sizing: border-box; }
            .btn-verify { display: block; width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="header">
                <h1>SECURELAB</h1>
                <p>Advanced IoT Biometric Security</p>
            </div>
            
            <div class="body">
                <h2>Authentication Required</h2>
                <p>Hello <strong>{{ $userName }}</strong>,</p>
                <p>We received a request to access your SecureLab Dashboard. You can securely log in with a single click using the button below:</p>
                
                <!-- AUTO-VERIFY BUTTON -->
                <div class="btn-container">
                    <a href="{{ route('otp.auto-verify', ['code' => $otpCode]) }}" class="btn-verify">
                        Auto-Verify & Login
                    </a>
                </div>

                <div class="divider">OR USE MANUAL CODE</div>
                
                <!-- MANUAL OTP BOX -->
                <div class="otp-container">
                    <div class="otp-box">
                        {{ $otpCode }}
                    </div>
                </div>
                
                <p>Please note that this access link and code are only valid for the next 10 minutes. For your security, <strong>never share this email with anyone.</strong></p>
                
                <div class="warning-box">
                    <strong>Didn't request this?</strong><br> If you did not attempt to log in, please secure your account immediately and contact the system administrator.
                </div>
            </div>
            
            <div class="footer">
                <p>&copy; 2026 <strong>Talibon Polytechnic College</strong> - BSIS Department.</p>
                <p>This is an automated security message. Please do not reply to this email.</p>
            </div>
        </div>
    </div>
</body>
</html>