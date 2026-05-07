<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation mot de passe</title>
</head>
<body>
    <h1>Réinitialisation de votre mot de passe</h1>
    <p>Vous recevez cet email car vous avez demandé la réinitialisation de votre mot de passe.</p>
    <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}">Réinitialiser mon mot de passe</a>
    <p>Ce lien expire dans 60 minutes.</p>
    <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
    <p>Merci,<br>{{ config('app.name') }}</p>
</body>
</html>