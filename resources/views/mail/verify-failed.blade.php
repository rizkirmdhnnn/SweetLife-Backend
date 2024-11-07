<!-- resources/views/emails/verify-error.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
        <h1 style="color: #e74c3c;">Verification Failed</h1>

        <p>We attempted to verify your email address, but unfortunately, the verification was unsuccessful. This could be due to:</p>

        <ul>
            <li>The verification link has expired.</li>
            <li>The verification link has already been used.</li>
            <li>There may have been an issue with the link provided.</li>
        </ul>

        <p>Please try requesting a new verification email if you believe this is an error.</p>

        <p>If you need further assistance, feel free to contact our support team.</p>

        <p>Best regards,<br>Your Company Team</p>
    </div>
</body>

</html>