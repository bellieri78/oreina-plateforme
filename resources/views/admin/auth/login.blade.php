<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - OREINA Admin</title>
    @vite(['resources/css/admin.css'])
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #356B8A 0%, #2d5a75 100%);
        }
        .login-card {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        .login-logo img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
        }
        .login-logo span {
            font-size: 1.5rem;
            font-weight: 700;
            color: #356B8A;
        }
        .login-title {
            text-align: center;
            font-size: 1.25rem;
            color: #374151;
            margin-bottom: 1.5rem;
        }
        .login-form .form-group {
            margin-bottom: 1.25rem;
        }
        .login-form .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .login-form .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .login-form .form-input:focus {
            outline: none;
            border-color: #356B8A;
            box-shadow: 0 0 0 3px rgba(53, 107, 138, 0.1);
        }
        .login-form .btn-login {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #356B8A;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .login-form .btn-login:hover {
            background: #2d5a75;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-me input {
            width: 1rem;
            height: 1rem;
        }
        .remember-me label {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .error-message {
            background: rgba(232, 93, 117, 0.1);
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="OREINA" onerror="this.style.display='none'">
                <span>OREINA</span>
            </div>
            <h1 class="login-title">Connexion Extranet</h1>

            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
