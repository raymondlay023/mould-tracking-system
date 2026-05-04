# Mould Tracking System

A comprehensive production management system for tracking injection moulds throughout their lifecycle, from commissioning to maintenance and production runs.

## Documentation

For detailed documentation, please see the [docs/](docs/) folder:

- [README](docs/README.md) - Complete user guide with installation, features, and usage
- [SYSTEM_ARCHITECTURE](docs/SYSTEM_ARCHITECTURE.md) - Technical architecture and design details

## Quick Start

1. **Clone and setup**
   ```bash
   git clone <repository-url>
   cd mould-tracking-system
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate
   ```

2. **Run development servers**
   ```bash
   composer dev  # Runs PHP server, queue worker, and Vite dev server
   ```

3. **Access the application**
   - URL: `http://localhost:8000`
   - Demo admin: `admin@demo.local` / `password`

## Key Features

- Real-time production tracking with live status updates
- Preventive maintenance scheduling (shot-based and time-based)
- Comprehensive reporting and analytics
- Role-based access control (5 roles)
- QR code generation for physical mould tracking
- Excel import/export functionality

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Livewire 3, Tailwind CSS, Alpine.js
- **Database**: SQLite (dev) / MySQL (Docker production)
- **Build**: Vite, Composer, NPM
- **Container**: Docker (production only)

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed contribution guidelines.

---

**Version**: 1.0.0
**Last Updated**: May 2026