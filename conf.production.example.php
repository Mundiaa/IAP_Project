<?php
/**
 * Production Configuration Template
 * 
 * IMPORTANT: 
 * 1. Copy this file to conf.php
 * 2. Fill in all the values with your production settings
 * 3. Never commit conf.php with real credentials to version control
 * 4. Set file permissions to 600 (chmod 600 conf.php)
 */

$conf = [
    
    // Site Information
    'site_name' => 'Notez Wiz',
    'site_url' => 'https://yourdomain.com', // Your actual domain name
    'admin_email' => 'admin@yourdomain.com', // Your admin email
    
    // Database Configuration
    // For shared hosting, usually 'localhost'
    // For remote databases, use the host provided by your hosting company
    'db_host' => 'localhost', // or 'db.example.com' for remote
    'db_port' => 3306, // Usually 3306, check with your hosting provider
    'db_user' => 'your_database_username', // Database username from hosting
    'db_pass' => 'your_strong_database_password', // Database password
    'db_name' => 'notez_wiz', // Database name
    
    // Site Language
    'site_lang' => 'en',
    
    // SMTP Configuration
    // Option 1: Gmail (for testing, limited to 500 emails/day)
    'mail_type' => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_user' => 'your-email@gmail.com',
    'smtp_pass' => 'your-gmail-app-password', // Gmail App Password (16 characters)
    'smtp_port' => 465,
    'smtp_secure' => 'ssl',
    
    // Option 2: SendGrid (recommended for production)
    // 'mail_type' => 'smtp',
    // 'smtp_host' => 'smtp.sendgrid.net',
    // 'smtp_user' => 'apikey',
    // 'smtp_pass' => 'your-sendgrid-api-key',
    // 'smtp_port' => 587,
    // 'smtp_secure' => 'tls',
    
    // Option 3: Mailgun
    // 'mail_type' => 'smtp',
    // 'smtp_host' => 'smtp.mailgun.org',
    // 'smtp_user' => 'your-mailgun-username',
    // 'smtp_pass' => 'your-mailgun-password',
    // 'smtp_port' => 587,
    // 'smtp_secure' => 'tls',
];

// Production Error Handling
// Uncomment these lines for production
// error_reporting(0);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/logs/error.log');

?>

