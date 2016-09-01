<?php



// Application middleware
// e.g: app()->add(new \Slim\Csrf\Guard);
//

// Trailing slash
app()->add(Zank\Middleware\TrailingSlash::class);
