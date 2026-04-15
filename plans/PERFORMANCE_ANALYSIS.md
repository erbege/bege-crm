# Performance Analysis & Optimization Recommendations

## Executive Summary

This document outlines identified performance issues in the SKNET CRM Laravel application and provides actionable recommendations to address them. The analysis covers database, query optimization, caching, queue processing, and architectural considerations.

---

## 1. Database Configuration Issues

### Issue 1.1: Using SQLite for Development/Production

**Location:** [`config/database.php`](config/database.php:19)

```php
'default' => env('DB_CONNECTION', 'sqlite'),
```

**Problem:**

- SQLite is configured as the default database connection
- SQLite has limitations for concurrent writes and complex queries
- Not suitable for production ISP management systems

**Recommendation:**

- Switch to MySQL or MariaDB for production
- Configure proper connection pooling

---

### Issue 1.2: Cache Driver Using Database

**Location:** [`config/cache.php`](config/cache.php:18)

```php
'default' => env('CACHE_STORE', 'database'),
```

**Problem:**

- Database cache driver adds extra load to the main database
- Not suitable for high-performance applications

**Recommendation:**

- Use Redis or Memcached for caching
- Configure `CACHE_STORE=redis` in `.env`

---

### Issue 1.3: Queue Using Database Driver

**Location:** [`config/queue.php`](config/queue.php:16)

```php
'default' => env('QUEUE_CONNECTION', 'database'),
```

**Problem:**

- Database queue is slow for heavy jobs (Radius sync, OLT provisioning)
- Jobs run synchronously adding latency

**Recommendation:**

- Use Redis for queue backend
- Configure `QUEUE_CONNECTION=redis` in `.env`

---

## 2. N+1 Query Issues

### Issue 2.1: CustomerManager Missing Eager Loading

**Location:** [`app/Livewire/Customer/CustomerManager.php`](app/Livewire/Customer/CustomerManager.php:84-102)

**Problem:**

```php
$customers = Customer::query()
    ->with(['area', 'activeSubscription.package', 'activeSubscription.coveragePoint'])
```

**Observation:**

- Eager loading is implemented correctly here ✓
- However, the `activeSubscription` uses `latestOfMany()` which may cause additional queries

**Recommendation:**

- Consider caching `status` attribute computation
- Add `select()` to limit columns fetched

---

### Issue 2.2: Customer Model - Missing Indexes

**Location:** [`app/Models/Customer.php`](app/Models/Customer.php)

**Problem:**

- No database indexes on frequently queried columns:
    - `phone` - used in search
    - `name` - used in search
    - `customer_id` - unique but should be verified as indexed
    - `status` - computed column, needs separate index

**Recommendation:**
Add migrations for missing indexes:

```php
// Add to migration or create new migration
Schema::table('customers', function (Blueprint $table) {
    $table->index('phone');
    $table->index(['name']);
    $table->index('registered_at');
});
```

---

### Issue 2.3: Subscription Model - Missing Indexes

**Location:** [`database/migrations/2026_02_03_142500_create_subscriptions_table.php`](database/migrations/2026_02_03_142500_create_subscriptions_table.php:35-37)

**Current:**

```php
$table->index(['customer_id', 'period_start']);
$table->index('status');
```

**Missing Indexes Needed:**

```php
// For active subscription queries
$table->index(['customer_id', 'status']);

// For period-based queries
$table->index(['period_end', 'status']);

// For PPPoE username lookups
$table->index('pppoe_username');
```

---

### Issue 2.4: CoveragePoint Model - Missing Indexes

**Location:** [`app/Models/CoveragePoint.php`](app/Models/CoveragePoint.php)

**Problem:**

- No indexes on `area_id`, `type`, `is_active` combination
- Used frequently in filtering

**Recommendation:**

```php
Schema::table('coverage_points', function (Blueprint $table) {
    $table->index(['area_id', 'type', 'is_active']);
});
```

---

## 3. Livewire Component Performance Issues

### Issue 3.1: CustomerManager - Multiple Queries on Mount

**Location:** [`app/Livewire/Customer/CustomerManager.php`](app/Livewire/Customer/CustomerManager.php:76-79)

