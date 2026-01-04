# 🛒 Symfony Marketplace Application

A modern, full-featured online marketplace built with Symfony 7, enabling users to create, manage, and browse classified advertisements with advanced role-based access control.

![Symfony](https://img.shields.io/badge/Symfony-7.x-black?style=flat&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

---

## 📸 Screenshots

### Homepage
![Homepage](Screenshots/homepage.png)
*Modern landing page with featured marketplace listings*

### User Dashboard
![User Dashboard](Screenshots/userdashboard.png)
*Personal dashboard with advertisement statistics*

### My Advertisements
![My Adverts](Screenshots/myadverts.png)
*Manage your listings with full CRUD operations*

### Create Advertisement
![Create Advert](Screenshots/createadvertisement.png)
*Intuitive form for creating new listings*

### Manager Dashboard
![Manager Dashboard](Screenshots/managerdashboard.png)
*Moderate and manage all advertisements*

### Admin Panel
![Admin Users](Screenshots/adminpanel.png)
*User management with search and filtering*

![Admin Moderators](Screenshots/rolemanagement.png)
*Role management - promote users to managers*

### Mobile Responsive
<div>
  <img src="Screenshots/mobilemenu.png" width="250" alt="Mobile Menu"/>
  <img src="Screenshots/mobilelisting.png" width="250" alt="Mobile Listing"/>
</div>

*Fully responsive design optimized for all devices*

---

## ✨ Features

### 🔐 For All Users
- **Secure Authentication**: Registration, login, and password management with bcrypt encryption
- **Advertisement Management**: Create, edit, and delete your own classified ads
- **Image Upload**: Upload product images via Cloudinary integration
- **Category Browsing**: Browse ads by categories (Electronics, Fashion, Automotive, etc.)
- **Search & Filter**: Find ads by title, category, or location
- **Responsive Design**: Fully mobile-optimized interface with slide-out navigation

### 👔 For Managers (Moderators)
- **All User Features** +
- **Moderate All Ads**: View, edit, and delete any advertisement
- **Content Management**: Access dedicated moderation dashboard
- **Advanced Filtering**: Search and filter all listings

### 👑 For Administrators
- **All Manager Features** +
- **User Management**: View, search, and delete user accounts
- **Role Management**: Promote users to Manager role or demote them
- **System Analytics**: View total users, ads, and manager statistics
- **Complete Control**: Full administrative access to all features

---

## 🛠 Technology Stack

**Backend:**
- Symfony 7.x (PHP Framework)
- PHP 8.2+
- Doctrine ORM (Database)
- MySQL / PostgreSQL

**Frontend:**
- Twig 3.x (Template Engine)
- Bootstrap 5.3 (CSS Framework)
- Bootstrap Icons
- Vanilla JavaScript

**Services:**
- Cloudinary (Image Storage & CDN)
- Symfony Security Component
- KnpPaginatorBundle (Pagination)

---

## 📋 Prerequisites

Before installing this application, ensure you have:

- **PHP** 8.2 or higher
- **Composer** 2.x (PHP Dependency Manager)
- **MySQL** 8.0 or **PostgreSQL** 13+
- **Git** (for cloning the repository)
- **Cloudinary Account** (free tier available at [cloudinary.com](https://cloudinary.com))

### Check PHP Version
```bash
php -v
```

### Check Composer Installation
```bash
composer --version
```

---

## 🚀 Installation Guide

Follow these steps to get the application running on your local machine [web:298][web:300]:

### Step 1: Clone the Repository

```bash
# Clone the project
git clone https://github.com/yourusername/symfony-marketplace.git

# Navigate to project directory
cd symfony-marketplace
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

**Note:** This may take a few minutes. If you see errors, make sure PHP 8.2+ is installed [web:298].

### Step 3: Configure Environment Variables

Create your local environment file:

```bash
# Copy the example environment file
cp .env .env.local
```

Open `.env.local` and configure the following:

#### Database Configuration

**For MySQL:**
```env
DATABASE_URL="mysql://your_db_user:your_db_password@127.0.0.1:3306/marketplace_db?serverVersion=8.0&charset=utf8mb4"
```

**For PostgreSQL:**
```env
DATABASE_URL="postgresql://your_db_user:your_db_password@127.0.0.1:5432/marketplace_db?serverVersion=15&charset=utf8"
```

Replace:
- `your_db_user` - your database username
- `your_db_password` - your database password
- `marketplace_db` - desired database name

#### Cloudinary Configuration

1. Sign up for free at [cloudinary.com](https://cloudinary.com)
2. Go to Dashboard → Account Details
3. Copy your "API Environment variable"
4. Add to `.env.local`:

```env
CLOUDINARY_URL=cloudinary://123456789012345:abcdefghijklmnopqrstuv@your-cloud-name
```

#### Application Secret

Generate a secure secret key:

```bash
# Generate secret
php -r "echo bin2hex(random_bytes(32));"
```

Add to `.env.local`:
```env
APP_SECRET=paste_generated_key_here
```

### Step 4: Create Database

```bash
# Create the database
php bin/console doctrine:database:create

# Run migrations to create tables
php bin/console doctrine:migrations:migrate
```

When prompted, type `yes` to execute the migrations.

### Step 5: Load Sample Data (Optional but Recommended)

```bash
php bin/console doctrine:fixtures:load
```

Type `yes` when prompted. This creates:

**Default Users:**
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | admin123 |
| Manager | manager@example.com | manager123 |
| User | user@example.com | user123 |

**Sample Data:**
- Product categories (Electronics, Fashion, Automotive, etc.)
- Sample advertisements with images

### Step 6: Start the Application

**Option A: Using Symfony CLI (Recommended)**

```bash
# Install Symfony CLI first if you haven't
# Visit: https://symfony.com/download

# Start the server
symfony server:start
```

**Option B: Using PHP Built-in Server**

```bash
php -S localhost:8000 -t public/
```

### Step 7: Access the Application

Open your browser and visit:
```
http://localhost:8000
```

**You should see the marketplace homepage!** 🎉

---

## 🔑 Default Login Credentials

After loading fixtures, you can login with:

### Admin Account
- **Email:** admin@example.com
- **Password:** admin123
- **Access:** Full system control

### Manager Account
- **Email:** manager@example.com
- **Password:** manager123
- **Access:** Moderate all advertisements

### User Account
- **Email:** user@example.com
- **Password:** user123
- **Access:** Create and manage own ads

**⚠️ Important:** Change these passwords in production!

---

## 📖 User Guide

### For Regular Users

#### Creating Your First Advertisement

1. **Login** to your account
2. Click **"My Adverts"** in the sidebar
3. Click **"Create New Advert"** button
4. Fill in the form:
   - Select a **Category**
   - Enter **Product Name**
   - Write a **Description**
   - Set **Price** (in LKR)
   - Enter **Location**
   - Add **Phone Number** for contact
   - **Upload Image** (max 5MB, JPG/PNG/WEBP)
5. Click **"Create Ad"**
6. Your ad is now live! 🎉

#### Managing Your Advertisements

- **View All:** Navigate to "My Adverts"
- **Edit:** Click pencil icon on any ad
- **Delete:** Click trash icon (confirmation required)
- **Dashboard:** View your ad count on dashboard

### For Managers

#### Moderating Advertisements

1. Login with manager credentials
2. Access **"Manage Ads"** dashboard
3. View all user advertisements
4. **Filter by category** using dropdown
5. **Search** by title, seller, or ID
6. **Edit any ad:** Click pencil icon
7. **Delete inappropriate ads:** Click trash icon

### For Administrators

#### Managing Users

1. Login with admin credentials
2. Click **"Manage Users"** in admin panel
3. View all registered users
4. **Search users** by name, email, or ID
5. **Delete users:** Click trash icon with confirmation

#### Promoting Users to Manager

1. Go to **"Moderator Actions"**
2. Search for user by name or email
3. View user role badges:
   - 🔵 **Manager** - Current managers
   - ⚫ **User** - Regular users
4. Click **"Promote"** on regular users
5. Click **"Demote"** on managers to remove role

---

## 🔧 Configuration

### Customizing Categories

Edit `src/DataFixtures/CategoryFixtures.php` to add/modify categories:

```php
$categories = [
    'Electronics',
    'Fashion',
    'Automotive',
    'Your Custom Category', // Add here
];
```

Then reload fixtures:
```bash
php bin/console doctrine:fixtures:load
```

### Image Upload Limits

Edit `.env.local` to change max upload size:
```env
# Default: 5MB
MAX_UPLOAD_SIZE=5242880
```

### Email Configuration (Optional)

To enable email notifications, configure mailer in `.env.local`:

```env
MAILER_DSN=smtp://username:password@smtp.gmail.com:587
```

---

## 🐛 Troubleshooting

### Database Connection Failed

**Problem:** Cannot connect to database

**Solution:**
1. Check MySQL/PostgreSQL is running:
   ```bash
   # MySQL
   sudo service mysql start
   
   # PostgreSQL
   sudo service postgresql start
   ```
2. Verify credentials in `.env.local`
3. Test connection:
   ```bash
   php bin/console doctrine:database:create
   ```

### Cloudinary Upload Not Working

**Problem:** Images fail to upload

**Solution:**
1. Verify `CLOUDINARY_URL` in `.env.local`
2. Check image size (must be < 5MB)
3. Ensure allowed formats: JPG, PNG, WEBP
4. Test Cloudinary credentials in dashboard

### CSS/Styling Not Loading

**Problem:** Page displays without styling

**Solution:**
```bash
# Clear cache
php bin/console cache:clear

# Restart server
symfony server:stop
symfony server:start
```

### Permission Denied Errors

**Problem:** Cannot write to cache/log directories

**Solution:**
```bash
# Linux/Mac
chmod -R 777 var/cache var/log

# Windows - Run as Administrator
cacls var\cache /e /t /g everyone:f
cacls var\log /e /t /g everyone:f
```

### Port 8000 Already in Use

**Problem:** Server fails to start

**Solution:**
```bash
# Use different port
php -S localhost:8001 -t public/

# Or kill process using port 8000
# Linux/Mac
lsof -ti:8000 | xargs kill

# Windows
netstat -ano | findstr :8000
taskkill /PID [process_id] /F
```

---

## 🔒 Security Best Practices

This application implements multiple security layers [web:298][web:307]:

### Built-in Security Features

✅ **Password Hashing:** Bcrypt with cost factor 13  
✅ **CSRF Protection:** All forms include CSRF tokens  
✅ **XSS Prevention:** Twig automatic output escaping  
✅ **SQL Injection Prevention:** Doctrine ORM parameterized queries  
✅ **Role-Based Access Control:** Three-tier authorization system  
✅ **Secure File Uploads:** Validation and Cloudinary storage  

### Production Deployment Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Set `APP_ENV=prod` in `.env.local`
- [ ] Use strong `APP_SECRET` (32+ characters)
- [ ] Configure HTTPS/SSL certificate
- [ ] Set up database backups
- [ ] Enable error logging (not displaying)
- [ ] Review and restrict file permissions
- [ ] Configure firewall rules
- [ ] Set up monitoring/alerts

---

## 📱 Mobile Responsive Design

The application is fully optimized for mobile devices with:

- **Slide-out navigation drawer** on tablets and phones
- **Touch-friendly buttons** and interactive elements
- **Card-based layouts** for easy mobile browsing
- **Optimized images** for faster loading
- **Responsive tables** that adapt to screen size
- **Full functionality** on all devices

### Responsive Breakpoints

- **< 992px:** Tablet mode with slide-out menu
- **< 768px:** Small tablet with reduced padding
- **< 576px:** Phone mode with vertical stacking
- **< 400px:** Compact mode for small phones
- **Landscape:** Special optimizations for horizontal orientation

---

## 🧪 Testing

### Run Database Schema Validation

```bash
php bin/console doctrine:schema:validate
```

### Check Security Vulnerabilities

```bash
symfony check:security
```

### Clear Cache

```bash
php bin/console cache:clear
```

---

## 🆘 Getting Help

### Common Issues

Check the **Troubleshooting** section above for solutions to common problems.

### Documentation

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM Guide](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [Twig Templates](https://twig.symfony.com/doc/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)

### Support

If you encounter issues:

1. Check existing [GitHub Issues](https://github.com/yourusername/symfony-marketplace/issues)
2. Create a new issue with:
   - Clear description of the problem
   - Steps to reproduce
   - Error messages (if any)
   - Your environment (OS, PHP version, etc.)

---

## 🤝 Contributing

Contributions are welcome! If you'd like to improve this project:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### Contribution Guidelines

- Follow PSR-12 coding standards
- Write clear commit messages
- Test your changes thoroughly
- Update documentation if needed

---

## 📄 License

This project is licensed under the MIT License - you are free to use, modify, and distribute this software.

---

## 🙏 Acknowledgments

Built with:
- [Symfony Framework](https://symfony.com/)
- [Bootstrap](https://getbootstrap.com/)
- [Cloudinary](https://cloudinary.com/)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [Twig Template Engine](https://twig.symfony.com/)

---

## 📞 Contact & Support

**Project Repository:** [github.com/yourusername/symfony-marketplace](https://github.com/yourusername/symfony-marketplace)

For questions or support, please open an issue on GitHub.

---

<div align="center">
  <p>⭐ If you find this project helpful, please give it a star!</p>
  <p>Made with ❤️ using Symfony</p>
</div>
```
