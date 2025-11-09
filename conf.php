<?php
// Site Information
$conf['site_name'] = 'Notez Wiz';
$conf['site_url'] = 'http://localhost'; //your site URL
$conf['admin_email'] = ''; //your admin email

$conf = [
    
    // Site Information
    'site_name' => 'Notez Wiz',
    'site_url' => 'http://localhost/project/IAP_Project', //File location for the project
    'admin_email' => '', //your admin email

    // Database Configuration
    'db_host' => 'localhost',
    'db_port' => 3306, //your MariaDB port
    'db_user' => 'root',
    'db_pass' => '', //your MariaDB password
    'db_name' => 'notez_wiz', //your database name

    // SMTP Configuration
    'mail_type' => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_user' => '', //your SMTP email
    'smtp_pass' => '', // Gmail app password
    'smtp_port' => 465,
    'smtp_secure' => 'ssl'
];
?>
