<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification | SecureLab</title>
    
    <!-- Light Theme Color -->
    <meta name="theme-color" content="#f4f7f6">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* 🟢 MODERN UI ENHANCEMENTS (Matched with Dashboard) */
        body { 
            background-color: #f4f7f6; 
            font-family: 'Poppins', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0;
            overflow-x: hidden;
        }

        /* 🟢 BACKGROUND ANIMATION (Light Theme Orbs) */
        .bg-animation-container {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            overflow: hidden;
            background-color: #f4f7f6;
        }

        .glowing-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.3;
            animation: floatOrb 20s infinite alternate ease-in-out;
        }

        /* Using Dashboard's Primary Blue and Success Teal */
        .orb-1 { width: 450px; height: 450px; background: #0d6efd; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 550px; height: 550px; background: #20c997; bottom: -150px; right: -100px; animation-delay: -5s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(80px, 120px) scale(1.1); }
            100% { transform: translate(-60px, 60px) scale(0.9); }
        }

        /* 🟢 AUTH CARD (Matched Dashboard Cards) */
        .otp-card { 
            background: #fff;
            border: none;
            padding: 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
            max-width: 480px; 
            width: 90%; 
            text-align: center; 
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* Faint Background Icon (Watermark) */
        .watermark-icon { 
            position: absolute; 
            right: -20px; 
            bottom: -20px; 
            font-size: 180px; 
            opacity: 0.02; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: #0d6efd;
        }

        /* Gradient Accents */
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }

        /* Dashboard-style Icon Box */
        .stat-icon-wrapper { 
            width: 75px; 
            height: 75px; 
            border-radius: 18px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 32px; 
            margin: 0 auto 25px;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(13, 110, 253, 0); }
            100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
        }

        /* 🟢 OTP INPUT BOXES (Light Theme) */
        .otp-inputs-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 30px;
            margin-top: 10px;
            position: relative;
            z-index: 2;
        }

        .otp-box-single {
            width: 55px;
            height: 65px;
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background-color: #f8f9fa;
            color: #212529;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .otp-box-single:focus {
            outline: none;
            border-color: #0d6efd;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            transform: translateY(-2px);
        }

        /* Hide Number Spinners */
        .otp-box-single::-webkit-outer-spin-button,
        .otp-box-single::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .otp-box-single[type=number] {
            -moz-appearance: textfield;
        }

        /* Primary Button */
        .control-btn {
            transition: all 0.3s ease;
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 2;
        }
        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
        }

        /* Resend Link Hover */
        .btn-link { position: relative; z-index: 2; }
        .btn-link:hover { color: #0a2540 !important; }

        @media (max-width: 480px) {
            .otp-box-single { width: 45px; height: 55px; font-size: 24px; }
            .otp-inputs-container { gap: 5px; }
            .otp-card { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <!-- 🟢 BACKGROUND ANIMATION LAYER -->
    <div class="bg-animation-container">
        <div class="glowing-orb orb-1"></div>
        <div class="glowing-orb orb-2"></div>
    </div>

    <!-- 🟢 MAIN CARD -->
    <div class="otp-card">
        <i class="fas fa-shield-alt watermark-icon"></i>
        
        <div class="stat-icon-wrapper bg-gradient-primary">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h3 class="fw-bold mb-2 text-dark" style="letter-spacing: -0.5px;">Two-Step Verification</h3>
        <p class="text-muted small mb-4 fw-medium">We've sent a 6-digit authentication code to your registered official email address.</p>

        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert small py-2 rounded-3 fw-bold border-0 text-success shadow-sm" style="background-color: #e7f6ea;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert small py-2 rounded-3 fw-bold border-0 text-danger shadow-sm" style="background-color: #ffe3e3;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- AUTOFILLED CODE SPLITTER -->
        @php
            $autoCode = session('autofill_code', '');
            $digits = $autoCode ? str_split((string)$autoCode) : array_fill(0, 6, '');
        @endphp

        <!-- FORM -->
        <form action="{{ route('otp.verify.post') }}" method="POST" id="otpForm">
            @csrf
            
            <input type="hidden" name="otp" id="actualOtp" value="{{ $autoCode }}" required>
            
            <div class="otp-inputs-container">
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[0] ?? '' }}" autofocus>
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[1] ?? '' }}">
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[2] ?? '' }}">
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[3] ?? '' }}">
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[4] ?? '' }}">
                <input type="number" class="otp-box-single" maxlength="1" value="{{ $digits[5] ?? '' }}">
            </div>

            <button type="submit" class="btn bg-gradient-primary text-white w-100 py-3 rounded-pill control-btn" id="verifyBtn">
                <i class="fas fa-unlock-alt me-2"></i> Verify & Proceed
            </button>
        </form>

        <div class="mt-4 pt-3 position-relative" style="border-top: 1px solid #e9ecef; z-index: 2;">
            <p class="small text-muted mb-1 fw-medium">Didn't receive the code?</p>
            <form action="{{ route('otp.resend') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link p-0 small fw-bold text-decoration-none text-primary" style="transition: color 0.3s;">
                    <i class="fas fa-paper-plane me-1"></i> Resend Code
                </button>
            </form>
        </div>
        
        <div class="mt-4 position-relative" style="z-index: 2;">
            <a href="{{ route('logout') }}" class="small text-danger text-decoration-none fw-bold opacity-75" style="transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.75'" onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">
                <i class="fas fa-arrow-left me-1"></i> Cancel & Return to Login
            </a>
            <form id="cancel-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.otp-box-single');
            const hiddenInput = document.getElementById('actualOtp');
            const form = document.getElementById('otpForm');

            function updateHiddenInput() {
                let otpValue = '';
                inputs.forEach(input => {
                    otpValue += input.value;
                });
                hiddenInput.value = otpValue;
            }

            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length > 1) {
                        this.value = this.value.slice(0, 1); 
                    }
                });

                input.addEventListener('keyup', function(e) {
                    if (e.key !== 'Backspace' && this.value !== '') {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                    updateHiddenInput();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '') {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let pastedData = e.clipboardData.getData('text/plain').trim();
                    if (/^\d{6}$/.test(pastedData)) {
                        inputs.forEach((inp, i) => {
                            inp.value = pastedData[i];
                        });
                        inputs[5].focus();
                        updateHiddenInput();
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                if (hiddenInput.value.length < 6) {
                    e.preventDefault();
                    alert("Please enter the complete 6-digit verification code.");
                }
            });
        });
    </script>

    <!-- 🟢 MAGIC SCRIPT: AUTOMATIC SUBMIT KUNG MAY AUTOFILL -->
    @if(session('autofill_code'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const verifyBtn = document.getElementById('verifyBtn');
                
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Verifying...';
                verifyBtn.disabled = true;
                
                setTimeout(() => {
                    document.getElementById('otpForm').submit();
                }, 1000);
            });
        </script>
    @endif

</body>
</html>