<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
</head>
<body>
    <p>Hello,</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Your password reset code is:{{ $reset_code }}</p>
    <p>This code will expire in 2 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
