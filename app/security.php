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
        //xss: 42 + local
        ->withHeader('Content-Security-Policy', "default-src 'self';img-src data: meta.intra.42.fr localhost:8080 res.cloudinary.com matcha2.herokuapp.com maps.gstatic.com *.googleapis.com *.apis.google.com")
        //clickjacking
        ->withHeader('X-Frame-Options', 'DENY');
});