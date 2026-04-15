<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Vérification de votre adresse email</title>
</head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#16302B;background:#f8f8f6;margin:0;padding:24px;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e5e5e5;border-radius:8px;padding:32px;">
        <h1 style="font-size:22px;margin:0 0 16px 0;color:#2C5F2D;">Bienvenue sur {{ config('journal.name') }}</h1>

        <p>Bonjour {{ $user->name }},</p>

        <p>Merci de votre inscription sur la plateforme Oreina. Pour finaliser la création de votre compte et accéder à toutes les fonctionnalités (notamment la soumission d'articles à la revue {{ config('journal.name') }}), merci de cliquer sur le lien ci-dessous :</p>

        <p style="text-align:center;margin:32px 0;">
            <a href="{{ $verifyUrl }}" style="display:inline-block;background:#2C5F2D;color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none;font-weight:600;">
                Vérifier mon adresse email
            </a>
        </p>

        <p style="font-size:13px;color:#666;">Ce lien expire dans 60 minutes. Si vous n'avez pas créé de compte, ignorez ce message.</p>

        <p style="font-size:13px;color:#666;word-break:break-all;">Si le bouton ne fonctionne pas, copiez-collez cette URL dans votre navigateur :<br>{{ $verifyUrl }}</p>
    </div>
</body>
</html>
