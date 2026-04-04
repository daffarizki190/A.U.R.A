<?php
echo "Running Feature Tests...\n";
$output = shell_exec('php artisan test --testsuite=Feature --filter=OutstandingDashboardTest --no-interaction 2>&1');
file_put_contents('test_results.txt', $output);
echo "Done.\n";
