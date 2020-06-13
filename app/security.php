<?php

// enabling lazy cors
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $this->get('settings')['siteUrl'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// add security headers
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        //check mime type -> xss
        ->withHeader('X-Content-Type-Options', 'nosniff')
        //referer info
        ->withHeader('Referrer-Policy', 'no-referrer, strict-origin-when-cross-origin')
        //xss
        ->withHeader('Content-Security-Policy', $_ENV['PROD'] ? "default-src https: 'unsafe-inline'" : "default-src http: 'unsafe-inline'")
        //clickjacking
        ->withHeader('X-Frame-Options', 'DENY');
});