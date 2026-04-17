# Data Sync System - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Laravel 13.5+
- PostgreSQL/MySQL
- PHP 8.4+

### What Was Created

```
app/
├── Services/
│   └── DataSyncService.php          # Core sync logic
├── Http/Controllers/
│   └── DataSyncController.php       # REST API endpoints
├── Console/Commands/
│   └── SyncExternalDataCommand.php  # CLI command
├── Jobs/
│   └── SyncExternalDataJob.php      # Background job
└── Models/
    ├── Programs.php                  # Updated with relationships
    └── Subjects.php                  # Updated with relationships

database/migrations/
├── 2026_04_17_083846_*.php          # Add fields to programs/subjects
└── 2026_04_17_083852_*.php          # Create pivot table

routes/api.php                        # Added sync routes
config/                              # No changes needed
```

## ⚡ Quick Usage

### Option 1: CLI Command (Simplest)
```bash
php artisan sync:external-data
```

### Option 2: REST API
```bash
curl -X POST http://localhost:8000/api/admin/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Option 3: Programmatic
```php
use App\Services\DataSyncService;

$syncService = app(DataSyncService::class);
$result = $syncService->syncAll();

echo $result['message']; // "Data synchronization completed successfully"
```

### Option 4: Automatic (Scheduled)
Runs automatically every 12 hours if scheduler is configured:
```bash
# Add to crontab
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📋 What Gets Synced

### Programs
- **From API**: id, code, name, department, status
- **To DB**: 5 programs from BSIT, BSBA, BSHM, BSED, BEED
- **Action**: Insert if new, update if changed, delete if removed from API

### Subjects
- **From API**: id, subject_code, subject_name, units, type, status
- **To DB**: All subjects with their relationships to programs
- **Action**: Insert if new, update if changed, delete if removed from API

### Relationships
- **Type**: Many-to-many (program ↔ subject)
- **Pivot Fields**: year_level, semester, school_year, status
- **Action**: Sync all relationships from API

## 🔍 Verify It Works

After running sync, check the data:

```bash
# In terminal
php artisan tinker

# Inside Tinker
>>> App\Models\Programs::count()  # Should show 5
>>> App\Models\Subjects::count()  # Should show 2+
>>> App\Models\Programs::find(60)->subjects # Get subjects for BSIT
```

Or query via API:
```bash
curl -X GET http://localhost:8000/api/admin/programs \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📊 Data Flow

```
External API
    ↓ (Read-only)
DataSyncService
    ↓ (Fetches & compares)
Local Database
    ├── Programs table
    ├── Subjects table
    └── program_subject pivot table
```

## 🛠️ Configuration

### Change Sync Schedule
Edit `app/Console/Kernel.php` in the `schedule()` method:

```php
// Daily at 2 AM
$schedule->job(new SyncExternalDataJob())->dailyAt('02:00');

// Hourly
$schedule->job(new SyncExternalDataJob())->hourly();

// Every 6 hours
$schedule->job(new SyncExternalDataJob())->everyThreeHours();
```

### Change API URL
Edit `app/Services/DataSyncService.php`:

```php
private const API_BASE_URL = 'https://your-api.com/api';
```

## 📝 API Response Format

### Success
```json
{
  "status": "success",
  "message": "Data synchronization completed successfully",
  "logs": [
    {"timestamp": "2026-04-17T08:39:14Z", "message": "✓ Programs synced", "context": {"total": 5}}
  ]
}
```

### Error
```json
{
  "status": "error",
  "message": "Data synchronization failed: Connection timeout",
  "logs": [{"timestamp": "2026-04-17T08:39:14Z", "message": "✗ Programs sync failed", "context": {"error": "..."}}]
}
```

## 🔐 Security Notes

- ✅ Admin role required for API sync
- ✅ API is read-only (no data sent back)
- ✅ All operations wrapped in transactions
- ✅ Retry logic protects against transient failures
- ✅ Concurrent sync prevented via cache lock

## 📊 Monitoring

### Check Last Sync
```bash
curl -X GET http://localhost:8000/api/admin/sync/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Review Logs
```bash
# Last 20 sync log entries
tail -20 storage/logs/laravel.log | grep -i sync

# Full log file
tail -f storage/logs/laravel.log
```

### Debug Mode
Enable more detailed logging:

```bash
# In .env
APP_DEBUG=true
LOG_LEVEL=debug
```

## ❓ Common Issues

| Issue | Solution |
|-------|----------|
| "API returned status 500" | Check external API health |
| No data after sync | Verify migrations ran: `php artisan migrate:status` |
| Sync takes too long | Check database performance, API response time |
| Sync doesn't auto-run | Verify scheduler in cron: `crontab -e` |
| "Forbidden - Admin access required" | Use admin user token |

## 📚 Full Documentation

See [DATA_SYNC_DOCUMENTATION.md](./DATA_SYNC_DOCUMENTATION.md) for complete details.

## 🎯 Key Features

✅ **Read-only from API** - Only local DB changes  
✅ **Idempotent** - Safe to run repeatedly  
✅ **Transactional** - All-or-nothing operations  
✅ **Efficient** - Only updates changed records  
✅ **Reliable** - Retry logic & error handling  
✅ **Auditable** - Detailed logging  
✅ **Concurrent** - Prevents duplicate runs  
✅ **Flexible** - CLI, API, or scheduled  

---

**Need help?** Check DATA_SYNC_DOCUMENTATION.md or review the implementation in:
- `app/Services/DataSyncService.php` - Core logic
- `app/Console/Commands/SyncExternalDataCommand.php` - CLI implementation
- `app/Http/Controllers/DataSyncController.php` - API endpoints
