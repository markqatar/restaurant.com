<?php
// SMTP configuration (override via environment variables if set)
return [
    'host'       => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'port'       => getenv('SMTP_PORT') ?: 587,
    'encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls', // tls or ssl
    'username'   => getenv('SMTP_USERNAME') ?: 'fornaciarimarcello@gmail.com',
    'password'   => getenv('SMTP_PASSWORD') ?: 'lepu xeiz gdxy ynyw', // Use Gmail App Password
    'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'fornaciarimarcello@gmail.com',
    'from_name'  => getenv('SMTP_FROM_NAME') ?: 'Purchasing Dept',
];
