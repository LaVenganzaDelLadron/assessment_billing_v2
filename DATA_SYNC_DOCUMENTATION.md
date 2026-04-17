# Data Synchronization System Documentation

## Overview

A robust, production-ready data synchronization system that fetches programs and subjects from an external API and mirrors them into the local database. The external API is treated as the single source of truth.

## Architecture

### Components

1. **DataSyncService** (`app/Services/DataSyncService.php`)
   - Core sync logic with full CRUD operations
   - Handles diff checking and relationship synchronization
   - Transaction-based operations for consistency
   - Comprehensive error handling

2. **Models**
   - `Programs` - Updated with `code` and `status` fields
   - `Subjects` - Updated with `subject_code`, `type`, and `status` fields
   - Both support many-to-many relationship via `program_subject` pivot table

3. **Controllers**
   - `DataSyncController` - REST API endpoints for manual sync triggers

4. **Commands**
   - `SyncExternalDataCommand` - CLI command for manual sync execution

5. **Jobs**
   - `SyncExternalDataJob` - Queued job for background/scheduled sync

6. **Database**
   - Migrations for new fields and pivot table
   - Pivot table stores: `year_level`, `semester`, `school_year`, `status`

## Features

### ✅ Implemented Features

- **Full Synchronization**: Programs and subjects with relationships
- **Relationship Sync**: Many-to-many program-subject with pivot data
- **Idempotent Operations**: Safe to run repeatedly without side effects
- **Diff Checking**: Only updates records that have changed
- **Batch Operations**: Efficient bulk insert/update/delete
- **Unique Identifiers**: Uses `id` from API as unique key
- **Transaction Safety**: All-or-nothing operations
- **Duplicate Prevention**: Unique constraints on pivot table
- **Partial Failure Handling**: Logs errors without breaking entire sync
- **Read-Only API**: No data pushed back to API
- **Local Database Only**: Creates/updates/deletes only local records
- **Error Recovery**: Automatic rollback on exceptions
- **Logging**: Detailed logs for audit trail

## Usage

### 1. Manual Sync via CLI

```bash
# Trigger sync from command line
php artisan sync:external-data
```

Output shows detailed log with timestamps and operations performed:
```
🔄 Starting data synchronization...

✅ Data synchronization completed successfully

📋 Sync Log:
  ┌─────────────────────────────────────────┬──────────────────────┬──────────────────────┐
  │ Timestamp                               │ Message              │ Details              │
  ├─────────────────────────────────────────┼──────────────────────┼──────────────────────┤
  │ 2026-04-17T08:39:14+00:00               │ ✓ Programs synced    │ {"total": 5}         │
  │ 2026-04-17T08:39:15+00:00               │ ✓ Subjects synced    │ {"total": 2}         │
  └─────────────────────────────────────────┴──────────────────────┴──────────────────────┘

✨ Synchronization completed successfully!
```

### 2. Manual Sync via API

**Endpoint**: `POST /api/admin/sync`

**Authentication**: Bearer token (admin role required)

**Request**:
```bash
curl -X POST http://localhost:8000/api/admin/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

**Response**:
```json
{
  "data": {
    "status": "success",
    "message": "Data synchronization completed successfully",
    "logs": [
      {
        "timestamp": "2026-04-17T08:39:14.000000Z",
        "message": "✓ Programs synced",
        "context": {"total": 5}
      },
      {
        "timestamp": "2026-04-17T08:39:15.000000Z",
        "message": "✓ Subjects synced",
        "total": 2
      }
    ]
  },
  "message": "Data synchronization completed successfully",
  "status": "success"
}
```

### 3. Check Sync Status

**Endpoint**: `GET /api/admin/sync/status`

**Authentication**: Bearer token (admin role required)

**Response**:
```json
{
  "data": {
    "last_sync": {
      "timestamp": "2026-04-17T08:39:15.000000Z",
      "status": "success",
      "message": "Data synchronization completed successfully"
    },
    "sync_running": false,
    "logs": []
  },
  "message": "Sync status retrieved",
  "status": "success"
}
```

### 4. Automatic Scheduled Sync

The system automatically syncs every 12 hours. To enable Laravel's scheduler:

```bash
# Add to your crontab
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Or use your application's process manager (Supervisor, etc.) to run the scheduler.

