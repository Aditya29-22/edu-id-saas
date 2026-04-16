<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EDU-ID SaaS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #fefefe 0%, #fdf8f0 30%, #fef3e2 60%, #fffbf5 100%);
            position: relative;
            overflow: hidden;
        }

        /* Left decorative panel */
        .login-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(145deg, #1a1a2e, #16213e);
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15), transparent 60%);
            top: -100px;
            right: -100px;
            border-radius: 50%;
            animation: float-slow 12s infinite alternate;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1), transparent 60%);
            bottom: -80px;
            left: -60px;
            border-radius: 50%;
            animation: float-slow 8s infinite alternate-reverse;
        }

        @keyframes float-slow {
            from { transform: scale(1) translateY(0); }
            to { transform: scale(1.15) translateY(-20px); }
        }

        .left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 40px;
            max-width: 500px;
        }

        .left-content .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #d4af37, #f0d060, #d4af37);
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #1a1a2e;
            margin-bottom: 28px;
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.3);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { box-shadow: 0 12px 40px rgba(212, 175, 55, 0.3); }
            50% { box-shadow: 0 16px 50px rgba(212, 175, 55, 0.5); }
        }

        .left-content h1 {
            font-size: 36px;
            font-weight: 800;
            color: white;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }

        .left-content h1 span {
            background: linear-gradient(135deg, #d4af37, #f0d060);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-content p {
            color: rgba(255,255,255,0.55);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .feature-list {
            list-style: none;
            text-align: left;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            font-weight: 500;
        }

        .feature-list li .feat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(212, 175, 55, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* Right login panel */
        .login-right {
            width: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            margin-bottom: 32px;
        }

        .login-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #1a1a2e;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #8b8fa3;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #4a4d65;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #b8bbd0;
            font-size: 15px;
            transition: color 0.2s;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #ffffff;
            border: 2px solid #eef0f6;
            border-radius: 14px;
            color: #1a1a2e;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.25s;
        }

        .input-wrap input:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }

        .input-wrap input:focus ~ i,
        .input-wrap input:focus + i {
            color: #d4af37;
        }

        .input-wrap input::placeholder {
            color: #c1c4d6;
        }

        .error-text {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #d4af37, #c5a028);
            color: #1a1a2e;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.25);
            margin-top: 8px;
            letter-spacing: 0.2px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, #e0bc42, #d4af37);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .demo-creds {
            margin-top: 24px;
            padding: 16px;
            background: #fdf8f0;
            border: 1px solid #f0e6d0;
            border-radius: 12px;
            text-align: center;
        }

        .demo-creds p {
            font-size: 10px;
            color: #a09070;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .demo-creds code {
            font-size: 12px;
            color: #8b6914;
            background: rgba(212, 175, 55, 0.1);
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-family: inherit;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eef0f6;
        }

        .divider span {
            font-size: 11px;
            color: #b8bbd0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 900px) {
            .login-left { display: none; }
            body { justify-content: center; }
            .login-right { width: 100%; }
        }

        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .role-option {
            background: #ffffff;
            border: 2px solid #eef0f6;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
        }

        .role-option i {
            display: block;
            font-size: 18px;
            color: #b8bbd0;
            margin-bottom: 6px;
            transition: all 0.2s;
        }

        .role-option span {
            font-size: 11px;
            font-weight: 700;
            color: #8b8fa3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-option:hover {
            border-color: #f0e6d0;
            background: #fdf8f0;
        }

        .role-option.active {
            border-color: #d4af37;
            background: rgba(212, 175, 55, 0.05);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1);
        }

        .role-option.active i {
            color: #d4af37;
            transform: scale(1.1);
        }

        .role-option.active span {
            color: #1a1a2e;
        }
    </style>
</head>
<body>
    <!-- Left Panel -->
    <div class="login-left">
        <div class="left-content">
            <div class="brand-logo">
                <i class="fas fa-id-card"></i>
            </div>
            <h1>Welcome to <span>EDU-ID</span></h1>
            <p>The most powerful school identity & attendance management platform. Streamline your operations today.</p>

            <ul class="feature-list">
                <li>
                    <div class="feat-icon"><i class="fas fa-fingerprint"></i></div>
                    Smart QR-based Attendance Tracking
                </li>
                <li>
                    <div class="feat-icon"><i class="fas fa-id-badge"></i></div>
                    Digital ID Card Generation
                </li>
                <li>
                    <div class="feat-icon"><i class="fas fa-building-columns"></i></div>
                    Multi-School Tenant Management
                </li>
                <li>
                    <div class="feat-icon"><i class="fas fa-chart-line"></i></div>
                    Real-time Analytics Dashboard
                </li>
            </ul>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="login-right">
        <div class="login-container">
            <div class="login-header">
                <h2>Sign In</h2>
                <p>Enter your credentials to access the dashboard</p>
            </div>

            <div class="role-selector">
                <div class="role-option active" data-role="super_admin" onclick="selectRole('super_admin')">
                    <i class="fas fa-crown"></i>
                    <span>Super Admin</span>
                </div>
                <div class="role-option" data-role="school_admin" onclick="selectRole('school_admin')">
                    <i class="fas fa-building-columns"></i>
                    <span>School admin</span>
                </div>
                <div class="role-option" data-role="student" onclick="selectRole('student')">
                    <i class="fas fa-user-graduate"></i>
                    <span>Student</span>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <input type="hidden" name="role" id="selectedRole" value="super_admin">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="admin@eduid.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    Sign In <i class="fas fa-arrow-right" style="margin-left:6px;"></i>
                </button>
            </form>

            <div class="demo-creds">
                <p>Demo Credentials</p>
                <div style="margin-top:6px;">
                    <code id="demoEmail">admin@eduid.com</code> &nbsp;/&nbsp; <code id="demoPass">Admin@123</code>
                </div>
            </div>
        </div>
    </div>
    <script>
        function selectRole(role) {
            // Update UI
            document.querySelectorAll('.role-option').forEach(opt => {
                opt.classList.remove('active');
            });
            document.querySelector(`.role-option[data-role="${role}"]`).classList.add('active');
            
            // Update hidden input
            document.getElementById('selectedRole').value = role;
            
            // Update placeholders or demo creds based on role
            const emailInput = document.querySelector('input[name="email"]');
            const demoEmail = document.getElementById('demoEmail');
            const demoPass = document.getElementById('demoPass');

            if (role === 'super_admin') {
                emailInput.placeholder = 'admin@eduid.com';
                demoEmail.innerText = 'admin@eduid.com';
                demoPass.innerText = 'Admin@123';
            } else if (role === 'school_admin') {
                emailInput.placeholder = 'school@example.com';
                demoEmail.innerText = 'school@eduid.com';
                demoPass.innerText = 'School@123';
            } else {
                emailInput.placeholder = 'student@example.com';
                demoEmail.innerText = 'student@eduid.com';
                demoPass.innerText = 'Student@123';
            }
        }
    </script>
</body>
</html>
