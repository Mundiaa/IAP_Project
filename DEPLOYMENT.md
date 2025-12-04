# Deployment Guide - Notez Wiz

This guide will help you deploy Notez Wiz to the internet for public access.

## 🌐 Hosting Options

### 1. Shared Web Hosting (Easiest - Recommended for Beginners)
**Best for**: Small to medium projects, low traffic
- **Providers**: Bluehost, Hostinger, SiteGround, Namecheap
- **Cost**: $3-10/month
- **Pros**: Easy setup, managed services, includes cPanel
- **Cons**: Limited control, shared resources

### 2. VPS (Virtual Private Server)
**Best for**: More control, better performance
- **Providers**: DigitalOcean, Linode, Vultr, AWS EC2
- **Cost**: $5-20/month
- **Pros**: Full control, scalable, better performance
- **Cons**: Requires server management knowledge

### 3. Cloud Platforms
**Best for**: Scalable applications
- **Providers**: AWS, Google Cloud, Azure, Heroku
- **Cost**: Pay-as-you-go or free tier available
- **Pros**: Highly scalable, managed services
- **Cons**: More complex setup, can be expensive at scale

### 4. Free Hosting Options (For Testing)
- **000webhost**: Free PHP hosting
- **InfinityFree**: Free hosting with MySQL
- **Heroku**: Free tier (limited)
- **Note**: Free hosting has limitations and may not be suitable for production

## 📋 Pre-Deployment Checklist

Before deploying, ensure you have:

- [ ] Domain name (optional but recommended)
- [ ] Web hosting account with PHP 7.4+ and MySQL support
- [ ] FTP/SFTP access or SSH access to your server
- [ ] Database credentials from your hosting provider
- [ ] SMTP email credentials (Gmail or other email service)
- [ ] Backup of your local project

## 🚀 Deployment Steps

### Option A: Shared Web Hosting (cPanel)

#### Step 1: Prepare Your Files

1. **Remove sensitive files** (if any):
   ```bash
   # Remove test files
   rm -rf test/
   ```

2. **Create a production config file**:
   - Copy `conf.php` to `conf.production.php`
   - Update with production credentials

#### Step 2: Upload Files via FTP

1. **Connect via FTP**:
   - Use FileZilla, WinSCP, or cPanel File Manager
   - Host: `ftp.yourdomain.com` or IP address
   - Username/Password: Provided by hosting company

2. **Upload project files**:
   - Upload all files to `public_html/` or `www/` directory
   - Maintain folder structure
   - Upload `vendor/` folder (or run `composer install` on server)

#### Step 3: Set Up Database

1. **Create database via cPanel**:
   - Login to cPanel
   - Go to "MySQL Databases"
   - Create new database: `notez_wiz`
   - Create database user
   - Grant all privileges to user

2. **Import database structure**:
   - Go to phpMyAdmin in cPanel
   - Select your database
   - Click "Import"
   - Run the SQL scripts (see Database Setup section in README)

#### Step 4: Configure Application

1. **Update `conf.php`** with production values:
   ```php
   <?php
   $conf = [
       'site_name' => 'Notez Wiz',
       'site_url' => 'https://yourdomain.com', // Your actual domain
       'admin_email' => 'admin@yourdomain.com',
       
       'db_host' => 'localhost', // Usually 'localhost' on shared hosting
       'db_port' => 3306,
       'db_user' => 'your_db_username', // From cPanel
       'db_pass' => 'your_db_password', // From cPanel
       'db_name' => 'your_db_name', // From cPanel
       
       'smtp_host' => 'smtp.gmail.com',
       'smtp_user' => 'your-email@gmail.com',
       'smtp_pass' => 'your-app-password',
       'smtp_port' => 465,
       'smtp_secure' => 'ssl'
   ];
   ?>
   ```

2. **Set proper file permissions**:
   - Folders: 755
   - Files: 644
   - `conf.php`: 600 (more secure)

#### Step 5: Install Dependencies

If Composer is available on your hosting:

```bash
# Via SSH (if available)
cd public_html
composer install --no-dev --optimize-autoloader
```

Or upload the `vendor/` folder from your local machine.

#### Step 6: Test and Verify

1. Visit `https://yourdomain.com`
2. Test registration
3. Test login
4. Create a test note
5. Check email functionality

