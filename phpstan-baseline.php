<?php

declare(strict_types=1);

$ignoreErrors = [];

$ignoreErrors[] = [
    // identifier: binaryOp.invalid
    'message' => '#^Binary operation "(\+|\*)" between .* and .* results in an error\.$#',
    'count' => 1,
    'path' => __DIR__ . '/app/Services/*.php',
];

$ignoreErrors[] = [
    // identifier: property.notFound
    'message' => '#^Access to an undefined property App\\\\.+?\\:\\:\\$pivot\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/*.php',
];

$ignoreErrors[] = [
    // identifier: property.nonObject
    'message' => '#^Cannot access property \\$(amount|quantity) on mixed\.$#',
    'count' => 5,
    'path' => __DIR__ . '/app/*.php',
];

$ignoreErrors[] = [
    // identifier: argument.type
    'message' => '#^Parameter \\#2 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<.+\\>\\:\\:with\\(\\) expects \\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation.+$#',
    'count' => 1,
    'path' => __DIR__ . '/app/*.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
