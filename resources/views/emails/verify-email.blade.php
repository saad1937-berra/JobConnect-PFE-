<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Verification email JobConnect</title>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#0a0a0a;color:#fff;padding:18px 22px;border-radius:8px 8px 0 0;">
            <div style="font-size:22px;font-weight:800;">Job<span style="color:#facc15;">Connect</span></div>
        </div>

        <div style="background:#ffffff;border:1px solid #e5e7eb;border-top:0;padding:24px 22px;border-radius:0 0 8px 8px;">
            <p style="margin:0 0 14px;font-size:15px;">Bonjour {{ $utilisateur->prenom }} {{ $utilisateur->nom }},</p>

            <p style="margin:0 0 18px;line-height:1.65;font-size:15px;">
                Confirmez votre adresse email pour activer votre profil JobConnect.
            </p>

            <p style="margin:22px 0;">
                <a href="{{ $verificationUrl }}" style="display:inline-block;background:#facc15;color:#111827;text-decoration:none;font-weight:800;padding:12px 18px;border-radius:8px;">
                    Confirmer mon email
                </a>
            </p>

            <p style="margin:18px 0 0;line-height:1.6;font-size:14px;color:#4b5563;">
                Ce lien expire dans 60 minutes. Si vous n'avez pas cree de compte JobConnect, ignorez cet email.
            </p>
        </div>
    </div>
</body>
</html>
