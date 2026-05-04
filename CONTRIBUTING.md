# Contributing to Mould Tracking System

Thank you for your interest in contributing to the Mould Tracking System! This document provides guidelines and information to help you contribute effectively.

## 🏗️ **System Architecture**

### Technology Stack
- **Backend**: Laravel 12.x (PHP 8.3+)
- **Frontend**: Livewire 3.x, Tailwind CSS 3.x, Alpine.js
- **Database**: MySQL 8.0+ (production), SQLite (local development)
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission v6.25.0
- **Activity Logging**: Spatie Laravel Activity Log v4.12.3
- **Excel Processing**: Maatwebsite Excel v3.1.69
- **QR Code Generation**: SimpleSoftwareIO Simple QR Code v4.2.0
- **Build Tool**: Vite 7.x
- **Testing**: PHPUnit 11.x
- **Container**: Docker (production deployment only)

### Key Components
- **Models**: Eloquent models with proper relationships and casting
- **Livewire Components**: Reactive UI components organized by feature
- **Seeders**: Database seeding with demo data and roles
- **Migrations**: Database schema management
- **Routes**: RESTful routing with middleware protection

## 🚀 **Development Setup**

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js & npm
- MySQL (for production) or SQLite (for development)
- Docker & Docker Compose (for production deployment)

### Local Development
```bash
# Clone repository
git clone <repository-url>
cd mould-tracking-system

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup (SQLite for development)
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Start development servers
php artisan serve
npm run dev
```

### Docker Production
```bash
# Start production containers
docker-compose up -d

# Initial setup
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

## 📝 **Coding Standards**

### PHP Code Style
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Use strict typing where possible
- Use meaningful variable and method names

### Laravel Conventions
- Use Eloquent relationships properly
- Implement proper validation in Form Requests
- Use Gates/Policies for authorization
- Follow RESTful routing conventions
- Use Livewire components for reactive UI

### Frontend Guidelines
- Use Tailwind CSS utility classes
- Follow component-based architecture with Livewire
- Use Alpine.js for lightweight JavaScript interactions
- Maintain responsive design principles

## 🧪 **Testing**

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Unit Tests**: Test individual components and methods
- **Feature Tests**: Test user interactions and workflows
- **Browser Tests**: Test JavaScript interactions (if applicable)

### Test Data
- Use Laravel factories for test data generation
- Create realistic test scenarios
- Test both positive and negative cases

## 🔄 **Database Management**

### Migrations
```bash
# Create new migration
php artisan make:migration create_new_table

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback
```

### Seeders
- Use descriptive seeder classes
- Include demo data for development
- Ensure seeders are idempotent

### Models
- Use proper Eloquent relationships
- Implement model casting for dates and enums
- Use accessors/mutators when needed
- Include proper validation rules

## 🔐 **Security Guidelines**

### Authentication & Authorization
- Use Laravel's built-in authentication
- Implement role-based access control with Spatie Permission
- Validate all user inputs
- Use CSRF protection for forms

### Data Protection
- Never store sensitive data in plain text
- Use Laravel's encryption for sensitive data
- Implement proper password hashing
- Sanitize user inputs to prevent XSS

### API Security
- Use proper authentication tokens
- Implement rate limiting
- Validate all API inputs
- Use HTTPS in production

## 📦 **Deployment**

### Production Checklist
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Application key generated
- [ ] Storage permissions set
- [ ] Caching configured
- [ ] SSL certificates installed
- [ ] Monitoring set up

### Docker Deployment
- Use production-optimized Dockerfile
- Configure proper environment variables
- Set up persistent volumes for data
- Use multi-stage builds for optimization
- Implement health checks

## 📋 **Pull Request Process**

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### PR Guidelines
- Provide clear description of changes
- Include screenshots for UI changes
- Reference related issues
- Ensure all tests pass
- Follow coding standards
- Update documentation if needed

## 🐛 **Bug Reports**

When reporting bugs, please include:
- Clear title and description
- Steps to reproduce
- Expected vs actual behavior
- Environment details (PHP version, OS, etc.)
- Screenshots or error logs
- Code snippets if applicable

## 💡 **Feature Requests**

For new features, please provide:
- Clear description of the feature
- Use case and benefits
- Mockups or wireframes if applicable
- Implementation suggestions
- Impact assessment

## 📚 **Resources**

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://laravel-livewire.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)
- [PSR Standards](https://www.php-fig.org/psr/)

## 🤝 **Code of Conduct**

- Be respectful and inclusive
- Provide constructive feedback
- Help fellow contributors
- Follow Laravel community guidelines
- Maintain professional communication

---

**Last Updated**: May 2026
**Version**: 1.0.0