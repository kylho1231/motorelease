<?php

// Forward Vercel serverless requests to Laravel
// Prepare /tmp storage for serverless execution
$storageDirs = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Database setup for Vercel serverless:
// Detect PostgreSQL or remote database credentials (Supabase, Neon, Railway, RDS, etc.)
$databaseUrl = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL') ?: getenv('DB_URL');
$hasRemoteDb = getenv('DB_HOST') || $databaseUrl;

if ($hasRemoteDb && getenv('DB_CONNECTION') !== 'sqlite') {
    // If a Postgres connection or URL is provided, ensure pgsql driver is active
    if (!getenv('DB_CONNECTION') || getenv('DB_CONNECTION') === 'pgsql') {
        if ($databaseUrl && (str_contains($databaseUrl, 'postgres') || str_contains($databaseUrl, 'pgsql'))) {
            putenv('DB_CONNECTION=pgsql');
            $_ENV['DB_CONNECTION'] = 'pgsql';
        }
    }
} else {
    // Fallback to standalone seeded SQLite for zero-config serverless demo
    $tmpDb = '/tmp/database.sqlite';
    $srcDb = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($tmpDb) && file_exists($srcDb)) {
        copy($srcDb, $tmpDb);
    }
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDb;
}

// Set environment overrides for serverless read-only filesystem
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';

require __DIR__ . '/../public/index.php';