---

### Option B: VPS Deployment (DigitalOcean, Linode, etc.)

#### Step 1: Set Up Server

1. **Create VPS instance**:
   - Choose Ubuntu 20.04/22.04 LTS
   - Minimum: 1GB RAM, 1 CPU
   - Select your region

2. **Initial server setup**:
   ```bash
   # SSH into server
   ssh root@your-server-ip
   
   # Update system
   apt update && apt upgrade -y
   
   # Install LAMP stack
   apt install apache2 mysql-server php php-mysql php-mbstring php-xml composer -y
   ```

#### Step 2: Configure Apache

1. **Enable required modules**:
   ```bash
   a2enmod rewrite
   a2enmod ssl
   systemctl restart apache2
   ```

2. **Create virtual host**:
   ```bash
   nano /etc/apache2/sites-available/notez-wiz.conf
   ```

   Add:
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       ServerAlias www.yourdomain.com
       DocumentRoot /var/www/notez-wiz
       
       <Directory /var/www/notez-wiz>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/notez-wiz-error.log
       CustomLog ${APACHE_LOG_DIR}/notez-wiz-access.log combined
   </VirtualHost>
   ```

3. **Enable site**:
   ```bash
   a2ensite notez-wiz.conf
   systemctl reload apache2
   ```

#### Step 3: Set Up SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
apt install certbot python3-certbot-apache -y

# Get SSL certificate
certbot --apache -d yourdomain.com -d www.yourdomain.com
```

#### Step 4: Deploy Application

1. **Clone or upload project**:
   ```bash
   cd /var/www
   # Option 1: Upload via SCP
   scp -r IAP_Project/* user@server:/var/www/notez-wiz/
   
   # Option 2: Clone from Git (if using version control)
   git clone your-repo-url notez-wiz
   ```

2. **Set permissions**:
   ```bash
   chown -R www-data:www-data /var/www/notez-wiz
   chmod -R 755 /var/www/notez-wiz
   chmod 600 /var/www/notez-wiz/conf.php
   ```

3. **Install dependencies**:
   ```bash
   cd /var/www/notez-wiz
   composer install --no-dev --optimize-autoloader
   ```

#### Step 5: Configure MySQL

```bash
# Secure MySQL installation
mysql_secure_installation

# Create database
mysql -u root -p
```

```sql
CREATE DATABASE notez_wiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'notez_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON notez_wiz.* TO 'notez_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Step 6: Configure Application

Update `conf.php` with production settings (see Option A, Step 4).

---

### Option C: Cloud Platform (Heroku Example)

#### Step 1: Prepare for Heroku

1. **Create `Procfile`**:
   ```
   web: vendor/bin/heroku-php-apache2
   ```

2. **Create `composer.json`** (already exists, verify):
   ```json
   {
       "require": {
           "php": "^7.4",
           "phpmailer/phpmailer": "^6.10"
       }
   }
   ```

#### Step 2: Deploy to Heroku

```bash
# Install Heroku CLI
# Login
heroku login

# Create app
heroku create notez-wiz

# Add MySQL addon (JawsDB or ClearDB)
heroku addons:create jawsdb:kitefree

# Set environment variables
heroku config:set SITE_URL=https://notez-wiz.herokuapp.com

# Deploy
git push heroku main
```

#### Step 3: Configure Database

```bash
# Get database URL
heroku config:get JAWSDB_URL

# Update conf.php to use environment variables
```

---

## 🔒 Security Hardening for Production

### 1. Update Configuration

1. **Disable error display**:
   ```php
   // In conf.php or create a separate production config
   error_reporting(0);
   ini_set('display_errors', 0);
   ini_set('log_errors', 1);
   ini_set('error_log', '/path/to/error.log');
   ```

2. **Use environment variables** (recommended):
   ```php
   // Instead of hardcoding in conf.php
   $conf['db_pass'] = getenv('DB_PASSWORD');
   $conf['smtp_pass'] = getenv('SMTP_PASSWORD');
   ```

### 2. File Security

1. **Protect sensitive files**:
   - Move `conf.php` outside web root if possible
   - Or add `.htaccess` protection:
   ```apache
   <Files "conf.php">
       Order allow,deny
       Deny from all
   </Files>
   ```

2. **Create `.htaccess`** in root:
   ```apache
   # Prevent directory listing
   Options -Indexes

   # Protect sensitive files
   <FilesMatch "^(conf\.php|composer\.(json|lock))$">
       Order allow,deny
       Deny from all
   </FilesMatch>

   # Enable HTTPS redirect
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 3. Database Security

