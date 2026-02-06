<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mini CRM</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --danger: #ef4444;
            --bg: #f3f4f6;
            --surface: #ffffff;
            --text: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-muted);
        }

        .login-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.875rem;
        }

        .demo-credentials {
            margin-top: 24px;
            padding: 16px;
            background: #f0f9ff;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .demo-credentials h3 {
            font-size: 0.875rem;
            margin-bottom: 8px;
            color: #0369a1;
        }

        .demo-credentials p {
            color: #0c4a6e;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Mini CRM</h1>
            <p>Sign in to access the admin panel</p>
        </div>

        <div class="login-card">
            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Sign In</button>
            </form>

            <div class="demo-credentials">
                <h3>Demo Credentials</h3>
                <p><strong>Admin:</strong> admin@example.com / password</p>
                <p><strong>Manager:</strong> manager1@example.com / password</p>
            </div>
        </div>
    </div>
</body>
</html>

