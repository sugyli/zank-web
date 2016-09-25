<?php

// Application middleware
// e.g: app()->add(new \Slim\Csrf\Guard);
//

// Trailing slash
\Zank\Application::add(Zank\Middleware\TrailingSlash::class);
