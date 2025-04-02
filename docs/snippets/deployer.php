<?php

declare(strict_types=1);

task('deploy', [
    // ...
    'artisan:migrate',
    'artisan:operation --before', // here
    'deploy:publish',
    'php-fpm:reload',
    'artisan:queue:restart',
    'artisan:operations', // here
]);
