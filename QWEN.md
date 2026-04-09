# SKNET CRM - Laravel Application

## Project Overview

This is a Laravel-based Customer Relationship Management (CRM) system designed for managing customers, subscriptions, and network access servers (NAS) for an internet service provider. The application is built on Laravel 12 with Jetstream, Livewire, and Tailwind CSS for the frontend.

### Key Technologies & Features

- **Framework**: Laravel 12
- **Authentication**: Laravel Jetstream with Fortify
- **Frontend Framework**: Livewire 3.6+
- **Styling**: Tailwind CSS with custom gold/dark theme
- **Database**: SQLite (default) with support for MySQL
- **Task Queue**: Database-driven queue system
- **Permissions**: Spatie Laravel Permission package
- **Activity Logging**: Spatie Activity Log package
- **PDF Generation**: Laravel DOMPDF
- **Excel Export**: PhpSpreadsheet via Maatwebsite Excel
- **MikroTik Integration**: RouterOS API PHP client
- **SSH Operations**: phpseclib for secure connections
- **Caching**: Redis support via Predis

### Architecture

The application follows Laravel's MVC pattern with additional service layers:

- **Models**: Located in `app/Models/` with observers in `app/Observers/`
- **Controllers**: Located in `app/Http/Controllers/`
- **Livewire Components**: Located in `app/Livewire/`
- **Services**: Business logic in `app/Services/`
- **Actions**: Reusable business operations in `app/Actions/`
- **Jobs**: Queued operations in `app/Jobs/`

## Building and Running

### Prerequisites

- PHP 8.2+
- Composer
- Node.js and npm
- SQLite (or MySQL/MariaDB)
- Redis (for caching and queues)

### Setup Instructions

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**:
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate --force
   ```

4. **Build Assets**:
   ```bash
   npm run build
   ```

5. **Alternative Setup Script**:
   The project includes a convenient setup script:
   ```bash
   composer run setup
   ```

### Development Mode

Run the application in development mode with hot reloading:

```bash
composer run dev
```

This command starts:
- Laravel development server
- Queue listener
- Pail log viewer
- Vite dev server

### Testing

Run the test suite:

```bash
composer run test
# or
php artisan test
```

## Development Conventions

### Coding Standards

- Follow PSR-12 coding standards
- Use Laravel's built-in helpers and conventions
- Leverage Eloquent ORM for database operations
- Use Laravel's validation and form request features
- Implement proper error handling and logging

### Frontend Conventions

- Tailwind CSS utility-first approach
- Blade templates with Livewire components
- Responsive design with mobile-first approach
- Custom gold/dark theme implementation
- SweetAlert2 for user notifications

### Security Practices

- Input validation and sanitization
- Authorization using Laravel's gate/permission system
- CSRF protection for forms
- Secure session management
- Proper password hashing with bcrypt

### Key Features

1. **Customer Management**: Full CRUD operations for customer records
2. **Subscription Management**: Handle recurring payments and service plans
3. **Network Access Server (NAS) Integration**: Connect with MikroTik routers
4. **Bandwidth Profiles**: Manage speed and data limits
5. **Activity Logging**: Track all user actions
6. **Role-Based Permissions**: Fine-grained access control
7. **Export Capabilities**: Generate reports in Excel and PDF formats
8. **Queue Processing**: Background jobs for heavy operations

### Directory Structure

```
app/                    # Main application code
├── Actions/           # Reusable business operations
├── Http/              # HTTP controllers and middleware
├── Jobs/              # Queueable jobs
├── Livewire/          # Livewire components
├── Models/            # Eloquent models
├── Observers/         # Model observers
├── Providers/         # Service providers
├── Services/          # Business logic services
└── View/              # View composers/helpers
config/                # Laravel configuration files
database/              # Migrations, seeds, and factories
public/                # Web root with assets
resources/             # Views, CSS, JS, and other assets
routes/                # Application routes
storage/               # Compiled templates, cache, and logs
tests/                 # PHPUnit tests
```

### Environment Variables

The application uses several environment variables for configuration:

- Database connection settings
- Mail configuration
- Redis connection
- AWS credentials (optional)
- Application URL and debugging settings

## Specialized Components

### RouterOS API Integration

The application integrates with MikroTik routers using the `evilfreelancer/routeros-api-php` package to manage network configurations and customer connections.

### SSH Operations

Secure shell operations are handled using `phpseclib/phpseclib` for remote server management tasks.

### Queue System

Background jobs are processed using Laravel's queue system, configured to use the database driver by default but supporting Redis for production environments.

### Activity Logging

All user actions are logged using the `spatie/laravel-activitylog` package, providing audit trails for compliance and debugging.