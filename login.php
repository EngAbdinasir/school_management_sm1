<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
             if ($password === $user['password_hash']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_role'] = $user['role_id'] ?? 'user';
                $_SESSION['user_name'] = $user['username'] ?? 'User';
                header("Location: index.php");
                exit;
             } else {
                 $error = "User name or Password is Incorrect.";
             }
        } else {
            $error = "User not found.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
            z-index: 0;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Floating Shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 15s infinite ease-in-out;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            bottom: -100px;
            right: -100px;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 2.5rem;
            color: #1e293b;
            text-decoration: none;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
        }

        .brand-logo span {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-title {
            text-align: center;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .welcome-subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            outline: none;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
        }

        .form-control.has-icon {
            padding-left: 3rem;
        }

        .btn-primary {
            padding: 0.875rem;
            font-weight: 600;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 2px solid #fecaca;
            color: #dc2626;
            border-radius: 12px;
            font-size: 0.9rem;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .form-check-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #764ba2;
        }

        .signup-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link:hover {
            color: #764ba2;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider::before {
            margin-right: 0.5rem;
        }

        .divider::after {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo">
                <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <span>Al Imra School</span>
            </div>
            
            <h1 class="welcome-title">Welcome Back</h1>
            <p class="welcome-subtitle">Sign in to access your dashboard</p>

            <?php if ($error): ?>
                <div class="alert alert-danger-custom">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control has-icon" id="email" name="email" placeholder="Enter your email" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control has-icon" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="" id="remember">
                    <label class="form-check-label" for="remember">
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
                
                <div class="text-center text-muted small">
                    Don't have an account? <a href="#" class="signup-link">Contact Admin</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
