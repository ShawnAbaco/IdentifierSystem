// app/Http/Kernel.php

protected $routeMiddleware = [
    // ... other middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
