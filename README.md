# Mould Tracking System

A comprehensive production management system for tracking injection moulds throughout their lifecycle, from commissioning to maintenance and production runs.

## 📋 Features

- **Real-time Production Tracking** - Monitor active production runs with live status updates
- **Preventive Maintenance Management** - Schedule and track PM intervals (shot-based and time-based)
- **Corrective Maintenance Logging** - Record unplanned repairs and downtime
- **Comprehensive Reporting** - Production metrics, NG% analysis, maintenance history
- **Role-Based Access Control** - 5 roles (Admin, Production, Maintenance, QA, Viewer)
- **Activity Logging** - Complete audit trail of all changes
- **Excel Import/Export** - Bulk operations and data analysis
- **QR Code Generation** - Physical mould tracking
- **Dashboard Analytics** - Active runs, PM alerts, top NG/CM metrics
- **Location History** - Track mould movements across plants and zones

## 🚀 Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3, Tailwind CSS 3, Alpine.js
- **Database**: SQLite (dev), MySQL/PostgreSQL (production)
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Activity Log**: Spatie Laravel Activity Log
- **Excel**: Maatwebsite Excel
- **QR Codes**: SimpleSoftwareIO Simple QR Code
- **Build Tool**: Vite 7
- **Testing**: PHPUnit 11

## 📦 Installation

###  Local Development

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd mould-tracking-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Creates SQLite database automatically
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Seed demo data (optional)**
   ```bash
   php artisan db:seed
   ```

6. **Run development servers**
   ```bash
   # Terminal 1: PHP development server
   php artisan serve

   # Terminal 2: Vite dev server
   npm run dev
   ```

   Or use the combined dev script:
   ```bash
   composer dev
   ```

7. **Access the application**
   - URL: `http://localhost:8000`
   - Default admin credentials (if seeded):
     - Email: `admin@example.com`
     - Password: `password`

### 🐳 Docker Development

1. **Start containers**
   ```bash
   docker-compose up -d
   ```

2. **Install dependencies inside container**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed
   ```

3. **Access the application**
   - URL: `http://localhost`

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run specific test file
php artisan test tests/Feature/ProductionRunTest.php

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

See [TESTING.md](TESTING.md) for detailed testing documentation.

## 👥 User Roles & Permissions

| Role | Access |
|------|--------|
| **Admin** | Full access including master data, audit logs, user management |
| **Production** | Production runs, mould viewing, reporting |
| **Maintenance** | Maintenance events (PM/CM), mould viewing, reporting |
| **QA** | Quality assurance, reporting, mould viewing |
| **Viewer** | Read-only access to all operational data |

## 📊 Key Workflows

### Production Run Workflow
1. Production user starts a run (mould + machine)
2. System auto-captures cavity count from mould master
3. Run tracked as "active" (`end_ts IS NULL`)
4. User closes run with shots, OK/NG parts, defects
5. System validates: `ok_part + ng_part = shot_total × cavities`
6. System validates: `sum(defects.qty) = ng_part`
7. Mould status updated to AVAILABLE

### Preventive Maintenance Workflow
1. Maintenance user creates PM event
2. Set next due date and/or shot count
3. Dashboard alerts when PM is due/overdue
4. PM completion updates next due calculations

## 🔧 Configuration

### Environment Variables

Key `.env` configurations:

```env
APP_NAME="Mould Tracking System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

For production configuration, see [DEPLOYMENT.md](DEPLOYMENT.md).

## 📚 API Documentation

The application uses Livewire components for most interactions. Key routes:

- `GET /dashboard` - Main dashboard
- `GET /moulds` - Mould listing
- `GET /moulds/{mould}` - Mould details
- `GET /runs/active` - Active production runs
- `GET /runs/{run}/close` - Close production run
- `GET /maintenance/events` - Maintenance event listing
- `GET /reports/production` - Production reports
- `GET /reports/maintenance` - Maintenance reports
- `GET /health` - Health check endpoint (no auth)

Full route list: `php artisan route:list`

## 🔒 Security Features

- Rate limiting on authentication routes (3-5 attempts/minute)
- CSRF protection (Laravel default)
- SQL injection protection (Eloquent ORM)
- Role-based authorization
- Activity logging for audit compliance
- Session-based authentication

## 📈 Monitoring

Health check endpoint for monitoring systems:

```bash
curl http://localhost:8000/health
```

Response:
```json
{
  "status": "healthy",
  "timestamp": "2026-01-14T15:00:00+00:00",
  "checks": {
    "database": "ok",
    "application": "running",
    "version": "1.0.0",
    "environment": "production",
    "disk_space_mb": 15234.56
  }
}
```

## 🚢 Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment guide including:
- MySQL/PostgreSQL migration
- Queue worker setup
- SSL/TLS configuration
- Backup strategies
- Performance optimization

## 🔄 Database Migration (SQLite → MySQL)

See [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) for step-by-step instructions.

## 🤝 Contributing

1. Create a feature branch
2. Write tests for new features
3. Ensure all tests pass: `php artisan test`
4. Follow Laravel Pint code style: `./vendor/bin/pint`
5. Submit a pull request

## 📝 License

This project is proprietary and confidential.

## 🆘 Troubleshooting

### Common Issues

**Issue**: "Class 'App\Models\Mould' not found"
**Solution**: Run `composer dump-autoload`

**Issue**: Migration errors
**Solution**: Fresh migrate `php artisan migrate:fresh --seed`

**Issue**: Livewire components not loading
**Solution**: Clear cache `php artisan optimize:clear`

**Issue**: Tests failing with database errors
**Solution**: Ensure `phpunit.xml` uses `:memory:` for `DB_DATABASE`

## 📞 Support

For issues or questions:
1. Check existing documentation
2. Review test files for usage examples
3. Check Laravel and Livewire documentation
4. Contact the development team

---

**Version**: 1.0.0  
**Last Updated**: January 2026
