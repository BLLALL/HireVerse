<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container {
            text-align: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            text-decoration: none;
            color: white !important;
            background-color: black;
            border-radius: 5px;
            margin-top: 20px;
        }
        
    </style>
</head>
<body>
    <div class="email-container">
        <img src="{{ $logo }}" class="logo" alt="Logo">

        <h2>Welcome to {{ $appName }}!</h2>
        <p>Thank you for signing up. Please verify your email by clicking the button below.</p>

        <a href="{{ $verificationUrl }}" class="btn">Verify Email</a>

        <p>If you did not create an account, no further action is required.</p>

        <p>Best Regards, <br>{{ $appName }}</p>
    </div>
</body>
</html>
