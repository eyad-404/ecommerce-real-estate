<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Changed</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>

    <p>Your password was successfully changed.</p>

    <p>If you did not perform this change, click the link below immediately to reset your password:</p>

    <p><a href="{{ $resetUrl }}">Reset your password</a></p>

    <p>Thank you,<br>EL Kayan Team</p>
</body>
</html>
