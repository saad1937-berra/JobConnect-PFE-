<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notification JobConnect</title>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#0a0a0a;color:#fff;padding:18px 22px;border-radius:8px 8px 0 0;">
            <div style="font-size:22px;font-weight:800;">Job<span style="color:#facc15;">Connect</span></div>
        </div>

        <div style="background:#ffffff;border:1px solid #e5e7eb;border-top:0;padding:24px 22px;border-radius:0 0 8px 8px;">
            <p style="margin:0 0 14px;font-size:15px;">Bonjour {{ $utilisateur->prenom }} {{ $utilisateur->nom }},</p>

            <p style="margin:0 0 18px;line-height:1.65;font-size:15px;">
                Vous avez une nouvelle notification sur JobConnect.
            </p>

            <div style="border-left:4px solid #facc15;background:#fffbeb;padding:14px 16px;margin:18px 0;">
                <div style="font-size:12px;text-transform:uppercase;font-weight:800;color:#92400e;margin-bottom:6px;">
                    {{ $type }}
                </div>
                <div style="font-size:15px;line-height:1.6;">
                    {{ $notificationMessage }}
                </div>
            </div>

            <p style="margin:18px 0 0;line-height:1.6;font-size:14px;color:#4b5563;">
                Connectez-vous a votre espace pour voir les details et continuer vos actions.
            </p>
        </div>
    </div>
</body>
</html>