```php
public function mount()
{
    $this->provinces = Area::where('type', 'province')->active()->orderBy('name')->get();
}
```

**Problem:**

- Queries all provinces on every component mount
- These are reference data that rarely change

**Recommendation:**

- Cache provinces using `Cache::remember()`
- Consider loading on-demand with wire:init

---

### Issue 3.2: SubscriptionManager - Cascading Selects

**Location:** [`app/Livewire/Subscription/SubscriptionManager.php`](app/Livewire/Subscription/SubscriptionManager.php:113-131)

**Problem:**

- Each `updated*` method queries the database
- Multiple round-trips when user selects province → city → district → village

**Recommendation:**

- Load all areas once and filter client-side
- Use Livewire's `select` population strategies

```php
// Alternative: Load all and filter in JavaScript
public function mount()
{
    $this->areas = Area::with('children')->whereNull('parent_id')->get();
}
```

---

### Issue 3.3: InvoiceManager - Complex Query

**Location:** [`app/Livewire/Invoice/InvoiceManager.php`](app/Livewire/Invoice/InvoiceManager.php:53-73)

```php
$invoices = Invoice::query()
    ->with(['customer', 'subscription.package'])
    ->when($this->search, function ($q) {
        $q->where(function ($sub) {
            $sub->where('invoice_number', 'like', '%' . $this->search . '%')
                ->orWhereHas('customer', function ($c) {
                    $c->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_id', 'like', '%' . $this->search . '%');
                });
        });
    })
```

**Problem:**

- `LIKE %%` queries without indexes are slow on large datasets
- `orWhereHas` with LIKE can be very slow

**Recommendation:**

- Add database indexes on `invoice_number`, `customer.name`
- Consider full-text search for customer names
- Add debouncing to search input

---

## 4. Model Attribute Access Issues

### Issue 4.1: Customer Status Attribute - N+1 Potential

**Location:** [`app/Models/Customer.php`](app/Livewire/Customer/CustomerManager.php:76-98)

```php
public function getStatusAttribute(): string
{
    $latest = $this->activeSubscription;
    // ... status logic
}
```

**Problem:**

- `activeSubscription` is accessed for each customer
- May cause N+1 if not eager loaded

**Recommendation:**

- Already using eager loading in CustomerManager ✓
- Add to global query scopes if needed

---

### Issue 4.2: CoveragePoint Available Ports

**Location:** [`app/Models/CoveragePoint.php`](app/Models/CoveragePoint.php:51-57)

```php
public function getAvailablePortsAttribute(): ?int
{
    if ($this->capacity === null) {
        return null;
    }
    return max(0, $this->capacity - $this->used_ports);
}
```

**Observation:**

- Computed attribute is efficient ✓
- `used_ports` is denormalized for performance ✓

---

## 5. Queue Job Performance Issues

### Issue 5.1: SyncToRadiusJob

**Location:** [`app/Jobs/SyncToRadiusJob.php`](app/Jobs/SyncToRadiusJob.php:32-39)

```php
public function handle(RadiusSyncService $radiusService): void
{
    $subscription = Subscription::with(['package.bwProfile', 'nas'])->find($this->subscriptionId);
    if ($subscription) {
        $radiusService->sync($subscription);
    }
}
```

**Problem:**

- No timeout configuration
- No retry limit specified
- Job runs sequentially

**Recommendation:**

```php
class SyncToRadiusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $maxExceptions = 3;
    public $timeout = 30;
    public $backoff = 30;
}
```

---

### Issue 5.2: RadiusSyncService - Multiple Database Operations

**Location:** [`app/Services/RadiusSyncService.php`](app/Services/RadiusSyncService.php)

**Problem:**

- Multiple DELETE/INSERT operations in transaction
- Each operation is a separate query
- No bulk operations

**Recommendation:**

- Use `upsert()` for bulk inserts
- Batch operations when syncing multiple subscriptions

---

## 6. Observer Performance Issues

### Issue 6.1: SubscriptionObserver - updateUsedPorts() Call

**Location:** [`app/Models/Subscription.php`](app/Models/Subscription.php:43-73)

