# Notez Wiz

A comprehensive, secure web-based note-taking application built with PHP and MySQL/MariaDB. Notez Wiz provides users with an intuitive interface to create, manage, and organize their notes, complete with user authentication, analytics tracking, and a modern dashboard experience.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Security Features](#security-features)
- [Analytics System](#analytics-system)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

Notez Wiz is a full-featured note-taking application designed for students and professionals who need a secure, organized way to manage their notes. The application features a modern, responsive user interface with dark mode support, comprehensive analytics, and robust user authentication.

### Key Highlights

- **Secure Authentication**: Password hashing, session management, and email verification
- **Intuitive Note Management**: Create, edit, delete, search, and filter notes with ease
- **Analytics Dashboard**: Track your note-taking habits with detailed charts and statistics
- **Modern UI/UX**: Responsive design with Bootstrap 4 and Font Awesome icons
- **Email Integration**: Welcome emails and password reset functionality via PHPMailer

## ✨ Features

### User Authentication
- User registration with email validation
- Secure login with password hashing (bcrypt)
- Password reset functionality via email
- Session-based authentication
- Auto-login after registration
- Welcome email notifications

### Note Management
- Create, edit, and delete notes
- Rich text content support
- Search notes by title or content
- Filter notes (All, Recent, Favorites)
- Recent notes highlighting
- Note timestamps and organization
- User-specific note isolation

### User Profile
- View and edit profile information
- Update full name and email
- Change password functionality
- Profile picture placeholder (ready for future implementation)

### Analytics & Reporting
- **Statistics Cards**: Total notes, interactions, average note length, recent activity
- **Interactive Charts**:
  - Notes created over time (Line Chart - Last 30 days)
  - Notes by day of week (Doughnut Chart)
  - Notes by hour of day (Bar Chart)
  - User interactions distribution (Horizontal Bar Chart)
  - Activity timeline (Multi-line Chart - Last 7 days)
- Real-time interaction tracking
- User-specific analytics (privacy-focused)

### User Experience
- Dark mode toggle
- Responsive sidebar navigation
- Smooth animations and transitions
- Loading indicators
- Empty state messages
- Toast notifications
- Mobile-friendly design

## 🛠 Technology Stack

### Backend
- **PHP 7.4+**: Server-side scripting
- **MySQL/MariaDB**: Relational database management
- **PHPMailer 6.10+**: Email functionality
- **PSR Log 3.0**: Logging standards

### Frontend
- **Bootstrap 4.5.2**: CSS framework
- **jQuery 3.5.1**: JavaScript library
- **Chart.js 3.9.1**: Data visualization
- **Font Awesome 6.4.0**: Icon library
- **Popper.js 2.5.2**: Tooltip positioning

### Development Tools
- **Composer**: Dependency management
- **Apache HTTP Server**: Web server

## 📦 Prerequisites

Before installing Notez Wiz, ensure you have the following installed:

- **PHP 7.4 or higher** with the following extensions:
  - `mysqli`
  - `mbstring`
  - `openssl`
  - `session`
- **MySQL 5.7+ or MariaDB 10.3+**
- **Apache HTTP Server 2.4+** (or compatible web server)
- **Composer** (for dependency management)
- **SMTP Email Account** (Gmail recommended for testing)

## 🚀 Installation

### Step 1: Clone or Download the Project

```bash
# If using Git
git clone <repository-url> IAP_Project
cd IAP_Project

# Or extract the project archive to your web server directory
# For Apache on Windows: C:\Apache24\htdocs\IAP_Project
# For Apache on Linux: /var/www/html/IAP_Project
```

### Step 2: Install Dependencies

Navigate to the project directory and install PHP dependencies using Composer:

```bash
composer install
```

This will install:
- PHPMailer (for email functionality)
- PSR Log (for logging)

### Step 3: Configure the Application

Edit the `conf.php` file with your specific configuration (see [Configuration](#configuration) section).

### Step 4: Set Up the Database

Create the database and tables (see [Database Setup](#database-setup) section).

### Step 5: Set Up Analytics (Optional)

If you want to use the analytics features, run:

```bash
php create_analytics_table.php
```

### Step 6: Configure Web Server

#### Apache Configuration

Ensure your Apache virtual host or `httpd.conf` is configured to point to the project directory:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/Apache24/htdocs/IAP_Project"
    <Directory "C:/Apache24/htdocs/IAP_Project">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Step 7: Access the Application

Open your web browser and navigate to:

```
http://localhost/IAP_Project
```

Or if using a custom port:

```
http://localhost:8081/IAP_Project
```

## ⚙️ Configuration

Edit the `conf.php` file to configure your application:

```php
<?php
$conf = [
    // Site Information
    'site_name' => 'Notez Wiz',
    'site_url' => 'http://localhost:8081/IAP_Project',
    'admin_email' => 'your-admin@email.com',
    
    // Database Configuration
    'db_host' => '127.0.0.1',
    'db_port' => 3307,        // Your MariaDB/MySQL port
    'db_user' => 'root',       // Your database username
    'db_pass' => 'your_password', // Your database password
    'db_name' => 'notez_wiz', // Your database name
    
    // Site Language
    'site_lang' => 'en',
    
    // SMTP Configuration
    'mail_type' => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_user' => 'your-email@gmail.com',
    'smtp_pass' => 'your-app-password', // Gmail App Password
    'smtp_port' => 465,
    'smtp_secure' => 'ssl'
];
?>
```

### Gmail App Password Setup

For Gmail SMTP, you need to generate an App Password:

1. Go to your Google Account settings
2. Enable 2-Step Verification
3. Go to App Passwords
4. Generate a new app password for "Mail"
5. Use this 16-character password in `conf.php`

**Important**: Never commit `conf.php` with real credentials to version control!

## 🗄 Database Setup

### Step 1: Create the Database

```sql
CREATE DATABASE notez_wiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Create the Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL DEFAULT 'New User',
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 3: Create the Notes Table

```sql
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 4: Create the Analytics Table (Optional)

Run the provided script:

```bash
php create_analytics_table.php
```

Or manually create:

```sql
CREATE TABLE user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    interaction_type VARCHAR(50) NOT NULL,
    interaction_details TEXT,
    page_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_interaction_type (interaction_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 📖 Usage

### Getting Started

1. **Register a New Account**
   - Navigate to the homepage
   - Click "Sign Up"
   - Enter your email and password
   - Confirm your password
   - Submit the form
   - You'll receive a welcome email and be automatically logged in

2. **Log In**
   - Enter your registered email and password
   - Click "Log In"
   - You'll be redirected to the dashboard

3. **Create Your First Note**
   - On the dashboard, enter a note title
   - Add your note content
   - Click "Add Note"
   - Your note will appear in the notes list

4. **Manage Notes**
   - **Edit**: Click the "Edit" button on any note
   - **Delete**: Click the "Delete" button (confirmation required)
   - **Search**: Use the search bar to find specific notes
   - **Filter**: Use the dropdown to filter by category

5. **View Analytics**
   - Click "Analytics" in the sidebar
   - View your note-taking statistics and charts
   - Analyze your activity patterns

6. **Update Profile**
   - Click "Profile" in the sidebar
   - Update your full name or email
   - Change your password if needed

### Password Reset

1. Click "Forgot Password?" on the login page
2. Enter your registered email
3. Check your email for the reset link
4. Follow the instructions to reset your password

## 📁 Project Structure

```
IAP_Project/
│
├── assets/                  # Frontend assets
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   └── js/
│       ├── dashboard.js    # Dashboard functionality
│       └── analytics.js    # Analytics charts
│
├── asset/                   # Legacy assets (deprecated)
│   └── js/
│       └── script.js
│
├── PHPMailer/              # PHPMailer library (legacy)
│
├── vendor/                 # Composer dependencies
│   ├── autoload.php
│   ├── phpmailer/
│   └── psr/
│
├── test/                   # Test files
│   ├── testdb.php
│   ├── test_insert.php
│   └── phpinfo.php
│
├── conf.php                # Application configuration
├── database.php            # Database connection
│
├── index.php               # Landing page & login
├── register.php            # User registration
├── Login.php               # User login handler
├── logout.php              # Logout handler
│
├── dashboard.php           # Main dashboard
├── add_note.php            # Create note endpoint
├── edit_note.php           # Edit note endpoint
├── delete_note.php         # Delete note endpoint
│
├── profile.php             # User profile page
├── update_profile.php      # Profile update handler
├── settings.php            # Settings page
├── update_password.php     # Password update handler
│
├── forgot_password.php     # Password reset request
├── reset_password.php      # Password reset handler
├── sendmail.php            # Email sending utility
├── verify_otp.php          # OTP verification
│
├── analytics.php           # Analytics dashboard
├── get_analytics_data.php  # Analytics API endpoint
├── track_interaction.php   # Interaction tracking endpoint
├── create_analytics_table.php # Analytics table setup
│
├── composer.json           # Composer dependencies
├── composer.lock          # Dependency lock file
├── ANALYTICS_SETUP.md      # Analytics setup guide
└── README.md               # This file
```

## 🔌 API Endpoints

### Authentication Endpoints

- `POST /register.php` - Register a new user
  - Parameters: `email`, `password`, `confirm_password`
  
- `POST /Login.php` - Authenticate user
  - Parameters: `email`, `password`
  
- `GET /logout.php` - Logout current user

### Note Management Endpoints

- `POST /add_note.php` - Create a new note
  - Parameters: `title`, `content`
  - Returns: `"success"`, `"error"`, or `"empty"`
  
- `POST /edit_note.php` - Update an existing note
  - Parameters: `note_id`, `title`, `content`
  - Returns: `"success"` or error message
  
- `POST /delete_note.php` - Delete a note
  - Parameters: `note_id`
  - Returns: `"success"` or error message

### Analytics Endpoints

- `GET /get_analytics_data.php` - Fetch analytics data
  - Returns: JSON object with chart data
  
- `POST /track_interaction.php` - Track user interaction
  - Parameters: `interaction_type`, `interaction_details`, `page_url`
  - Returns: JSON `{"status": "success"}`

### Profile Endpoints

- `POST /update_profile.php` - Update user profile
  - Parameters: `fullname`, `email`
  
- `POST /update_password.php` - Change password
  - Parameters: `current_password`, `new_password`, `confirm_password`

## 🔒 Security Features

### Password Security
- **Bcrypt Hashing**: All passwords are hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`
- **Password Verification**: Secure password comparison using `password_verify()`
- **Password Reset**: Secure token-based password reset via email

### Session Management
- **Session-based Authentication**: Secure session handling
- **Session Validation**: All protected pages check for valid sessions
- **Auto-redirect**: Unauthenticated users are redirected to login

### SQL Injection Prevention
- **Prepared Statements**: All database queries use prepared statements
- **Parameter Binding**: User input is bound to query parameters
- **Input Validation**: Email validation and input sanitization

### XSS Prevention
- **Output Escaping**: All user-generated content is escaped using `htmlspecialchars()`
- **Content Security**: Proper encoding in HTML output

### Data Privacy
- **User Isolation**: Users can only access their own notes and data
- **Analytics Privacy**: Analytics are user-specific and private

## 📊 Analytics System

The analytics system tracks comprehensive user interactions and provides visual insights.

### Tracked Interactions

- Page views
- Note creation, editing, deletion
- Note favoriting/unfavoriting
- Search operations
- Filter applications
- Profile updates
- Password changes
- Login/logout events
- Button clicks
- Form submissions

### Analytics Dashboard Features

1. **Statistics Cards**
   - Total Notes
   - Total Interactions
   - Average Note Length
   - Notes Created (Last 7 Days)

2. **Visual Charts**
   - Notes Created Over Time (Line Chart)
   - Notes by Day of Week (Doughnut Chart)
   - Notes by Hour of Day (Bar Chart)
   - User Interactions Distribution (Horizontal Bar Chart)
   - Activity Timeline (Multi-line Chart)

For detailed analytics setup instructions, see `ANALYTICS_SETUP.md`.

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
```
Error: MariaDB connection failed
```
**Solution**: 
- Verify database credentials in `conf.php`
- Ensure MySQL/MariaDB service is running
- Check if the database exists
- Verify port number matches your configuration

#### Email Not Sending
```
Mailer Error: ...
```
**Solution**:
- Verify SMTP credentials in `conf.php`
- For Gmail, ensure you're using an App Password (not your regular password)
- Check if 2-Step Verification is enabled
- Verify firewall allows SMTP connections

#### Session Issues
```
Session not persisting
```
**Solution**:
- Check PHP `session.save_path` is writable
- Verify `session_start()` is called before any output
- Check browser cookie settings
- Ensure proper session configuration in `php.ini`

#### Analytics Not Working
```
Charts not displaying
```
**Solution**:
- Run `php create_analytics_table.php` to create the analytics table
- Check browser console for JavaScript errors
- Verify Chart.js CDN is accessible
- Ensure user has logged in and created some notes

#### Composer Dependencies Not Installing
```
composer install fails
```
**Solution**:
- Ensure Composer is installed and in PATH
- Check internet connection
- Verify `composer.json` syntax
- Try `composer update` instead

### Debug Mode

To enable debug mode, ensure these lines are in your PHP files:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Warning**: Disable debug mode in production!

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Use prepared statements for all database queries
- Escape all user output
- Add comments for complex logic
- Test thoroughly before submitting

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

- **Project Team** - Initial work

## 🙏 Acknowledgments

- Bootstrap team for the excellent CSS framework
- Chart.js for powerful data visualization
- PHPMailer for reliable email functionality
- Font Awesome for beautiful icons
- All contributors and users of Notez Wiz

## 📞 Support

For support, email [your-admin@email.com] or open an issue in the repository.

---

**Note**: This is a student project developed for educational purposes. For production use, additional security measures and optimizations are recommended.

**Last Updated**: 2024