- Use strong database passwords
- Limit database user privileges
- Use SSL for database connections (if available)
- Regular database backups

### 4. PHP Security

- Keep PHP updated
- Disable dangerous functions in `php.ini`:
  ```ini
  disable_functions = exec,passthru,shell_exec,system
  ```

### 5. Session Security

Update session configuration:
```php
// In a bootstrap file or conf.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS only
ini_set('session.use_only_cookies', 1);
```

---

## 🌍 Domain Configuration

### If You Have a Domain Name

1. **Point domain to your server**:
   - Get nameservers from your hosting provider
   - Update DNS records at your domain registrar
   - A Record: `@` → Your server IP
   - CNAME: `www` → `yourdomain.com`

2. **Wait for DNS propagation** (24-48 hours)

3. **Update `conf.php`**:
   ```php
   'site_url' => 'https://yourdomain.com',
   ```

---

## 📧 Email Configuration for Production

### Option 1: Gmail SMTP (For Testing)

- Use Gmail App Password
- Limited to 500 emails/day

### Option 2: Professional Email Service (Recommended)

**SendGrid**:
```php
'smtp_host' => 'smtp.sendgrid.net',
'smtp_user' => 'apikey',
'smtp_pass' => 'your-sendgrid-api-key',
'smtp_port' => 587,
'smtp_secure' => 'tls'
```

**Mailgun**:
```php
'smtp_host' => 'smtp.mailgun.org',
'smtp_user' => 'your-mailgun-username',
'smtp_pass' => 'your-mailgun-password',
'smtp_port' => 587,
'smtp_secure' => 'tls'
```

---

## ✅ Post-Deployment Checklist

- [ ] Application accessible via domain
- [ ] HTTPS/SSL certificate installed
- [ ] Database connection working
- [ ] User registration working
- [ ] Login functionality working
- [ ] Email sending working
- [ ] Notes CRUD operations working
- [ ] Analytics tracking working
- [ ] Error logging configured
- [ ] Backups configured
- [ ] Security measures in place
- [ ] Performance optimized

---

## 🔄 Updating Your Application

### For Shared Hosting:
1. Upload new files via FTP
2. Replace old files
3. Clear browser cache

### For VPS:
```bash
# Via Git
cd /var/www/notez-wiz
git pull origin main
composer install --no-dev --optimize-autoloader

# Or via SCP
scp -r updated-files/* user@server:/var/www/notez-wiz/
```

---

## 🐛 Troubleshooting Deployment Issues

### Issue: 500 Internal Server Error
**Solutions**:
- Check Apache/PHP error logs
- Verify file permissions
- Check `.htaccess` syntax
- Verify PHP version compatibility

### Issue: Database Connection Failed
**Solutions**:
- Verify database credentials
- Check if database exists
- Verify database user has proper permissions
- Check firewall rules

### Issue: Email Not Sending
**Solutions**:
- Verify SMTP credentials
- Check firewall allows SMTP ports
- Test SMTP connection
- Check spam folder

### Issue: CSS/JS Not Loading
**Solutions**:
- Check file paths (use absolute URLs)
- Verify file permissions
- Clear browser cache
- Check for mixed content (HTTP/HTTPS)

---

## 📊 Monitoring and Maintenance

### Regular Tasks:
1. **Backup database** (weekly)
2. **Update PHP and dependencies** (monthly)
3. **Monitor error logs** (daily)
4. **Check disk space** (weekly)
5. **Review security logs** (weekly)

### Backup Script Example:
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u user -p database_name > backup_$DATE.sql
tar -czf backup_$DATE.tar.gz /var/www/notez-wiz
```

---

## 🆘 Getting Help

If you encounter issues:
1. Check error logs
2. Review this guide
3. Check hosting provider documentation
4. Contact hosting support
5. Review PHP/Apache documentation

---

**Last Updated**: 2024

