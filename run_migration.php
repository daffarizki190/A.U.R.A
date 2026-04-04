<?php
echo "Starting migration...\n";
$output = shell_exec('php artisan migrate:status --no-interaction 2>&1');
file_put_contents('migration_log.txt', "STATUS:\n" . $output . "\n\n");

echo "Running migrate --seed...\n";
$output = shell_exec('php artisan migrate --seed --no-interaction 2>&1');
file_put_contents('migration_log.txt', "MIGRATE:\n" . $output, FILE_APPEND);
echo "Done.\n";