```php
static::updated(function ($subscription) {
    if ($subscription->isDirty('coverage_point_id') || $subscription->isDirty('status')) {
        if ($subscription->coverage_point_id) {
            $subscription->coveragePoint?->updateUsedPorts();
        }
        // ...
    }
});
```

**Problem:**

- `updateUsedPorts()` likely does a COUNT query each time
- Running during every subscription update

**Recommendation:**

- Consider caching `used_ports` with TTL
- Use database triggers for automatic updates
- Batch coverage point updates

---

## 7. Caching Strategy Issues

### Issue 7.1: Settings Loaded on Every Request

**Location:** [`app/Livewire/Customer/CustomerManager.php`](app/Livewire/Customer/CustomerManager.php:104-108)

```php
return view('livewire.customer.customer-manager', [
    'customers' => $customers,
    'mapLat' => Setting::get('general.map_latitude', -6.200000),
    'mapLng' => Setting::get('general.map_longitude', 106.816666),
    'mapZoom' => Setting::get('general.map_zoom', 13),
]);
```

**Problem:**

- `Setting::get()` may query database each time
- Called in every render

**Recommendation:**

- Cache settings globally
- Use config files for static settings

```php
// In AppServiceProvider boot()
config([
    'general.map_latitude' => Setting::get('general.map_latitude', -6.200000),
    // ...
]);
```

---

## 8. Missing Performance Optimizations

### 8.1: No Query Logging in Production

**Problem:**

- No query count monitoring
- Difficult to identify slow queries

**Recommendation:**

- Use Laravel Debugbar in development
- Configure query log for slow queries in production

---

### 8.2: No Database Connection Pooling

**Problem:**

- New connection created for each request
- Latency added for TCP handshake

**Recommendation:**

- Configure persistent connections in MySQL
- Use RDS Proxy or similar for AWS deployments

---

### 8.3: Missing Database Foreign Key Indexes

**Problem:**

- Some foreign keys may not have indexes

**Recommendation:**

- Verify all FK columns have indexes:
    - `customers.area_id`
    - `subscriptions.customer_id`
    - `subscriptions.package_id`
    - `invoices.subscription_id`

---

## 9. Priority Recommendations Matrix

| Priority | Issue                        | Impact   | Effort |
| -------- | ---------------------------- | -------- | ------ |
| **P0**   | Switch to MySQL/PostgreSQL   | Critical | Medium |
| **P0**   | Use Redis for cache/queue    | High     | Low    |
| **P1**   | Add missing database indexes | High     | Low    |
| **P1**   | Configure job timeouts/retry | High     | Low    |
| **P2**   | Implement setting cache      | Medium   | Low    |
| **P2**   | Add debouncing to search     | Medium   | Low    |
| **P3**   | Optimize cascading selects   | Low      | Medium |
| **P3**   | Batch Radius sync operations | Low      | Medium |

---

## 10. Immediate Actions Required

### Configuration Changes (.env)

```env
DB_CONNECTION=mysql
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### New Migration for Indexes

```php
// database/migrations/xxxx_xx_xx_add_performance_indexes.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Customers
        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone');
            $table->index(['name', 'customer_id']);
            $table->index('registered_at');
        });

        // Subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['customer_id', 'status']);
            $table->index(['period_end', 'status']);
            $table->index('pppoe_username');
        });

        // Coverage Points
        Schema::table('coverage_points', function (Blueprint $table) {
            $table->index(['area_id', 'type', 'is_active']);
        });

        // Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['issue_date', 'status']);
            $table->index('invoice_number');
        });
    }
};
```

---

## Conclusion

The SKNET CRM application has several performance bottlenecks that should be addressed, particularly around:

1. **Database infrastructure** - Moving from SQLite to MySQL
2. **Caching layer** - Implementing Redis
3. **Query optimization** - Adding missing indexes
4. **Queue configuration** - Using Redis backend
5. **Job resilience** - Adding timeouts and retry policies

Implementing these changes will significantly improve the application's performance, especially under load with multiple concurrent users and high-frequency operations like Radius synchronization.
