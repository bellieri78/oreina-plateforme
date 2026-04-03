<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Extranet - OREINA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --forest: #16302B;
            --blue: #356B8A;
            --sage: #85B79D;
            --gold: #EDC442;
            --bg: #F4F1ED;
            --surface: #FFFFFF;
            --surface-blue: #EEF4F8;
            --text: #1C2B27;
            --muted: #68756F;
            --border: rgba(22,48,43,0.10);
            --shadow: 0 14px 32px rgba(22,48,43,0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: var(--forest);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 0% 100%, rgba(133,183,157,0.15), transparent 40%),
                radial-gradient(circle at 100% 0%, rgba(53,107,138,0.12), transparent 40%);
        }
        .login-card {
            position: relative;
            width: 100%;
            max-width: 440px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--surface-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            color: var(--blue);
        }
        .login-header h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin: 0;
            line-height: 1.1;
        }
        .login-header p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }
        .login-form {
            display: grid;
            gap: 20px;
        }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }
        .field input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            font-size: 15px;
            font-family: inherit;
            color: var(--text);
            transition: 0.2s ease;
        }
        .field input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(53,107,138,0.12);
        }
        .error-msg {
            padding: 12px 14px;
            background: rgba(239,68,68,0.06);
            border: 1px solid rgba(239,68,68,0.15);
            color: #dc2626;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }
        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--blue);
        }
        .btn-login {
            width: 100%;
            height: 46px;
            border-radius: 14px;
            border: 0;
            background: var(--blue);
            color: white;
            font-size: 15px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.2s ease;
            box-shadow: 0 12px 24px rgba(53,107,138,0.2);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(53,107,138,0.3);
        }
        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 13px;
        }
        .login-footer a {
            color: var(--blue);
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i data-lucide="shield" style="width:28px;height:28px;"></i>
            </div>
            <h1>Extranet OREINA</h1>
            <p>Espace d'administration réservé aux éditeurs et administrateurs.</p>
        </div>

        @if($errors->any())
            <div class="error-msg" style="margin-bottom:20px;">
                <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
            @csrf

            <div class="field">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="votre@email.fr">
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <label class="remember">
                <input type="checkbox" name="remember">
                Se souvenir de moi
            </label>

            <button type="submit" class="btn-login">
                <i data-lucide="log-in" style="width:18px;height:18px;"></i>
                Se connecter
            </button>
        </form>

        <div class="login-footer">
            <p>Vous êtes adhérent ? <a href="{{ route('hub.login') }}">Connexion espace membre</a></p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
