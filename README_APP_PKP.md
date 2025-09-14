# 🚀 APP PKP - Sistem Manajemen Kinerja Pegawai

## Pengadilan Agama

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue.svg)](https://mysql.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)

---

## 📖 Tentang APP PKP

**APP PKP (Perjanjian Kinerja Pegawai)** adalah aplikasi manajemen kinerja pegawai yang dirancang khusus untuk instansi pemerintah, khususnya Pengadilan Agama. Sistem ini mengelola perjanjian kinerja individu pegawai dan menghasilkan laporan dalam format PDF sesuai standar pemerintahan.

### ✨ Fitur Utama

-   🔐 **Multi-Role Authentication** (Super Admin, Pejabat Penilai, Atasan, Pegawai)
-   📋 **Perjanjian Kinerja Individual** dengan approval workflow
-   📊 **Penilaian Kinerja Bulanan** dengan auto-calculation
-   📄 **PDF Report Generation** (3 jenis laporan)
-   📎 **Document Upload & Management**
-   📧 **Email Notifications**
-   📥 **Bulk Data Import** via Excel
-   📈 **Performance Dashboard**
-   🔍 **Advanced Search & Filtering**

---

## 🛠️ Tech Stack

-   **Backend:** Laravel 12, PHP 8.2+
-   **Frontend:** Vue.js 3, Inertia.js
-   **Database:** MySQL 8.0+
-   **PDF Generation:** DomPDF
-   **Authentication:** Laravel Sanctum
-   **Styling:** Tailwind CSS

---

## 📋 Prerequisites

### System Requirements

-   **PHP:** 8.2 or higher
-   **Node.js:** 16.x or higher
-   **MySQL:** 8.0 or higher
-   **Composer:** Latest version
-   **NPM:** Latest version

### Hardware Requirements

-   **RAM:** Minimum 4GB
-   **Storage:** Minimum 10GB free space
-   **Processor:** Intel Core i3 or equivalent

---

## 🚀 Installation & Setup

### 1. Clone Repository

```bash
git clone https://github.com/your-repo/app-pkp.git
cd app-pkp
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Database Setup

```bash
# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_pkp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Database

```bash
php artisan db:seed
```

### 8. Build Assets

```bash
npm run build
php artisan storage:link
```

### 9. Start Development Server

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev

# Terminal 3 - Queue Worker (for email notifications)
php artisan queue:work
```

### 10. Access Application

Open browser and go to: `http://localhost:8000`

---

## 👤 Default User Accounts

After seeding, you can login with these accounts:

| Role            | Email                 | Password   |
| --------------- | --------------------- | ---------- |
| Super Admin     | `superadmin@pkp.test` | `password` |
| Pejabat Penilai | `pejabat@pkp.test`    | `password` |
| Atasan Pejabat  | `atasan@pkp.test`     | `password` |
| Pegawai         | `pegawai@pkp.test`    | `password` |
| Pegawai 2       | `pegawai2@pkp.test`   | `password` |

---

## 📁 Project Structure

```
app-pkp/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # API & Web Controllers
│   │   ├── Middleware/           # Custom Middleware
│   │   └── Requests/             # Form Request Validation
│   ├── Models/                   # Eloquent Models
│   ├── Mail/                     # Email Templates
│   └── Imports/                  # Excel Import Classes
├── database/
│   ├── migrations/               # Database Migrations
│   ├── seeders/                  # Database Seeders
│   └── factories/                # Model Factories
├── resources/
│   ├── js/                       # Vue.js Components
│   ├── css/                      # Stylesheets
│   └── views/                    # Blade Templates & PDF
├── routes/
│   ├── api.php                   # API Routes
│   └── web.php                   # Web Routes
├── storage/
│   ├── app/                      # File Storage
│   └── templates/                # Import Templates
├── tests/                        # Test Files
├── config/                       # Configuration Files
├── public/                       # Public Assets
├── bootstrap/                    # Laravel Bootstrap
├── artisan                       # Laravel CLI
├── composer.json                 # PHP Dependencies
├── package.json                  # Node Dependencies
└── vite.config.js               # Vite Configuration
```

---

## 🔧 Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME="APP PKP"
APP_ENV=local
APP_KEY=base64_key_here
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_pkp
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@pkp.test"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database
```

### Queue Configuration

For email notifications, configure queue:

```bash
# Run queue worker
php artisan queue:work

# Or use supervisor for production
# See: https://laravel.com/docs/queues#supervisor-configuration
```

---

## 📊 Database Schema

### Core Tables

-   **users** - User accounts with roles
-   **employees** - Employee master data
-   **work_units** - Organizational units
-   **performance_agreements** - PK documents
-   **performance_targets** - PK targets
-   **monthly_assessments** - Monthly evaluations
-   **assessment_details** - Assessment breakdowns
-   **documents** - File attachments

### Key Relationships

```
User (1) ──── (1) Employee
Employee (1) ──── (N) PerformanceAgreement
PerformanceAgreement (1) ──── (N) PerformanceTarget
PerformanceAgreement (1) ──── (N) MonthlyAssessment
MonthlyAssessment (1) ──── (N) AssessmentDetail
MonthlyAssessment (1) ──── (N) Document
```

---

## 🔐 User Roles & Permissions

### 1. Super Admin

-   ✅ Full system access
-   ✅ Manage all users and data
-   ✅ Configure system settings
-   ✅ Generate all reports

### 2. Pejabat Penilai (Assessor)

-   ✅ Create PK for subordinates
-   ✅ Conduct monthly assessments
-   ✅ Upload supporting documents
-   ✅ View assigned employee data

### 3. Atasan Pejabat Penilai (Supervisor)

-   ✅ Approve performance agreements
-   ✅ Final approve monthly assessments
-   ✅ View unit performance reports
-   ✅ Monitor team performance

### 4. Pegawai (Employee)

-   ✅ Self-service PK creation
-   ✅ Self-assessment monthly
-   ✅ Upload personal documents
-   ✅ View personal performance data

---

## 📄 PDF Templates

The system includes 3 professional PDF templates:

1. **Perjanjian Kinerja Individual** - Performance agreement document
2. **Penilaian Capaian Kinerja Bulanan** - Monthly assessment report
3. **Rekapitulasi Penilaian Capaian Kinerja** - Annual performance summary

Templates are located in `resources/views/pdf/` and use DomPDF for generation.

---

## 📧 Email Notifications

### Notification Types

-   **PK Approved** - Sent to employee when PK is approved
-   **PK Rejected** - Sent to employee with rejection reason
-   **Assessment Submitted** - Sent to supervisor for approval
-   **Assessment Approved** - Sent to employee when assessment is approved

### Email Templates

Located in `app/Mail/`:

-   `PerformanceAgreementApproved.php`
-   `PerformanceAgreementRejected.php`

---

## 📥 Data Import

### Employee Import

1. Download template: `storage/app/templates/employee_import_template.xlsx`
2. Fill employee data according to format
3. Upload via: Menu Pegawai → Import Data
4. System validates and imports data

### Import Format

```csv
nip,nama,pangkat_golongan,jabatan,unit_kerja_id,status,tanggal_masuk
198501012010011001,AHMAD RAHMAN,Pembina Tk. I / IV.b,Juru Sita,1,active,2010-01-01
```

---

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/          # Feature tests
├── Unit/            # Unit tests
└── TestCase.php     # Base test case
```

---

## 🚀 Deployment

### Production Setup

1. **Server Requirements:**

    - Ubuntu 20.04+ / CentOS 8+
    - PHP 8.2+ with required extensions
    - MySQL 8.0+
    - Nginx/Apache
    - SSL Certificate

2. **Deployment Steps:**

    ```bash
    # Clone to production server
    git clone https://github.com/your-repo/app-pkp.git /var/www/app-pkp
    cd /var/www/app-pkp

    # Install dependencies
    composer install --optimize-autoloader --no-dev
    npm install && npm run build

    # Environment setup
    cp .env.example .env
    php artisan key:generate
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Database setup
    php artisan migrate --force
    php artisan db:seed --force

    # Storage setup
    php artisan storage:link

    # Queue setup
    php artisan queue:table
    php artisan migrate
    ```

3. **Web Server Configuration:**

    ```nginx
    server {
        listen 80;
        server_name pkp-app.test;
        root /var/www/app-pkp/public;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
    ```

4. **SSL Setup:**

    ```bash
    certbot --nginx -d pkp-app.test
    ```

5. **Queue Worker:**
    ```bash
    # Using Supervisor
    sudo apt install supervisor
    # Configure supervisor for Laravel queues
    ```

---

## 📈 Performance Optimization

### Database Optimization

-   ✅ Indexes on frequently queried columns
-   ✅ Eager loading for relationships
-   ✅ Query optimization with select columns

### Caching Strategy

-   ✅ Response caching (10-minute TTL)
-   ✅ Database query result caching
-   ✅ Configuration caching

### File Storage

-   ✅ Optimized file upload handling
-   ✅ Secure file storage with access control
-   ✅ Automatic file cleanup

---

## 🔧 Troubleshooting

### Common Issues

1. **Login Issues:**

    ```bash
    # Clear cache
    php artisan cache:clear
    php artisan config:clear
    ```

2. **Permission Issues:**

    ```bash
    # Fix storage permissions
    chown -R www-data:www-data storage/
    chmod -R 755 storage/
    ```

3. **Database Issues:**

    ```bash
    # Reset database
    php artisan migrate:fresh --seed
    ```

4. **Asset Issues:**
    ```bash
    # Rebuild assets
    npm run build
    php artisan storage:link
    ```

---

## 📞 Support & Contact

-   **Project Repository:** [GitHub Link]
-   **Documentation:** See `USER_MANUAL_APP_PKP.md`
-   **Quick Reference:** See `QUICK_REFERENCE_GUIDE.md`
-   **Issue Tracker:** [GitHub Issues]

### Team Contacts

-   **Technical Lead:** [Name] - [Email]
-   **Project Manager:** [Name] - [Email]
-   **Support Team:** support@pkp-app.test

---

## 📋 Changelog

### Version 1.0.0 (2025-01-14)

-   ✅ Initial release
-   ✅ Complete PK management system
-   ✅ Multi-role authentication
-   ✅ PDF report generation
-   ✅ Email notifications
-   ✅ Bulk import functionality
-   ✅ Performance optimizations

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

-   **Laravel Framework** - The PHP framework
-   **Vue.js** - Progressive JavaScript framework
-   **Inertia.js** - Modern monolith
-   **DomPDF** - PDF generation library
-   **Tailwind CSS** - Utility-first CSS framework

---

**🏛️ Pengadilan Agama - Sistem Manajemen Kinerja Pegawai**

**Developed with ❤️ for efficient performance management**
