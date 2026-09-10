<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset | SecureLab</title>
    <style>
        /* Base Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        
        /* Layout Structure */
        .email-wrapper { width: 100%; background-color: #f0f2f5; padding: 40px 20px; box-sizing: border-box; }
        .email-content { max-width: 550px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
        
        /* Header - Dark Tech Theme */
        .header { background: linear-gradient(135deg, #4b0082, #0d6efd); padding: 35px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 26px; letter-spacing: 3px; text-transform: uppercase; font-weight: 800; }
        .header p { color: #e0e0ff; margin: 8px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Body */
        .body { padding: 40px 35px; color: #333333; line-height: 1.6; font-size: 16px; }
        .body h2 { margin-top: 0; color: #0a2540; font-size: 20px; font-weight: 700; margin-bottom: 20px; }
        
        /* OTP Box */
        .otp-container { text-align: center; margin: 35px 0; }
        .otp-box { 
            display: inline-block; 
            background-color: #f8f9fa; 
            border: 2px dashed #4b0082; 
            padding: 15px 35px; 
            font-size: 38px; 
            font-weight: 800; 
            letter-spacing: 8px; 
            color: #4b0082; 
            border-radius: 10px; 
        }
        
        /* Security Warning */
        .warning-box { background-color: #fff8e1; border-left: 4px solid #ffc107; padding: 15px 20px; font-size: 14px; color: #665c00; margin-top: 30px; border-radius: 0 6px 6px 0; line-height: 1.5; }
        
        /* Footer */
        .footer { background-color: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #888888; border-top: 1px solid #eeeeee; }
        
        /* Mobile */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 20px 10px; }
            .body { padding: 30px 20px; }
            .header h1 { font-size: 22px; }
            .otp-box { font-size: 30px; letter-spacing: 5px; padding: 15px 20px; width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="header">
                <h1>SECURELAB</h1>
                <p>Security Alert</p>
            </div>
            
            <div class="body">
                <h2>Password Reset Request</h2>
                <p>Hello <strong>{{ $userName }}</strong>,</p>
                <p>We received a request to reset the password for your SecureLab account. Please use the verification code below to authorize this change:</p>
                
                <div class="otp-container">
                    <div class="otp-box">
                        {{ $otpCode }}
                    </div>
                </div>
                
                <p>If you requested this change, please enter the code in the system to set up your new password.</p>
                
                <div class="warning-box">
                    <strong>Did not request a password reset?</strong><br> Please ignore this email. Your password will remain unchanged. Secure your account if you suspect unauthorized activity.
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