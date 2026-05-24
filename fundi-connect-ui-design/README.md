# FundiConnect - HTML/PHP Platform

FundiConnect is a platform connecting households and businesses with verified skilled workers (fundis) in Uganda. This version uses **HTML/PHP** with MySQL database.

## 🚀 Features

- **User Authentication**: Registration, login, and password management
- **Role-Based Access**: Admin, Client, and Fundi dashboards
- **Service Categories**: Plumbing, Electrical, Carpentry, Painting, Cleaning, and more
- **Job Management**: Create, track, and manage job requests
- **Reviews & Ratings**: Rate and review completed services
- **Messaging System**: Direct communication between clients and fundis
- **Verification System**: Admin verification for fundi profiles
- **Responsive Design**: Bootstrap 5 with custom dark theme

## 📋 Requirements

- **XAMPP** (or any PHP development environment)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache Web Server
- Modern web browser

## 🛠️ Installation

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP to `C:\xampp` (or your preferred location)
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Setup Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `fundiconnect`
3. Import the database schema:
   - Click on the `fundiconnect` database
   - Go to "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go" to import

### Step 3: Configure Database Connection

1. Open `config/database.php`
2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Your MySQL password
   define('DB_NAME', 'fundiconnect');
   ```

### Step 4: Create Uploads Directory

Create an `uploads` folder in the project root for file uploads:
```
mkdir uploads
```

### Step 5: Access the Application

Open your browser and navigate to:
```
http://localhost/fundi-connect-ui-design/
```

## 👤 Default Admin Account

After importing the database, you can login with:

- **Email**: `admin@fundiconnect.com`
- **Password**: `admin123`

**⚠️ Important**: Change this password immediately after first login!

## 📁 Project Structure

```
fundi-connect-ui-design/
├── api/                    # API endpoints
│   ├── login.php          # Login handler
│   ├── register.php       # Registration handler
│   └── logout.php         # Logout handler
├── assets/                # Static assets
│   └── css/
│       └── style.css      # Custom styles
├── config/                # Configuration files
│   ├── config.php         # App configuration
│   └── database.php       # Database connection
├── database/              # Database files
│   └── schema.sql         # Database schema
├── includes/              # PHP includes
│   ├── auth.php           # Authentication functions
│   └── functions.php      # Helper functions
├── public/                # Public assets (images, icons)
├── uploads/               # User uploaded files
├── index.php              # Homepage
├── login.php              # Login page
├── register.php           # Registration page
├── admin-dashboard.php    # Admin dashboard
├── client-dashboard.php   # Client dashboard
├── fundi-dashboard.php    # Fundi dashboard
└── contact.php            # Contact page
```

## 🔐 Security Features

- Password hashing using bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- CSRF token protection (ready to implement)
- Session management
- Role-based access control

## 🎨 Customization

### Changing Colors

Edit `assets/css/style.css` and modify the CSS variables:

```css
:root {
    --gold: #D4AF37;
    --gold-light: #E5C158;
    --black: #0A0A0A;
    /* ... more variables */
}
```

### Adding New Service Categories

1. Open phpMyAdmin
2. Go to `service_categories` table
3. Insert new category with name, slug, description, and icon

## 📱 Pages Overview

### Public Pages
- **index.php**: Homepage with hero section, services, and featured fundis
- **login.php**: User login
- **register.php**: Multi-step registration (Client or Fundi)
- **contact.php**: Contact form

### Protected Pages
- **admin-dashboard.php**: Admin panel for managing users and verifications
- **client-dashboard.php**: Client dashboard for managing job requests
- **fundi-dashboard.php**: Fundi dashboard for viewing and accepting jobs

## 🔧 Development

### Adding New Features

1. **Create API Endpoint**: Add new file in `api/` folder
2. **Add Functions**: Add helper functions in `includes/functions.php`
3. **Update Database**: Modify `database/schema.sql` if needed
4. **Create Frontend**: Add new PHP page or update existing ones

### Database Functions

Common database operations are in `includes/functions.php`:
- `sanitize()` - Clean user input
- `isLoggedIn()` - Check authentication
- `getCurrentUser()` - Get current user data
- `hasRole()` - Check user role
- `redirect()` - Redirect to URL

## 🐛 Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database credentials in `config/database.php`
- Ensure `fundiconnect` database exists

### Page Not Found (404)
- Check if Apache is running
- Verify the project is in `htdocs` folder
- Check file extensions are `.php` not `.html`

### Session Issues
- Clear browser cookies
- Check PHP session settings in `php.ini`
- Ensure `session_start()` is called

### Upload Errors
- Check `uploads/` folder exists
- Verify folder permissions (should be writable)
- Check PHP upload settings in `php.ini`

## 📝 API Endpoints

### Authentication
- `POST /api/login.php` - User login
- `POST /api/register.php` - User registration
- `GET /api/logout.php` - User logout

### Future Endpoints (To be implemented)
- `GET /api/fundis.php` - Get list of fundis
- `POST /api/jobs.php` - Create job request
- `GET /api/jobs/{id}` - Get job details
- `POST /api/reviews.php` - Submit review

## 🚀 Deployment

### For Production:

1. **Update Configuration**:
   - Set `error_reporting(0)` in `config/config.php`
   - Update `APP_URL` in `config/config.php`
   - Change database credentials

2. **Security**:
   - Change default admin password
   - Enable HTTPS
   - Set secure session cookies
   - Implement CSRF protection

3. **Optimization**:
   - Enable PHP OPcache
   - Minify CSS/JS files
   - Enable gzip compression
   - Set up CDN for static assets

## 📄 License

This project is proprietary software for FundiConnect.

## 👥 Support

For support, email: support@fundiconnect.com

## 🔄 Migration from React/Next.js

This project was converted from React/Next.js to pure HTML/PHP:

### What Changed:
- ✅ Removed all React/Next.js dependencies
- ✅ Converted `.tsx` components to `.php` pages
- ✅ Replaced client-side routing with PHP pages
- ✅ Added PHP session management
- ✅ Created MySQL database structure
- ✅ Implemented server-side authentication
- ✅ Kept all existing HTML/CSS/Bootstrap code

### What Stayed:
- ✅ All existing HTML pages
- ✅ Bootstrap 5 styling
- ✅ Custom CSS design
- ✅ JavaScript functionality
- ✅ Responsive layout

## 📚 Next Steps

1. ✅ Setup database and import schema
2. ✅ Test login/registration
3. ⏳ Implement job request functionality
4. ⏳ Add messaging system
5. ⏳ Implement payment integration
6. ⏳ Add email notifications
7. ⏳ Deploy to production server

---

**Built with ❤️ for Uganda's skilled workers**