**Current Schedule**: Every 12 hours (configurable in `app/Console/Kernel.php`)

## API Integration Details

### Endpoints

**Programs API**:
- URL: `https://registrarmodule1-production.up.railway.app/api/programs`
- Response Format:
```json
[
  {
    "id": 60,
    "code": "BSIT",
    "name": "Bachelor of Science in Information Technology",
    "department": "CCS",
    "status": "active",
    "created_at": "2026-04-16T19:57:46.000000Z",
    "updated_at": "2026-04-16T19:57:46.000000Z"
  }
]
```

**Subjects API**:
- URL: `https://registrarmodule1-production.up.railway.app/api/subjects`
- Response Format:
```json
[
  {
    "id": 17,
    "subject_code": "IT102",
    "subject_name": "Computer Programming 1",
    "units": 3,
    "type": "lecture",
    "status": "active",
    "created_at": "2026-04-16T20:22:26.000000Z",
    "updated_at": "2026-04-16T20:22:26.000000Z",
    "programs": [
      {
        "id": 60,
        "code": "BSIT",
        "name": "Bachelor of Science in Information Technology",
        "department": "CCS",
        "status": "active",
        "created_at": "2026-04-16T19:57:46.000000Z",
        "updated_at": "2026-04-16T19:57:46.000000Z",
        "pivot": {
          "subject_id": 17,
          "program_id": 60,
          "year_level": "1",
          "semester": "1st",
          "school_year": "2026-2027",
          "status": "active"
        }
      }
    ]
  }
]
```

## Sync Algorithm

### Step 1: Fetch Programs
1. Fetch all programs from API
2. Extract API IDs
3. For each program:
   - If exists locally → compare and update if changed
   - If not exists → insert new record
4. Delete any local programs not in API response

### Step 2: Fetch Subjects
1. Fetch all subjects from API
2. Extract API IDs
3. For each subject:
   - If exists locally → compare and update if changed
   - If not exists → insert new record
4. For each subject, sync program relationships:
   - Get existing local relationships
   - Insert new relationships from API
   - Update changed pivot data
   - Delete relationships not in API

### Step 3: Cleanup
- Delete local subjects not in API response
- All operations wrapped in database transaction

## Database Schema

### Programs Table (Updated Fields)
```sql
ALTER TABLE programs ADD COLUMN code VARCHAR(255) NULLABLE;
ALTER TABLE programs ADD COLUMN status VARCHAR(255) DEFAULT 'active';
```

### Subjects Table (Updated Fields)
```sql
ALTER TABLE subjects ADD COLUMN subject_code VARCHAR(255) NULLABLE;
ALTER TABLE subjects ADD COLUMN type VARCHAR(255) NULLABLE;
ALTER TABLE subjects ADD COLUMN status VARCHAR(255) DEFAULT 'active';
```

### Program-Subject Pivot Table (New)
```sql
CREATE TABLE program_subject (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    program_id BIGINT NOT NULL,
    subject_id BIGINT NOT NULL,
    year_level VARCHAR(255) NULLABLE,
    semester VARCHAR(255) NULLABLE,
    school_year VARCHAR(255) NULLABLE,
    status VARCHAR(255) DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_program_subject (program_id, subject_id)
);
```

## Error Handling

### Retry Logic
- API timeout: 30 seconds with 3 retries (100ms backoff)
- Job failures: Up to 3 retry attempts before marking as failed

### Failure Handling
- Partial failures: Continues processing, logs all errors
- Transaction rollback: Entire sync rolls back on unrecoverable error
- Dead letter logging: All failures logged to `storage/logs/laravel.log`

