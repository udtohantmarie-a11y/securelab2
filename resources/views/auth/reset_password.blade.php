<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Create New Password | SecureLab</title>
    
    <!-- Light Theme Color -->
    <meta name="theme-color" content="#f4f7f6">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* 🟢 MODERN UI ENHANCEMENTS */
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

        /* Dashboard's Primary Blue and Success Teal */
        .orb-1 { width: 450px; height: 450px; background: #0d6efd; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 550px; height: 550px; background: #20c997; bottom: -150px; right: -100px; animation-delay: -5s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(80px, 120px) scale(1.1); }
            100% { transform: translate(-60px, 60px) scale(0.9); }
        }

        /* 🟢 MAIN CARD */
        .reset-card { 
            background: #fff;
            border: none;
            padding: 50px 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
            max-width: 480px; 
            width: 90%; 
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

        /* Forms & Inputs */
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }
        .form-label { font-weight: 700; color: #4e73df; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-left: 5px; }
        
        .form-control { 
            border-radius: 12px; 
            padding: 14px 18px; 
            border: 1px solid #e9ecef; 
            background-color: #f8f9fa;
            transition: 0.3s; 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .form-control:focus { 
            border-color: #0d6efd; 
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); 
            background-color: #fff; 
        }

        /* Custom Password Group */
        .pwd-group { 
            background-color: #f8f9fa; 
            border: 1px solid #e9ecef; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            transition: all 0.3s; 
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
            z-index: 2;
        }
        .pwd-group:focus-within { 
            background-color: white; 
            border-color: #0d6efd; 
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }
        .pwd-group input { border: none; background: transparent; padding: 14px 18px; flex-grow: 1; box-shadow: none !important; outline: none; width: 100%; font-weight: 600; color: #212529; }
        .pwd-toggle { padding: 0 18px; cursor: pointer; color: #adb5bd; transition: color 0.3s; display: flex; align-items: center; justify-content: center; height: 100%; }
        .pwd-toggle:hover { color: #0d6efd; }

        .otp-input { 
            letter-spacing: 12px; 
            font-size: 22px; 
            font-weight: 800; 
            text-align: center; 
            position: relative;
            z-index: 2;
            color: #0d6efd;
        }
        .otp-input::placeholder { color: #ced4da; font-weight: 600; }

        /* Buttons */
        .control-btn { transition: all 0.3s ease; font-weight: 700; letter-spacing: 0.5px; position: relative; z-index: 2; }
        .control-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2); }

        /* Cancel Link */
        .cancel-link { position: relative; z-index: 2; transition: opacity 0.3s; opacity: 0.75; }
        .cancel-link:hover { opacity: 1; }

        @media (max-width: 576px) {
            .reset-card { padding: 40px 20px; width: 95%; border-radius: 20px; }
            .otp-input { letter-spacing: 8px; font-size: 20px; }
            .form-control, .pwd-group input { padding: 12px 15px; }
            .stat-icon-wrapper { width: 65px; height: 65px; font-size: 28px; }
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
    <div class="reset-card">
        <i class="fas fa-lock watermark-icon"></i>
        
        <div class="text-center mb-4">
            <div class="stat-icon-wrapper bg-gradient-primary">
                <i class="fas fa-key text-white"></i>
            </div>
            <h3 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Secure Your Account</h3>
            <p class="text-muted small fw-medium">We sent a verification code to <strong class="text-primary">{{ session('reset_email') }}</strong>. Enter it below along with your new password.</p>
        </div>

        @if(session('success'))
            <div class="alert small py-2 rounded-3 fw-bold border-0 text-success text-center shadow-sm" style="background-color: #e7f6ea; position: relative; z-index: 2;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert small py-2 rounded-3 fw-bold border-0 text-danger text-center shadow-sm" style="background-color: #ffe3e3; position: relative; z-index: 2;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label text-muted">6-Digit Code</label>
                <input type="text" name="otp" class="form-control otp-input" placeholder="000000" maxlength="6" autocomplete="off" required autofocus>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted">New Password</label>
                <div class="pwd-group">
                    <input type="password" name="password" id="new_password" placeholder="Minimum 8 characters" required>
                    <span class="pwd-toggle" data-target="new_password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted">Confirm Password</label>
                <div class="pwd-group">
                    <input type="password" name="password_confirmation" id="confirm_password" placeholder="Retype new password" required>
                    <span class="pwd-toggle" data-target="confirm_password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="btn bg-gradient-primary text-white w-100 py-3 rounded-pill control-btn">
                <i class="fas fa-save me-2"></i> Update Password
            </button>
        </form>

        <div class="mt-4 pt-2 text-center">
            <a href="{{ route('index') }}" class="small text-danger text-decoration-none fw-bold cancel-link">
                <i class="fas fa-arrow-left me-1"></i> Cancel & Return to Login
            </a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.pwd-toggle').forEach(toggleBtn => {
            toggleBtn.addEventListener('click', function() {
                let targetId = this.getAttribute('data-target');
                let inputField = document.getElementById(targetId);
                let icon = this.querySelector('i');

                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    icon.classList.add('text-primary');
                } else {
                    inputField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.remove('text-primary');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>