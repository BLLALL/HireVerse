<p>Hello {{ $name }},</p>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p>Your password reset token is: <strong>{{ $token }}</strong></p>

<p>This password reset token will expire in 60 minutes.</p>

<p>If you did not request a password reset, no further action is required.</p>

<p>Regards,<br>{{ env('APP_NAME')}}</p>