### Concurrency Control
- Cache-based lock prevents concurrent sync operations
- Lock timeout: 5 minutes
- Warning logged if sync attempted while running

## Logging

All sync activities logged to `storage/logs/laravel.log`:

```
[2026-04-17 08:39:14] local.INFO: Starting scheduled data synchronization
[2026-04-17 08:39:14] local.INFO: ✓ Programs synced {"total": 5}
[2026-04-17 08:39:14] local.INFO: Program created {"id": 60, "code": "BSIT"}
[2026-04-17 08:39:15] local.INFO: ✓ Subjects synced {"total": 2}
[2026-04-17 08:39:15] local.INFO: Subject created {"id": 17, "code": "IT102"}
[2026-04-17 08:39:15] local.INFO: Data synchronization completed {"status": "success", "message": "..."}
```

## Security

- ✅ Admin role required for API sync endpoints
- ✅ Transaction-based consistency
- ✅ No direct SQL execution (uses Eloquent ORM)
- ✅ Validated API responses
- ✅ Timeout and retry protection
- ✅ Read-only from external API

## Performance Optimizations

- **Batch Operations**: Efficient bulk insert/update/delete
- **Diff Checking**: Only updates changed records
- **Unique Constraints**: Prevents duplicate relationships
- **Eager Loading**: Loads relationships efficiently
- **Query Batching**: Uses Eloquent collections for memory efficiency

## Troubleshooting

### Sync Fails with "API returned status 500"
- Check external API health
- Verify network connectivity
- Check API rate limits

### Sync Completes but no data appears
- Verify database migration ran successfully: `php artisan migrate:status`
- Check user role is 'admin'
- Review logs: `tail -f storage/logs/laravel.log`

### Sync takes too long
- Check database performance
- Verify API response time
- Monitor server resources

### Sync doesn't run automatically
- Verify Laravel scheduler is registered in cron: `crontab -l`
- Check if queue worker is running (if using queue)
- Review scheduler logs

## Configuration

### Modify Sync Schedule

Edit `app/Console/Kernel.php`:

```php
// Change to daily at 2 AM
$schedule->job(new SyncExternalDataJob())
    ->dailyAt('02:00')
    ->name('sync-external-data');

// Change to hourly
$schedule->job(new SyncExternalDataJob())
    ->hourly()
    ->name('sync-external-data');
```

### Change API Base URL

Edit `app/Services/DataSyncService.php`:

```php
private const API_BASE_URL = 'https://new-api-url.com/api';
```

## API Response Examples

### Successful Sync
```json
{
  "data": {
    "status": "success",
    "message": "Data synchronization completed successfully",
    "logs": [
      {
        "timestamp": "2026-04-17T08:39:14Z",
        "message": "✓ Programs synced",
        "context": {"total": 5}
      }
    ]
  },
  "message": "Data synchronization completed successfully",
  "status": "success"
}
```

### Failed Sync (Partial Error)
```json
{
  "data": {
    "status": "error",
    "message": "Data synchronization encountered errors",
    "logs": [
      {
        "timestamp": "2026-04-17T08:39:14Z",
        "message": "✗ Subjects sync failed",
        "context": {"error": "Connection timeout"}
      }
    ]
  },
  "message": "Data synchronization encountered errors",
  "status": "error"
}
```

## Testing

Run a manual sync to test:

```bash
# CLI test
php artisan sync:external-data

# API test with curl
curl -X POST http://localhost:8000/api/admin/sync \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"
```

Verify data in database:

```bash
# Check programs
php artisan tinker
>>> App\Models\Programs::count()
>>> App\Models\Programs::with('subjects')->first()

# Check subjects
>>> App\Models\Subjects::count()
>>> App\Models\Subjects::with('programs')->first()

# Check relationships
>>> App\Models\Programs::find(60)->subjects()->get()
```
